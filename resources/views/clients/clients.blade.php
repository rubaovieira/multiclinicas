@extends('layouts.app')

@section('title', 'Pacientes') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">  
            
            <!-- Botão Criar Novo -->
            <div class="text-end mb-3">
                <a href="{{ route('new-client') }}" class="btn btn-primary">
                    <i class="bi bi-plus
                    "></i>
                    Criar Novo Cliente</a>
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('clients') }}" method="GET" class="mb-4" >
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome, nascimento, responsável ou cpf" aria-label="Buscar"  value="{{ $_GET['query']??'' }}">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th> 
                            <th>Nascimento</th> 
                            <th>Telefone</th> 
                            <th>Responsável</th> 
                            <th>CPF</th> 
                            <th>Plano</th> 
                            <th>Ações</th>
                        </tr>
                    </thead>
                    @if(count($clients) == 0)
                    <tbody>
                        <td colspan="7">
                            <div class="alert alert-warning" role="alert">
                                Nenhum atendimento encontrado.
                            </div>
                        </td>
                    </tbody>
                    @endif
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            <td>{{ $client->name }}</td> 
                            <td>{{ date('d/m/Y', strtotime($client->date_birth)) }}</td>  
                            <td>{{ $client->telephone }}</td>  
                            <td>{{ $client->caregiver_responsible }}</td>  
                            <td>{{ $client->cpf }}</td>
                            <td>{{ $client->healthPlan->name }}</td>  
                            <td class="d-flex gap-2">  
                                @if($client->active) 
                                    <a href="{{ route('service-client', ['id' => $client->id]) }}" class="btn btn-primary btn-sm">Atendimentos</a>  
                                    <a href="{{ route('edit-client', ['id' => $client->id]) }}" class="btn btn-warning btn-sm">Editar</a> 
                                    @if(auth()->user() && auth()->user()->perfil === 'admin' || auth()->user()->perfil === 'medico' || auth()->user()->perfil === 'equipe-multidisciplinar')
                                        <a href="{{ route('delete-client', ['id' => $client->id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente desativar este cliente?')">Desativar</a>
                                    @endif
                                @else 
                                    <a href="{{ route('activate-client', ['id' => $client->id]) }}" class="btn btn-success btn-sm">Reativar</a> 
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>   
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">Mostrando {{ $clients->firstItem() }} a {{ $clients->lastItem() }} de {{ $clients->total() }} resultados</p>
                    <div>
                        {{ $clients->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
