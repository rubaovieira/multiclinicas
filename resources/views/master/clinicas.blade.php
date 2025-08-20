@extends('layouts.app')

@section('title', 'Clínicas') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">  
            
            <!-- Botão Criar Novo -->
            <div class="text-end mb-3">
                <a href="{{ route('clinics.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i>
                    Nova Clínica</a>
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('clinics') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome da clínica" aria-label="Buscar" value="{{ $_GET['query']??'' }}">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th> 
                            <th>Status</th> 
                            <th>Link</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clinicas as $clinica)
                        <tr>
                            <td>{{ $clinica->nome }}</td> 
                            <td>
                                <span class="badge {{ $clinica->status == 'ativo' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($clinica->status) }}
                                </span>
                            </td>  

                            <td>
                                <a href="http://127.0.0.1:8000/login/{{$clinica->slug}}" target="_blank">
                                    http://127.0.0.1:8000/login/{{$clinica->slug}}
                                </a>
                            </td>

                            <td class="d-flex gap-2">
                                <a href="{{ route('clinics.edit', $clinica->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                @if($clinica->status == 'ativo')
                                    <form action="{{ route('clinics.deactivate', $clinica->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente desativar esta clínica?')">Desativar</button>
                                    </form>
                                @else
                                    <form action="{{ route('clinics.activate', $clinica->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Ativar</button>
                                    </form>
                                @endif
                                <form action="{{ route('clinics.delete', $clinica->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente excluir esta clínica?')">Excluir</button>
                                </form>
                                @if($clinica->trashed())
                                    <form action="{{ route('clinics.restore', $clinica->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Restaurar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma clínica encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">Mostrando {{ $clinicas->firstItem() }} a {{ $clinicas->lastItem() }} de {{ $clinicas->total() }} resultados</p>
                    <div>
                        {{ $clinicas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
