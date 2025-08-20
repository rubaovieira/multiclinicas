@extends('layouts.app')

@section('title', 'Atendimentos do Paciente')

@section('content')
<div class="d-flex justify-content-center align-items-center mt-4">
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-5">
          
            <div class="mb-4">
                    <!-- Botão Criar Novo -->
                    <div class="text-end mb-3">
                        <a class="btn btn-primary" onclick="abrirmodalnewservice('{{ $client->id }}')" >
                            <i class="bi bi-plus
                            "></i>
                            Atendimento</a>
                    </div>


                <h4 class="text-primary">Dados do Paciente</h4>
                
                <hr>
                <div class="row gy-3">
                    <div class="col-lg-4">
                        <span>
                            <b>Nome Completo:</b> {{ $client->name }}
                        </span>
                    </div>
                    <div class="col-lg-2">
                        <span>
                            <b>Nascimento:</b> {{ date('d/m/Y', strtotime($client->date_birth)) }}
                        </span>
                    </div>
                    <div class="col-lg-2">
                        <span>
                            <b>Telefone:</b> {{ $client->telephone }}
                        </span>
                    </div>
                    <div class="col-lg-2">
                        <span>
                            <b>CPF:</b> {{ $client->cpf }}
                        </span>
                    </div>
                    <div class="col-lg-2">
                        <span>
                            <b>Plano de Saúde:</b> {{ $client->healthPlan->name }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-primary">Diagnóstico</h4>
                <hr>
                <div>
                    <span>{{ $client->diagnosis }}</span>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-primary">Atendimentos do Paciente</h4>
                <hr>
                @if(count($services_client) == 0)
                    <div class="alert alert-warning" role="alert">
                        Nenhum atendimento encontrado.
                    </div>
                @endif
                <div class="accordion" id="accordionExample">
                    @foreach($services_client as $key => $service)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="{{$service->id}}">
                           
                                <button class="accordion-button d-flex justify-content-between {{ ($key > 0) ? 'collapsed' : ''}}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$service->id}}" aria-expanded="true" aria-controls="collapse{{$service->id}}">
                                    <span>
                                        Atendimento {{ date('d/m/Y H:i:s', strtotime($service->created_at)) }}
                                        {{' - '}} {{ $service->status }}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{$service->id}}" class="accordion-collapse collapse {{ ($key <= 0) ? 'show' : ''}}" aria-labelledby="{{$service->id}}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                            @if(auth()->user() && auth()->user()->perfil === 'medico' || auth()->user()->perfil === 'equipe-multidisciplinar' || auth()->user()->perfil === 'admin')
                                                <div class="col-lg-auto">
                                                    <button class="btn btn-primary btn-sm" 
                                                            onclick="abrirmodal('{{$service->id}}')"
                                                        >
                                                        <i class="bi bi-plus"></i>
                                                        Medicamentos
                                                    </button>
                                                </div>
                                            @endif

                                          

                                            @if(auth()->user() && auth()->user()->perfil === 'medico' || auth()->user()->perfil === 'equipe-multidisciplinar' || auth()->user()->perfil === 'admin')
                                                <div class="col-lg-auto">
                                                    <button class="btn btn-primary btn-sm" 
                                                            onclick="abrirmodalEvolucao('{{$service->id}}')"
                                                        >
                                                        <i class="bi bi-plus"></i>
                                                        Evolução
                                                    </button>
                                                </div>
                                            @endif

                                            @if(auth()->user() && auth()->user()->perfil !== 'equipe-tecnica')
                                                <div class="col-lg-auto">
                                                    <button class="btn btn-primary btn-sm"
                                                    onclick="abrirmodalProcedure('{{$service->id}}')"
                                                    ><i class="bi bi-plus"></i> Procedimento</button>
                                                </div>
                                            @endif

                                            @if(auth()->user() && auth()->user()->perfil !== 'equipe-tecnica')
                                                <div class="col-lg-auto">
                                                    <button class="btn btn-primary btn-sm"
                                                    onclick="abrirmodalAnexo('{{$service->id}}')"
                                                    ><i class="bi bi-plus"></i> Anexo </button>
                                                </div>
                                            @endif

                                            @if(auth()->user() && (auth()->user()->perfil === 'admin' || auth()->user()->perfil === 'medico'))
                                                <div class="col-lg-auto">
                                                    <button class="btn btn-primary btn-sm"
                                                    onclick="abrirmodalReceita('{{$service->id}}')"
                                                    ><i class="bi bi-plus"></i> Receita </button>
                                                </div>
                                            @endif

                                        @if(auth()->user() && auth()->user()->perfil !== 'equipe-tecnica')
                                            @if($service->status == 'EM ANDAMENTO')
                                                <div class="col">
                                                    <button class="btn btn-success btn-sm" 
                                                        onclick="finalizarService('{{ $service->id }}')"
                                                    >  <i class="bi bi-check-circle"></i> Finalizar</button>
                                                </div>
                                            @else
                                                <div class="col">
                                                    <button class="btn btn-warning btn-sm"
                                                    onclick="reabrirService('{{ $service->id }}')"
                                                    >  <i class="bi bi-arrow-repeat"></i> Reabrir</button>
                                                </div>
                                            @endif
                                        @endif

                                       


                                        @if(auth()->user() && auth()->user()->perfil === 'admin')
                                            <div class="col-lg-auto">
                                                <button class="btn btn-primary btn-sm" 
                                                        onclick="window.open('{{ route('print-location-occurrences', ['id' => $service->id]) }}', '_blank')">
                                                <i class="bi bi-printer"></i> Receita
                                            </button>
                                        </div>
                                        @endif
                                     
                                        <div class="col-lg-auto">
                                            <button class="btn btn-primary btn-sm" 
                                                onclick="window.open('{{ route('print-location-occurrences', ['id' => $service->id]) }}', '_blank')">
                                                <i class="bi bi-printer"></i> Imprimir
                                            </button>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <br>
                                        </div>
                                    </div>
                                    
                                    @if(auth()->user() && auth()->user()->perfil === 'admin')
                                        <div class="row">
                                            <div class="col-lg-auto">   
                                                <label for="qtd_evolucoes" class="form-label">Quantidade de evoluções por profissional</label>
                                                <input type="number" id="qtd_evolucoes_{{ $service->id }}"  onChange="salvarqtdev('{{ $service->id }}')"  class="form-control" value="{{ $service->limit_evolution }}"> 
                                            </div> 
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col">
                                            <br>
                                        </div>
                                    </div>
                                    @if(count($service->service_items()) == 0)
                                        <div class="alert alert-warning" role="alert">
                                            Nenhum item encontrado.
                                        </div>
                                    @endif
                                    @foreach($service->service_items() as $item)
                                    <div class="card mb-2">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div>
                                               
                                                <small class="text-muted">
                                                    {{ $item->user?->name ?? 'Usuário não disponível' }} - 
                                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                                </small>
                                                <div>
                                                    @if($item instanceof \App\Models\ServiceProcedure) 
                                                        <strong>Procedimento:</strong> {{ $item->procedure->name }}
                                                        @if($item->observation)
                                                            <strong>Observação:</strong> {{ $item->observation }}
                                                        @endif 
                                                        @php $itemType = 'procedure'; @endphp
                                                    @elseif($item instanceof \App\Models\ServiceEvolution)
                                                        <strong>Evolução:</strong> {{ $item->evolution_text }}
                                                        @php $itemType = 'evolution'; @endphp 
                                                    
                                                    @elseif($item instanceof \App\Models\ServiceFile)
                                                        <strong>Arquivo:</strong> 
                                                        @php
                                                            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']; // Extensões de imagens permitidas
                                                            $extension = pathinfo($item->file_path, PATHINFO_EXTENSION);
                                                        @endphp
                                                      
                                                            @if(in_array(strtolower($extension), $extensions))
                                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"><img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->file_name }}" style="width: 100px; height: auto;"></a>
                                                            @else
                                                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank">{{ $item->file_name }}</a>
                                                            @endif
                                                        @php $itemType = 'file'; @endphp 
                                                    
                                                    @elseif($item instanceof \App\Models\ServiceMedicine) 
                                                        <strong>Medicamento:</strong> {{ $item->medicamento }}
                                                        @if($item->observation)
                                                            <strong>Observação:</strong> {{ $item->observation }}
                                                        @endif 
                                                        <div> 
                                                     

                                                            @foreach($item->serviceMedicineTimes as $time)

                                                                @php
                                                                    $isLate = false;

                                                                    foreach ($time->serviceMedicineTimeMinistereds as $ministered) {
                                                                        if (strtotime($time->time) < strtotime('-3 hours', strtotime($ministered->created_at))) {
                                                                            $isLate = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp

                                                                @if($isLate)
                                                                    <span class="badge bg-warning text-black">{{ $time->time }}<i class="bi bi-check2"></i> 
                                                                </span>

                                                                @elseif($time->serviceMedicineTimeMinistereds->count() > 0)
                                                                    <span class="badge bg-success">{{ $time->time }} <i class="bi bi-check2"></i></span>
                                                                @else
                                                                    @if(date('H:i',strtotime($time->time)) < date('H:i', strtotime('-3 hours')))
                                                                        <span class="badge bg-danger"
                                                                           onclick="abrirmodalMinistrar('{{$time->id}}', '{{$time->time}}')"
                                                                        >{{ $time->time }} <i class="bi bi-hourglass-bottom"></i></span>
                                                                    @else

                                                                        @if(date('H:i', strtotime($time->time)) <= date('H:i', strtotime('-3 hours +15 minutes')))
                                                                        <span class="badge bg-primary" 
                                                                                onclick="abrirmodalMinistrar('{{$time->id}}', '{{$time->time}}')" 
                                                                            >{{ $time->time }} <i class="bi bi-clock-history"></i>
                                                                        </span>
                                                                        @else
                                                                        <span class="badge bg-secondary " 
                                                                        style="cursor: not-allowed;"
                                                                        onclick="alert('Você só poderá ministrar esse medicamento 15min antes do horário dele')"

                                                                        title='Você só poderá ministrar esse medicamento 15min antes do horário dele' >{{ $time->time }} <i class="bi bi-clock-history"></i></span>
                                                                        @endif 

                                                                    @endif 
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        @php $itemType = 'medicine'; @endphp
                                                    @elseif($item instanceof \App\Models\Receita)
                                                        <strong>Receita:</strong> {{ $item->receita_text }}
                                                        <div class="mt-2">
                                                            <a href="{{ route('receita.print', $item->id) }}" target="_blank" class="btn btn-primary btn-sm">
                                                                <i class="bi bi-printer"></i> Imprimir Receita
                                                            </a>
                                                        </div>
                                                        @php $itemType = 'receita'; @endphp
                                                    @endif
                                                </div>
                                            </div>
                                           
                                            <div class="d-flex align-items-center"> 
                                                @if((auth()->user() && auth()->user()->perfil === 'efermeria' && $itemType === 'medicine'  && $itemType === 'evolution')) 
                                               
                                                @elseif(auth()->user() && (auth()->user()->perfil === 'medico' || auth()->user()->perfil === 'equipe-multidisciplinar' || auth()->user()->perfil === 'efermeria'  || auth()->user()->perfil === 'admin'))
                                                    
                                                    @if($item instanceof \App\Models\Receita)
                                                        <form action="{{ route('receita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta receita?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="bi bi-trash3"></i> 
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('service_items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este item?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="type" value="{{ $itemType }}">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="bi bi-trash3"></i> 
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif 
                                               
                                            </div>
                                           
                                        </div>
                                    </div>
                                @endforeach

                                </div> 
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>

function salvarqtdev(id) { 
    // Capturar o valor do input baseado no ID específico do serviço
    var qtd = document.getElementById('qtd_evolucoes_' + id).value;  
    var _token = document.getElementsByName('_token')[0].value; // Certifique-se de que o _token existe na página
    var url = "{{ route('qtd_evolution') }}";

    var data = {
        qtd: qtd, 
        id: id,
        _token: _token
    };

    // Enviar os dados via AJAX
    $.post(url, data, function(response) {
        console.log(response);
    }).fail(function(error) {
        console.error('Erro ao salvar:', error);
    });
}

function abrirmodal($id) { 
    $("#exampleModalLabel").text('Adicionar Medicamentos');
    
    // <div class="mb-3">
    //             <label for="medicamento" class="form-label">Medicamento</label>
    //             <input type="text" class="form-control" id="medicamento" name="medicamento" required>
    //         </div>
    // HTML do formulário com campos de Horário Inicial e Posologia
    var html = `
        <form id="medicine-form" action="{{ route('service-medicine-item') }}" method="POST">
            @csrf
            <input type="hidden" name="service_id" value='${$id}'>
            <input type="hidden" name="type" value="medicine">
              
            <div class="mb-3">
                <label for="product_id" class="form-label">Medicamento</label>
                <select class="form-select" id="product_id" name="product_id" required>
                        <option value="">Selecione um Medicamento</option>
                    @foreach($stocks as $stock)
                        <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="observation" class="form-label">Observação</label>
                <textarea class="form-control" id="observation" name="observation"></textarea>
                <input type="hidden" id="next_times" name="next_times">
            </div>

            <!-- Campo para o horário inicial -->
            <div class="mb-3">
                <label for="start_time" class="form-label">Horário Inicial</label>
                <input type="time" class="form-control" id="start_time" name="start_time" required>
            </div>

            <!-- Campo para a posologia -->
            <div class="mb-3">
                <label for="posology" class="form-label">Posologia</label>
                <select class="form-control" id="posology" name="posology" required>
                    <option value="6x6">6x6 (6 horas)</option>
                    <option value="12x12">12x12 (12 horas)</option>
                    <option value="2x2">2x2 (2 horas)</option>
                    <option value="3x3">3x3 (3 horas)</option>
                    <option value="4x4">4x4 (4 horas)</option>
                    <option value="1x1">1x1 (1 hora)</option>
                    <option value="8x8">8x8 (8 horas)</option>
                    <option value="10x10">10x10 (10 horas)</option>
                    <option value="24x24">24x24 (24 horas)</option>
                </select>
                <small class="form-text text-muted">Escolha a posologia.</small>
            </div>

            <div class="mb-3">
                <label for="next_times" class="form-label">Horários</label>
                <ul id="next_times_list" class="list-group"></ul>
            </div>

            <hr>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    `;
    
    // Adiciona o conteúdo HTML no modal
    $(".modal-body").html(html);
    
    // Exibe o modal
    $('#exampleModal').modal('show');

     $('#start_time, #posology').on('change', function() {
        var startTime = $('#start_time').val();
        var posology = $('#posology').val().trim();

        var nextTimes = calculateNextTimes(startTime, posology);
        $('#next_times').val(JSON.stringify(nextTimes));
        $('#next_times_list').empty();
        nextTimes.forEach(function(time) {
            $('#next_times_list').append('<li class="list-group-item">' + time + '</li>');
        });
    });
    
    function calculateNextTimes(startTime, posology) {
        var timeIntervals = {
            '6x6': 6,    // 6 horas de intervalo
            '12x12': 12,  // 12 horas de intervalo
            '2x2': 2,    // 2 horas de intervalo
            '3x3': 3,    // 3 horas de intervalo
            '4x4': 4,    // 4 horas de intervalo
            '1x1': 1,    // 1 hora de intervalo
            '8x8': 8,    // 8 horas de intervalo
            '10x10': 10, // 10 horas de intervalo
            '24x24': 24  // 24 horas de intervalo
        };

        var interval = timeIntervals[posology] || 0;
        if (interval === 0 || !startTime) return [];

        var times = [];
        var startDate = new Date();

        startDate.setHours(startTime.split(':')[0]);
        startDate.setMinutes(startTime.split(':')[1]);
        startDate.setSeconds(0);  // Garantir que os segundos sejam 0

        // Adicionar horários até 24 horas após o horário inicial
        var endTime = new Date(startDate);
        endTime.setHours(startDate.getHours() + 24); // 24 horas a partir do horário inicial

        while (startDate < endTime) {
            // Adiciona o horário atual e calcula o próximo
            var formattedTime = startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            times.push(formattedTime);
            
            // Atualiza para o próximo horário
            startDate.setHours(startDate.getHours() + interval);
        }

        return times;
    }
}
function abrirmodalEvolucao($id) { 
    $("#exampleModalLabel").text('Adicionar Evolução');
    
    var html = `
        <form id="medicine-form" action="{{ route('service-evolution') }}" method="POST">
            @csrf
            <input type="hidden" name="service_id" value='${$id}'> 

            <div class="mb-3">
                <label for="evolution_text" class="form-label">Evolução</label>
                <textarea class="form-control" id="evolution_text" name="evolution_text"></textarea>
                <input type="hidden" id="next_times" name="next_times">
            </div> 

            <hr>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    `;
    
    $(".modal-body").html(html);
    
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
                    <textarea class="form-control" id="observation" name="observation" ></textarea>
                </div>
                <hr>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        `;
        $(".modal-body").html(html)
        $('#exampleModal').modal('show');
    }

 
    function abrirmodalnewservice(id){
        //fazer a requisição ajax para pegar lista de clientes 
        $.ajax({
            url: "{{ route('service-new') }}?id=" + id, // Passa o id como parâmetro
            type: 'GET',
            success: function(data) {
                $("#exampleModalLabel").text('Novo Atendimento');
                $(".modal-body").html(data)
                $('#exampleModal').modal('show');
            }
        }); 
    }

    function finalizarService(serviceId) {
    $.ajax({
        url: "{{ route('service-finish') }}", // Altere para a rota correta
        type: 'POST',
        data: {
            id: serviceId,
            _token: '{{ csrf_token() }}' // Inclui o token CSRF
        },
        success: function(response) { 
            location.reload(); // Opcional: recarrega a página para refletir as mudanças
        },
        error: function(xhr) {
            // Trate erros
            alert('Ocorreu um erro ao finalizar o serviço.');
        }
    });
}


function abrirmodalMinistrar($id,$horario) { 
        $("#exampleModalLabel").text('Ministrar Medicamento');
        var html = `
            <form action="{{ route('service-medicine-minister') }}" method="POST">
                @csrf
                Horário: ${$horario}
                <input type="hidden" name="service_medicine_time_id" value='${$id}'>  
                <div class="mb-3">
                    <label for="qtd" class="form-label">Quantidade usada</label>
                    <input class="form-control" id="qtd" name="qtd"  value="1" > 
                </div>
                <div class="mb-3">
                    <label for="observation" class="form-label">Observação</label>
                    <textarea class="form-control" id="observation" name="observation" ></textarea>
                </div>
                <hr>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        `;
        $(".modal-body").html(html)
        $('#exampleModal').modal('show');
}

function reabrirService(serviceId) {
    $.ajax({
        url: "{{ route('service-open') }}", // Altere para a rota correta
        type: 'POST',
        data: {
            id: serviceId,
            _token: '{{ csrf_token() }}' // Inclui o token CSRF
        },
        success: function(response) { 
            location.reload(); // Opcional: recarrega a página para refletir as mudanças
        },
        error: function(xhr) { 
            alert('Ocorreu um erro ao reabrir o serviço.');
        }
    });
}

function abrirmodalReceita($id) { 
    $("#exampleModalLabel").text('Adicionar Receita');
    
    var html = `
        <form action="{{ route('receita.store') }}" method="POST">
            @csrf
            <input type="hidden" name="service_id" value='${$id}'> 

            <div class="mb-3">
                <label for="receita_text" class="form-label">Receita</label>
                <textarea class="form-control" id="receita_text" name="receita_text" rows="10"></textarea>
            </div> 

            <hr>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    `;
    
    $(".modal-body").html(html);
    
    $('#exampleModal').modal('show'); 
}

</script>


@section('scripts')
<script>

function abrirmodalAnexo($id) { 
    $("#exampleModalLabel").text('Adicionar Anexo');

    var html = `
        <form action="{{ route('service-attachment') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="service_id" value='${$id}'> 
            <input type="file" 
                id="fileupload"
                class="filepond"
                name="filepond[]" 
                multiple />    
            <hr>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button> 
        </form>  
    `; 
        
    $(".modal-body").html(html);
    $('#exampleModal').modal('show');

    FilePond.setOptions({
        allowMultiple: true,
        labelIdle: `<span class="filepond--label-action"> Procurar </span> ou arraste um arquivo até aqui`,
        maxFiles: 5,
        server: {
            process: {
                url: "{{ route('service-attachment') }}", // Ajuste para a sua rota
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Incluindo o CSRF token
                },
                ondata: (formData) => {
                    // Adiciona o service_id ao FormData
                    formData.append('service_id', document.querySelector('[name="service_id"]').value);
                    return formData;
                },
                onload: () => {
                    // Recarrega a página após o upload bem-sucedido
                    setTimeout(() => {
                        location.reload();
                    }, 500); // Adicione um pequeno delay, se necessário
                },
            },
            revert: {
                url: "{{ route('service-attachment-revert') }}", // Ajuste para a sua rota de exclusão
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Incluindo o CSRF token
                },
            },
        },
    });

 
    // Inicializa o FilePond para o novo elemento
    FilePond.create(document.querySelector('#fileupload'));
}


     
 

</script> 

@endsection


@endsection
