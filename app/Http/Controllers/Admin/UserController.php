<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $editId = $request->query('edit');
        $mode = $request->query('mode');

        $roles = ['user', 'premium', 'admin'];
        $plans = ['free', 'premium'];

        $query = User::query()->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                if (ctype_digit($search)) {
                    $builder->where('id', (int) $search)
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                } else {
                    $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                }
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $selectedUser = null;

        if (is_numeric($editId)) {
            $selectedUser = User::query()->whereKey((int) $editId)->first();
        }

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'roles' => $roles,
            'plans' => $plans,
            'selectedUser' => $selectedUser,
            'mode' => $mode,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['user', 'premium', 'admin'])],
            'plan' => ['required', Rule::in(['free', 'premium'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->plan = $validated['plan'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('admin.users', ['q' => $request->input('q')])
            ->with('user-status', 'Usuario creado.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'premium', 'admin'])],
            'plan' => ['required', Rule::in(['free', 'premium'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $authUser = $request->user();
        if ($authUser && $authUser->id === $user->id && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'No puedes quitar tu rol de admin.'])->withInput();
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->plan = $validated['plan'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users', ['q' => $request->input('q')])
            ->with('user-status', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser && $authUser->id === $user->id) {
            return back()->withErrors(['delete' => 'No puedes borrar tu propio usuario.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users', ['q' => $request->input('q')])
            ->with('user-status', 'Usuario eliminado.');
    }
}
