@extends('layouts.game.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row g-2 h-100">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h1 class="h5 mb-0">Usuarios</h1>
                    <div class="small text-secondary">Busca por ID, nombre o correo y gestiona usuarios.</div>
                </div>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-light btn-sm">Volver al panel</a>
            </div>
        </div>

        <div class="col-12">
            @if (session('user-status'))
                <div class="alert alert-success small mb-0">{{ session('user-status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger small mb-0">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-12 d-lg-none">
            <div class="card bg-zinc-900 border-secondary text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-1">Usuarios</div>
                <div class="card-body d-flex flex-column gap-2 p-2">
                    <form method="GET" action="{{ route('admin.users') }}" class="d-flex flex-column gap-2">
                        <input name="q" class="form-control form-control-sm" placeholder="Buscar por ID, nombre o correo" value="{{ $search }}">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">Buscar</button>
                            <a class="btn btn-outline-light btn-sm w-100" href="{{ route('admin.users', ['q' => $search, 'mode' => 'create']) }}">Crear</a>
                        </div>
                    </form>

                    @if ($mode === 'create')
                        <form method="POST" action="{{ route('admin.users.store') }}" class="d-grid gap-1">
                            @csrf
                            <div>
                                <label class="form-label" for="m_user_name">Nombre</label>
                                <input id="m_user_name" name="name" class="form-control form-control-sm" value="{{ old('name') }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="m_user_email">Correo</label>
                                <input id="m_user_email" name="email" type="email" class="form-control form-control-sm" value="{{ old('email') }}" required>
                            </div>
                            <div class="row g-1">
                                <div class="col-6">
                                    <label class="form-label" for="m_user_role">Rol</label>
                                    <select id="m_user_role" name="role" class="form-select form-select-sm" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected(old('role', 'user') === $role)>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="m_user_plan">Plan</label>
                                    <select id="m_user_plan" name="plan" class="form-select form-select-sm" required>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan }}" @selected(old('plan', 'free') === $plan)>{{ $plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="m_user_password">Password</label>
                                <input id="m_user_password" name="password" type="password" class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label" for="m_user_password_confirmation">Confirmar password</label>
                                <input id="m_user_password_confirmation" name="password_confirmation" type="password" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">Crear</button>
                        </form>
                    @elseif ($mode === 'edit' && $selectedUser)
                        <form method="POST" action="{{ route('admin.users.update', $selectedUser) }}" class="d-grid gap-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="q" value="{{ $search }}">
                            <div>
                                <label class="form-label small">ID</label>
                                <input class="form-control form-control-sm" value="{{ $selectedUser->id }}" disabled>
                            </div>
                            <div>
                                <label class="form-label" for="m_edit_name">Nombre</label>
                                <input id="m_edit_name" name="name" class="form-control form-control-sm" value="{{ old('name', $selectedUser->name) }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="m_edit_email">Correo</label>
                                <input id="m_edit_email" name="email" type="email" class="form-control form-control-sm" value="{{ old('email', $selectedUser->email) }}" required>
                            </div>
                            <div class="row g-1">
                                <div class="col-6">
                                    <label class="form-label" for="m_edit_role">Rol</label>
                                    <select id="m_edit_role" name="role" class="form-select form-select-sm" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected(old('role', $selectedUser->role) === $role)>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="m_edit_plan">Plan</label>
                                    <select id="m_edit_plan" name="plan" class="form-select form-select-sm" required>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan }}" @selected(old('plan', $selectedUser->plan) === $plan)>{{ $plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="m_edit_password">Nueva password</label>
                                <input id="m_edit_password" name="password" type="password" class="form-control form-control-sm" placeholder="Opcional">
                            </div>
                            <div>
                                <label class="form-label" for="m_edit_password_confirmation">Confirmar password</label>
                                <input id="m_edit_password_confirmation" name="password_confirmation" type="password" class="form-control form-control-sm" placeholder="Opcional">
                            </div>
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">Actualizar</button>
                        </form>
                    @else
                        @if ($users->count() === 0)
                            <div class="small text-secondary">No hay usuarios para mostrar.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-dark table-sm align-middle mb-0 small">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td class="text-end">
                                                    <a class="btn btn-outline-info btn-sm" href="{{ route('admin.users', ['q' => $search, 'page' => $users->currentPage(), 'mode' => 'edit', 'edit' => $user->id]) }}">
                                                        Editar
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres borrar este usuario?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="q" value="{{ $search }}">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Borrar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-2">
                                {{ $users->links('pagination::simple-bootstrap-5') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 d-none d-lg-flex flex-column gap-2">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-1">
                    {{ $mode === 'create' ? 'Crear usuario' : 'Editar usuario' }}
                </div>
                <div class="card-body d-flex flex-column gap-1 p-2">
                    @if ($mode === 'create')
                        <form method="POST" action="{{ route('admin.users.store') }}" class="d-grid gap-1">
                            @csrf
                            <div>
                                <label class="form-label" for="user_name">Nombre</label>
                                <input id="user_name" name="name" class="form-control form-control-sm" value="{{ old('name') }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="user_email">Correo</label>
                                <input id="user_email" name="email" type="email" class="form-control form-control-sm" value="{{ old('email') }}" required>
                            </div>
                            <div class="row g-1">
                                <div class="col-6">
                                    <label class="form-label" for="user_role">Rol</label>
                                    <select id="user_role" name="role" class="form-select form-select-sm" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected(old('role', 'user') === $role)>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="user_plan">Plan</label>
                                    <select id="user_plan" name="plan" class="form-select form-select-sm" required>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan }}" @selected(old('plan', 'free') === $plan)>{{ $plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="user_password">Password</label>
                                <input id="user_password" name="password" type="password" class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label" for="user_password_confirmation">Confirmar password</label>
                                <input id="user_password_confirmation" name="password_confirmation" type="password" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">Crear</button>
                        </form>
                    @elseif ($mode === 'edit' && $selectedUser)
                        <form method="POST" action="{{ route('admin.users.update', $selectedUser) }}" class="d-grid gap-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="q" value="{{ $search }}">
                            <div>
                                <label class="form-label small">ID</label>
                                <input class="form-control form-control-sm" value="{{ $selectedUser->id }}" disabled>
                            </div>
                            <div>
                                <label class="form-label" for="edit_name">Nombre</label>
                                <input id="edit_name" name="name" class="form-control form-control-sm" value="{{ old('name', $selectedUser->name) }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="edit_email">Correo</label>
                                <input id="edit_email" name="email" type="email" class="form-control form-control-sm" value="{{ old('email', $selectedUser->email) }}" required>
                            </div>
                            <div class="row g-1">
                                <div class="col-6">
                                    <label class="form-label" for="edit_role">Rol</label>
                                    <select id="edit_role" name="role" class="form-select form-select-sm" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected(old('role', $selectedUser->role) === $role)>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="edit_plan">Plan</label>
                                    <select id="edit_plan" name="plan" class="form-select form-select-sm" required>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan }}" @selected(old('plan', $selectedUser->plan) === $plan)>{{ $plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="edit_password">Nueva password</label>
                                <input id="edit_password" name="password" type="password" class="form-control form-control-sm" placeholder="Opcional">
                            </div>
                            <div>
                                <label class="form-label" for="edit_password_confirmation">Confirmar password</label>
                                <input id="edit_password_confirmation" name="password_confirmation" type="password" class="form-control form-control-sm" placeholder="Opcional">
                            </div>
                            <button type="submit" class="btn btn-outline-info btn-sm w-100">Actualizar</button>
                        </form>
                    @else
                        <div class="small text-secondary">Selecciona crear o edita un usuario de la lista.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8 d-none d-lg-flex">
            <div class="card bg-zinc-900 border-secondary flex-grow-1 text-white shadow-sm">
                <div class="card-header border-secondary bg-dark text-center py-1">Buscar y gestionar</div>
                <div class="card-body d-flex flex-column gap-1 p-2">
                    <form method="GET" action="{{ route('admin.users') }}" class="d-flex flex-column flex-lg-row gap-2">
                        <input name="q" class="form-control form-control-sm" placeholder="Buscar por ID, nombre o correo" value="{{ $search }}">
                        <button type="submit" class="btn btn-outline-info btn-sm">Buscar</button>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.users', ['q' => $search, 'mode' => 'create']) }}">Crear usuario</a>
                    </form>

                    @if ($users->count() === 0)
                        <div class="small text-secondary">No hay usuarios para mostrar.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Plan</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>{{ $user->plan }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-outline-info btn-sm" href="{{ route('admin.users', ['q' => $search, 'page' => $users->currentPage(), 'mode' => 'edit', 'edit' => $user->id]) }}">
                                                    Editar
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres borrar este usuario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="q" value="{{ $search }}">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Borrar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2">
                            {{ $users->links('pagination::simple-bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
