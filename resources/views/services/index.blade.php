@extends('layouts.app')

@section('title', 'Atendimentos') 

@section('content')

<?php  
$agent = new \Jenssegers\Agent\Agent();
 
$isMobile = $agent->isMobile();
 
?>

<style>
    @media (max-width: 768px) {
    .table {
        font-size: 11px; /* Tamanho da fonte apenas para dispositivos móveis */
    }
}
</style>
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">  
            
            <!-- Botão Criar Novo -->
            <div class="text-end mb-3">
                <a class="btn btn-primary" onclick="abrirmodalnewservice()" >
                    <i class="bi bi-plus
                    "></i>
                    Atendimento</a>
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('services') }}" method="GET" class="mb-4" >
                <div class="input-group gap-2">
                    
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome, nascimento, responsável ou cpf" aria-label="Buscar"  value="{{ $_GET['query']??'' }}">
                   
                    <div class="row">
                        <div class="col">
                            <input type="date" name="data" class="form-control" placeholder="dd/mm/yyyy" aria-label="Buscar"  value="{{ $_GET['data']??'' }}">
                        </div>
                    </div>
                     
                    <select name="status" id="status">
                        <option value="">Todos</option> 
                        <option value="EM ANDAMENTO"  {{ (isset($_GET['status'])  && $_GET['status'] == 'EM ANDAMENTO') ?'selected':''; }} >Em andamento</option>
                        <option value="FINALIZADO" {{ (isset($_GET['status'])  && $_GET['status'] == 'FINALIZADO') ?'selected':''; }}>Finalizado</option>
                    </select>
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr> 
                            <th>Status</th>
                            <th>Nome</th> 
                            @if(!$isMobile) 
                                <th>Nascimento</th> 
                                <th>Telefone</th> 
                                <th>Responsável</th> 
                                <th>CPF</th> 
                                <th>Plano</th>   
                            @endif
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($services) == 0)
                            <tr>
                                <td colspan="8">
                                    <div class="alert alert-warning" role="alert">
                                        Nenhum atendimento encontrado.
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach($services as $service)
                        <tr> 
                            <td>{{ $service->status }}</td> 
                            <td>{{ $service->client->name }}</td>
                            @if(!$isMobile)  
                            <td>{{ date('d/m/Y', strtotime($service->client->date_birth)) }}</td>  
                            <td>{{ $service->client->telephone }}</td>  
                            <td>{{ $service->client->caregiver_responsible }}</td>  
                            <td>{{ $service->client->cpf }}</td>
                            <td>{{ $service->client->healthPlan->name }}</td> 
                            @endif 
                            <td class="d-flex gap-1 flex-wrap"> 
                                @if(auth()->user() && (auth()->user()->perfil === 'medico'  || auth()->user()->perfil === 'equipe-multidisciplinar'  ))
                                    <button class="btn btn-primary btn-sm mb-1" style="font-size: 12px; padding: 4px 8px;" onclick="abrirmodal('{{$service->id}}')">+ Medicamento</button>
                                @endif 
                                @if(auth()->user() && auth()->user()->perfil !== 'equipe-tecnica')
                                    <button class="btn btn-primary btn-sm mb-1" style="font-size: 12px; padding: 4px 8px;" onclick="abrirmodalProcedure('{{$service->id}}')">+ Procedimento</button>
                                @endif
                                <button class="btn btn-primary btn-sm mb-1" style="font-size: 12px; padding: 4px 8px;" onclick="abrirmodalhistorico('{{$service->id}}')"> 
                                    <i class="bi bi-clock-history"></i> Histórico de Atividades
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>   
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">Mostrando {{ $services->firstItem() }} a {{ $services->lastItem() }} de {{ $services->total() }} resultados</p>
                    <div>
                        {{ $services->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function abrirmodalhistorico($id){
        //fazer a requisição ajax
        $.ajax({
            url: "{{ route('service-history') }}",
            type: 'GET',
            data: {service_id: $id},
            success: function(data) {
                $("#exampleModalLabel").text('Histórico de Atividades');
                $(".modal-body").html(data) 
                $('#exampleModal').modal('show');
            }
        });  

    }

    function abrirmodal($id) { 
        $("#exampleModalLabel").text('Adicionar Medicamentos');
        var html = `
            <form action="{{ route('service-medicine-item') }}" method="POST">
                @csrf
                <input type="hidden" name="service_id" value='${$id}'>
                <input type="hidden" name="type" value="medicine">
                <div class="mb-3">
                    <label for="medicamento" class="form-label">Medicamento</label>
                    <input type="text" class="form-control" id="medicamento" name="medicamento" required>
                </div> 
                <div class="mb-3">
                    <label for="observation" class="form-label">Observação</label>
                    <textarea class="form-control" id="observation" name="observation" required></textarea>
                </div>
                <hr>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        `;
        $(".modal-body").html(html)
        $('#exampleModal').modal('show');
    }

    function abrirmodalProcedure($id) { 
        $("#exampleModalLabel").text('Adicionar Procedimento');
        var html = `
            <form action="{{ route('service-procedure-item') }}" method="POST">
                @csrf
                <input type="hidden" name="service_id" value='${$id}'>
                <input type="hidden" name="type" value="procedure">
                <div class="mb-3">
                    <label for="procedure_id" class="form-label
                    ">Procedimento</label>
                    <select class="form-select" id="procedure_id" name="procedure_id" required>
                        @foreach($procedures as $procedure)
                            <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="observation" class="form-label">Observação</label>
                    <textarea class="form-control" id="observation" name="observation" required></textarea>
                </div>
                <hr>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        `;
        $(".modal-body").html(html)
        $('#exampleModal').modal('show');
    }

    function abrirmodalnewservice(){
        //fazer a requisição ajax para pegar lista de clientes 
        $.ajax({
            url: "{{ route('service-new') }}",
            type: 'GET',
            success: function(data) {
                $("#exampleModalLabel").text('Novo Atendimento');
                $(".modal-body").html(data)
                $('#exampleModal').modal('show');
            }
        }); 
    }
</script>

@endsection
