 
 @extends('layouts.app')

@section('title', 'Cadastro de Escala') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">   
            
                <form action="{{ route('schedule_update', ['id' => $user->id ]) }}" method="POST">
                    @csrf
                    <h5><u>Quadro de disponiblidade </u></h5>
                    <?php
                    $dias = [
                        '1 - Segunda' => 'Segunda', 
                        '2 - Terça' => 'Terça', 
                        '3 - Quarta' => 'Quarta', 
                        '4 - Quinta' => 'Quinta', 
                        '5 - Sexta' => 'Sexta', 
                        '6 - Sábado' => 'Sábado', 
                        '7 - Domingo' => 'Domingo']; 
                    ?>
                    <div class="row">
                        @foreach($dias as $key => $dia) 
                            <div class="col-lg-4">  
                                <div>
                                    <b>{{$dia}}</b>  <input type="checkbox" class="my-checkbox" name="marcado[{{$key}}]" id="checkbox{{'-'.str_replace(' ','', $key)}}" <?php echo ($dia === "Domingo") ?  '': 'checked' ?> >  
                                </div>
                                <div>
                                    <b class="text-red alert-danger p-1" id="error{{'-'.str_replace(' ','', $key)}}" style="display: none;"   >
                                        mostrar erros aqui
                                    </b>
                                </div> 
                                <table class="table table-bordered table-striped text-center">
                                    <thead class="thead-light" style="background-color:red;">
                                        <tr>
                                            <th>Turno</th>
                                            <th>Início</th>
                                            <th>Fim</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Manhã</td>
                                            <td>
                                                <input type="text" id="inicio_manha{{'-'.str_replace(' ','', $key)}}" name="inicio_manha[{{$key}}]" class="form-control horarioiniciofim time" onchange="verificarconflito('inicio_manha','{{$key}}','fim_manha')" value="08:00">
                                            </td>
                                            <td>
                                                <input type="text" id="fim_manha{{'-'.str_replace(' ','', $key)}}" name="fim_manha[{{$key}}]" class="form-control horarioiniciofim time" onchange="verificarconflito('fim_manha','{{$key}}','inicio_manha')" value="12:00">
                                            </td>
                                            <td style="cursor: pointer; text-align: center; vertical-align: middle; padding: 2px !important;"  onclick="limparhorario('manha','{{$key}}')">
                                                <img src="https://cdn.icon-icons.com/icons2/37/PNG/512/broom_4488.png" alt="" width="25px">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tarde/Noite</td>
                                            <td>
                                                <input type="text" id="inicio_tarde{{'-'.str_replace(' ','', $key)}}" name="inicio_tarde[{{$key}}]" class="form-control horarioiniciofim time" onchange="verificarconflito('inicio_tarde','{{$key}}','fim_tarde')" value="13:00">
                                            </td>
                                            <td>
                                                <input type="text" id="fim_tarde{{'-'.str_replace(' ','', $key)}}"  name="fim_tarde[{{$key}}]" class="form-control horarioiniciofim time" onchange="verificarconflito('fim_tarde','{{$key}}','inicio_tarde')" value="17:00">
                                            </td>
                                            <td style="cursor: pointer; text-align: center; vertical-align: middle; padding: 2px !important;" onclick="limparhorario('tarde','{{$key}}')">
                                                <img  src="https://cdn.icon-icons.com/icons2/37/PNG/512/broom_4488.png" alt="" width="25px" >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table> 
                            </div> 
                        @endforeach  
                    </div>  
                    <br>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form> 
        </div>
    </div>
</div> 


@section('scripts')


