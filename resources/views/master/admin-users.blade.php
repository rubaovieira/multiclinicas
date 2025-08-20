@extends('layouts.app')

@section('title', 'Administradores')

@section('content')
<div class="d-flex justify-content-center align-items-center">
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">
            
            <!-- Botão Criar Novo -->
            <div class="text-end mb-3">
                <a href="{{ route('admin-users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i>
                    Novo Administrador</a>
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('admin-users') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome, email ou CPF" aria-label="Buscar" value="{{ request('query') }}">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Clínica</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    @if(count($users) == 0)
                    <tbody>
                        <td colspan="6">
                            <div class="alert alert-warning" role="alert">
                                Nenhum administrador encontrado.
                            </div>
                        </td>
                    </tbody>
                    @endif
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->cpf }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td>{{ $user->clinica?->nome ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $user->active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin-users.edit', $user->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                @if($user->active)
                                    <form action="{{ route('admin-users.deactivate', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente desativar este administrador?')">Desativar</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin-users.activate', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Reativar</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin-users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este administrador?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>   
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} resultados</p>
                    <div>
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 