<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FinalBossStoreRequest;
use App\Http\Requests\Admin\FinalBossUpdateRequest;
use App\Models\FinalBoss;

class FinalBossController extends Controller
{
    public function index()
    {
        $bosses = FinalBoss::query()
            ->orderBy('name')
            ->paginate(15);

        return view('admin.final-bosses.index', [
            'bosses' => $bosses,
        ]);
    }

    public function create()
    {
        return view('admin.final-bosses.create');
    }

    public function store(FinalBossStoreRequest $request)
    {
        $boss = FinalBoss::create($request->validated());

        return redirect()
            ->route('final-bosses.edit', $boss)
            ->with('status', 'Boss creado correctamente.');
    }

    public function edit(FinalBoss $finalBoss)
    {
        return view('admin.final-bosses.edit', [
            'boss' => $finalBoss,
        ]);
    }

    public function update(FinalBossUpdateRequest $request, FinalBoss $finalBoss)
    {
        $finalBoss->update($request->validated());

        return redirect()
            ->route('final-bosses.edit', $finalBoss)
            ->with('status', 'Boss actualizado correctamente.');
    }

    public function destroy(FinalBoss $finalBoss)
    {
        $finalBoss->delete();

        return redirect()
            ->route('final-bosses.index')
            ->with('status', 'Boss eliminado correctamente.');
    }
}
