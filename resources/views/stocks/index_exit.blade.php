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
                <a class="btn btn-primary btn-sm" onclick="abrirmodalnewexit()" >
                    <i class="bi bi-plus
                    "></i>
                    Saidas</a> 
            </div>
            
            <!-- Formulário de Busca -->
            <form action="{{ route('saidas') }}" method="GET" class="mb-4" >
                <div class="input-group gap-2">
                    <input type="text" name="query" class="form-control" placeholder="Buscar por nome" aria-label="Buscar"  value="{{ $_GET['query']??'' }}">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr> 
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Data</th>   
                            <th>Operador de cadastro</th>  
                            @if(auth()->user() && auth()->user()->perfil === 'admin') 
                                <th>Ação</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($exits) == 0)
                            <tr>
                                <td colspan="8">
                                    <div class="alert alert-warning" role="alert">
                                        Nenhum registro encontrado.
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach($exits as $exit)
                            <tr> 
                                <td>{{ $exit->product->name }}</td> 
                                <td>{{ $exit->qtd * -1 }}</td> 
                                <td>{{ date('d/m/Y H:i', strtotime($exit->created_at)) }}</td>
                                <td>{{ $exit->user->name }}</td> 
                                @if(auth()->user() && auth()->user()->perfil === 'admin')
                                    <td class="d-flex gap-2">
                                        @if($exit->active)
                                            <a href="{{ route('delete-inventory_controls', ['id' => $exit->id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente desativar ?')">Desativar</a>
                                        @else
                                            <a href="{{ route('activate-inventory_controls', ['id' => $exit->id]) }}" class="btn btn-success btn-sm">Reativar</a> 
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">Mostrando {{ $exits->firstItem() }} a {{ $exits->lastItem() }} de {{ $exits->total() }} resultados</p>
                    <div>
                        {{ $exits->links('pagination::bootstrap-4') }}
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

     
    function abrirmodalnewexit(){
        //fazer a requisição ajax para pegar lista de clientes 
        $.ajax({
            url: "{{ route('product-exit') }}",
            type: 'GET',
            success: function(data) {
                $("#exampleModalLabel").text('Lançar Saída');
                $(".modal-body").html(data)
                $('#exampleModal').modal('show');
            }
        }); 
    }

       
    function abrirmodalnewentry(){
        //fazer a requisição ajax para pegar lista de clientes 
        $.ajax({
            url: "{{ route('product-entry') }}",
            type: 'GET',
            success: function(data) {
                $("#exampleModalLabel").text('Lançar Entrada');
                $(".modal-body").html(data)
                $('#exampleModal').modal('show');
            }
        }); 
    }
   
    function abrirmodalnewservice(){
        //fazer a requisição ajax para pegar lista de clientes 
        $.ajax({
            url: "{{ route('product-new') }}",
            type: 'GET',
            success: function(data) {
                $("#exampleModalLabel").text('Lançar Produto');
                $(".modal-body").html(data)
                $('#exampleModal').modal('show');
            }
        }); 
    }
</script>

@endsection