<script>
     function verificarconflito(atual, dia, aposto){
        var id = id;
        if(atual == 'inicio_manha' || atual == 'fim_manha'){
            var diaFormatado = dia.replace(/ /g, '');
            var inicioManha = $("#inicio_manha-" + diaFormatado).val();
            var fimManha = $("#fim_manha-" + diaFormatado).val(); 
 
            if(inicioManha !== '' && fimManha !== '' && inicioManha >= fimManha){ 
                $("#error-" + diaFormatado).show();
                $("#inicio_manha-" + diaFormatado).focus(); 
                $("#error-" + diaFormatado).text('O horário de início deve ser menor que o horário de fim'); 
            }else{
                var inicioTarde = $("#inicio_tarde-" + diaFormatado).val();
                var fimTarde = $("#fim_tarde-" + diaFormatado).val();
                
                if (inicioManha !== '' && fimManha !== '' && inicioTarde !== '' && fimTarde !== '') {
                    if (fimManha > inicioTarde) {
                        $("#error-" + diaFormatado).show();
                        $("#inicio_tarde-" + diaFormatado).focus(); 
                        $("#error-" + diaFormatado).text('Os horários de manhã e tarde não podem conflitar');
                    } else {
                        $("#error-" + diaFormatado).hide();
                        $("#error-" + diaFormatado).text('');
                    }
                } else{
                    $("#error-" + diaFormatado).hide();
                    $("#error-" + diaFormatado).text('');
                }
            }  

        }

        if (atual == 'inicio_tarde' || atual == 'fim_tarde') {
            var diaFormatado = dia.replace(/ /g, '');
            
            var inicioTarde = $("#inicio_tarde-" + diaFormatado).val();
            var fimTarde = $("#fim_tarde-" + diaFormatado).val();
            
            if (inicioTarde !== '' && fimTarde !== ''  && inicioTarde >= fimTarde) {
                $("#error-" + diaFormatado).show();
                $("#inicio_tarde-" + diaFormatado).focus(); 
                $("#error-" + diaFormatado).text('O horário de início deve ser menor que o horário de fim');
            } else {
                // Verificar se os horários de manhã e tarde estão conflitando
                var inicioManha = $("#inicio_manha-" + diaFormatado).val();
                var fimManha = $("#fim_manha-" + diaFormatado).val();
                
                if (inicioManha !== '' && fimManha !== '' && inicioTarde !== '' && fimTarde !== '') {
                    if (fimManha > inicioTarde) {
                        $("#error-" + diaFormatado).show();
                        $("#inicio_tarde-" + diaFormatado).focus(); 
                        $("#error-" + diaFormatado).text('Os horários de manhã e tarde não podem conflitar');
                    } else {
                        $("#error-" + diaFormatado).hide();
                        $("#error-" + diaFormatado).text('');
                    }
                } else{
                    $("#error-" + diaFormatado).hide();
                    $("#error-" + diaFormatado).text('');
                } 
            }  
        } 

    }

    $(document).ready(function(){    
        buscarhorariosabertos()
    });

    function buscarhorariosabertos(){
        
            $.ajax({
                url: '{{ route('buscar.horarios') }}',
                type: "GET",
                async: true,
                data: { 
                    user_id: '{{ $user->id }}'
                },
                success: function (data) {  
                    if(JSON.parse(data).length > 0){
                        $('.my-checkbox').prop('checked', false);
                        $('.horarioiniciofim').val('');
                        
                        JSON.parse(data).forEach(function(item){    
                            $("#checkbox-"+(item.day).replace(/ /g, '')).prop('checked', true);
                            if(item.turn == "inicio_manha"){
                                $("#inicio_manha"+"-"+(item.day).replace(/ /g, '')).val(item.start);  
                                $("#fim_manha"+"-"+(item.day).replace(/ /g, '')).val(item.end);  
                            } 
                            if(item.turn == "inicio_tarde"){
                                $("#inicio_tarde"+"-"+(item.day).replace(/ /g, '')).val(item.start);  
                                $("#fim_tarde"+"-"+(item.day).replace(/ /g, '')).val(item.end);
                            }   
                        });
                    }
                    
                }
            }); 
        } 

        function limparhorario(turno, dia){
            if(turno == 'manha'){
                $("#inicio_manha"+"-"+dia.replace(/ /g, '')).val('');  
                $("#fim_manha"+"-"+dia.replace(/ /g, '')).val('');  
            } 
            if(turno == 'tarde'){
                $("#inicio_tarde"+"-"+dia.replace(/ /g, '')).val('');  
                $("#fim_tarde"+"-"+dia.replace(/ /g, '')).val('');
            } 
           
        }
</script>

@endsection

@endsection