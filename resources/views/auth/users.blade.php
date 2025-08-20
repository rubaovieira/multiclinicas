@extends('layouts.app')

@section('title', 'Equipe') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">   
            <!-- Botão Criar Novo -->
            <div class="text-end mb-3">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="bi bi-plus
                    "></i>
                    Criar Novo Usuário</a>
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('users') }}" method="GET" class="mb-4" >
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome, email ou obsevação" aria-label="Buscar"  value="{{ $_GET['query']??'' }}">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Perfil</th>
                            <th>Email</th>
                            <th>Observação</th>  
                            @if(auth()->user() && auth()->user()->perfil === 'admin')
                                <th>Ações</th> 
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ ($user->perfil == 'funcionario') ? 'Encarregado' : (($user->perfil == 'faxina') ? 'Limpeza' : $user->perfil) }}</td>  
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->observation }}</td>
                            
                                <td class="d-flex gap-2"> 
                                @if(auth()->user()->perfil === 'admin')
                                    <a href="{{ route('schedule', ['id' => $user->id]) }}" class="btn btn-primary btn-sm">Quadro</a>
                                    @if($user->active) 
                                        <a href="{{ route('edit-users', ['id' => $user->id]) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="{{ route('delete-users', ['id' => $user->id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente desativar este usuario?')">Desativar</a>
                                    @else
                                        <a href="{{ route('activate-users', ['id' => $user->id]) }}" class="btn btn-success btn-sm">Reativar</a>
                                    @endif
                                @endif
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
