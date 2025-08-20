@extends('layouts.app')

@section('title', 'Procedimentos') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">  
            
            <!-- Botão Criar Novo -->
            <div class="text-end mb-3">
                <a href="{{ route('new-procedure') }}" class="btn btn-primary">
                    <i class="bi bi-plus
                    "></i>
                    Criar Novo Procedimento</a>
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('procedures') }}" method="GET" class="mb-4" >
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome" aria-label="Buscar"  value="{{ $_GET['query']??'' }}">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>  
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procedures as $procedure)
                        <tr>
                            <td>{{ $procedure->name }}</td>   
                            <td class="d-flex gap-2">  
                                @if($procedure->active)  
                                    <a href="{{ route('edit-procedure', ['id' => $procedure->id]) }}" class="btn btn-warning btn-sm">Editar</a> 
                                    <a href="{{ route('delete-procedure', ['id' => $procedure->id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente desativar este procedimento?')">Desativar</a>
                                @else 
                                    <a href="{{ route('activate-procedure', ['id' => $procedure->id]) }}" class="btn btn-success btn-sm">Reativar</a> 
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>   
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">Mostrando {{ $procedures->firstItem() }} a {{ $procedures->lastItem() }} de {{ $procedures->total() }} resultados</p>
                    <div>
                        {{ $procedures->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
