
<div class="d-flex justify-content-center align-items-center mt-3">
    <div class=" border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class=" p-3">    
            <div class="mb-1"> 
                <div class="accordion" id="accordionExample">  
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="{{$service->id}}">
                                <button class="accordion-button d-flex justify-content-between " type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$service->id}}" aria-expanded="true" aria-controls="collapse{{$service->id}}">
                                    <span>
                                        Atendimento {{ date('d/m/Y H:i:s', strtotime($service->created_at)) }}
                                        {{' - '}} {{ $service->status }}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{$service->id}}" class="accordion-collapse collapse show" aria-labelledby="{{$service->id}}" data-bs-parent="#accordionExample">
                                <div class="accordion-body"> 
                                    <div class="row">
                                        <div class="col">
                                            <br>
                                        </div>
                                    </div>
                                    @if(count($items) == 0)
                                        <div class="alert alert-warning" role="alert">
                                            Nenhum item encontrado.
                                        </div>
                                    @endif
                                    @foreach($items as $item)
                                        <div class="card mb-2"> <!-- Card para cada item -->
                                            <div class="card-body d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">
                                                        {{ $item->user->name }} - {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}

                                                    </small>
                                                    <div>
                                                        @if($item instanceof \App\Models\ServiceProcedure) 
                                                            <strong>Procedimento:</strong> {{ $item->procedure->name }}
                                                            <strong>Observação:</strong> {{ $item->observation }}
                                                        @elseif($item instanceof \App\Models\ServiceMedicine) 
                                                            <strong>Medicamento:</strong> {{ $item->medicamento }}
                                                            <strong>Observação:</strong> {{ $item->observation }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <!-- Exemplo de ícone, você pode usar Font Awesome ou outro -->
                                                    <i class="fas fa-info-circle"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach 
                                </div> 
                            </div>
                          
                        </div>  
                </div>
                <br>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>