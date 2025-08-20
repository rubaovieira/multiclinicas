<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php 

$date = new \DateTime();
$date->modify('-3 hours');
$formattedDate = $date->format('d/m/Y H:i');

?>
    <title>+ SAÚDE BRASIL-PRONTUARIO DOMICILIAR ({{ $client->name }}) {{ $formattedDate }}.pdf</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
 

        .col {
            width: 50%;
        }

        .page-break {
            page-break-after: always; /* Força quebra de página */
        }

        .align-center {
            text-align: center;
        }

        .lined-text {
            white-space: pre-wrap; /* Preserva quebras de linha */
            line-height: 24px; /* Altura da linha, corresponde ao espaço entre os traços */
            position: relative;
            font-size: 16px; /* Tamanho do texto */
        }

        .lined-text::before {
            content: ''; 
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                to bottom,
                transparent,
                transparent 22px, /* Espaço antes da linha */
                #ccc 22px,        /* Linha começa */
                #ccc 24px         /* Espessura da linha */
            );
            z-index: -1; /* Fundo atrás do texto */
        }


        .table-striped {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table-striped td, .table-striped th {
    border: 1px solid black;
    padding: 8px;
    text-align: center;
}

.table-striped tr:nth-child(even) {
    background-color: #f2f2f2; /* cor de fundo para linhas pares */
}

.table-striped tr:hover {
    background-color: #ddd; /* cor de fundo quando a linha é hover */
}

.table-striped th {
    background-color: #f2f2f2;
    font-weight: bold;
}

    </style>
</head>
<body>
 
    <table>
        <tr>
            <td><b>PACIENTE:</b> {{ $client->name }}</td>
        </tr>
    </table>
 
    @foreach($datas_ministradas_agrupadas->chunk(7) as $data_chunk)
        <table class="table-striped" >
            <tr>
                <td rowspan="2" class='align-center'><b>PRESCRIÇÃO MÉDICA</b></td>
                <td> </td>
                <td class='align-center' colspan={{ $data_chunk->count() }} >Data  </td> 
                
            </tr>
            <tr>
                <td  class='align-center'>VIA</td>
                @foreach($data_chunk as $key => $data)
                <td class='align-center'><b>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $key)->format('d/m') }}</b></td>

                @endforeach
            </tr>
            @foreach($medicamentos as $keym => $medicamento)
                <tr> 
                    <td>{{ $keym }}</td>  
                    <td class="align-center">-</td>  
                    @foreach($data_chunk as $key => $data)
                        @php
                            // Verifica se existe administração do medicamento na data atual
                            $registros = $data->filter(function ($item) use ($keym) {
                                return $item['medicamento'] === $keym;
                            });
                        @endphp
                        <td class="align-center">
                            @foreach($registros as $registro)
                                <div>
                                    {{ $registro['time'] }}
                                    <img src="{{ asset('storage/' . $registro['carimbo']) }}" alt="Carimbo do médico" style="width: 45px;">
                                </div>
                            @endforeach 
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tr>
        </table>
    @endforeach



    <h1>EVOLUÇÕES</h1>

    @foreach($evolucoes->chunk(2) as $chunk) <!-- Agrupa por 2 -->
    <table>
        <tr>
            @foreach($chunk as $key => $evolucao)
            <td class="col">
                <table>
                    <tr>
                        <td>PACIENTE: {{ $client->name }}</td>
                        <td>
                            <div style='border-bottom:1px solid black;'>
                                  DATA: {{ $evolucao->created_at->format('d/m/Y') }}  
                            </div>
                        </td> 
                    </tr>
                    <tr>
                        <td colspan="2" class="align-center" style="padding: 10px;">
                            <b style="font-size:17px;">PLANTÃO</b>
                        </td>
                    </tr>
                    <tr>
                    <td colspan="2" class="lined-text">
                {{$evolucao->evolution_text}}
            </td>
                    </tr>
                    <tr>
                        <td colspan="2" class='align-center' style='padding:10px;'>
              
                            <img src="{{ asset('storage/' . $evolucao->user->carimbo) }}" alt="Carimbo do médico"  style="width: 70px;" id="carimboPreview">
                            <div style='border-top:1px solid black; padding:10px;'>
                                <br>
                                ASSINATURA E CARIMBO DO PROFISSIONAL
                            </div> 
                        </td>
                    </tr>
                </table>
            </td>
            @endforeach
        </tr>
    </table>
    @if(!$loop->last) <!-- Condição para evitar a quebra na última página -->
        <div class="page-break"></div> <!-- Quebra de página -->
    @endif
    @endforeach

    <h1>PROCEDIMENTOS </h1>
    @foreach($procedures->chunk(2) as $chunk) <!-- Agrupa por 2 -->
    <table>
        <tr>
            @foreach($chunk as $key => $procedure)
            <td class="col">
                <table>
                    <tr>
                        <td>PACIENTE: {{ $client->name }}</td>
                        <td>
                            <div style='border-bottom:1px solid black;'>
                                  DATA: {{ $procedure->created_at->format('d/m/Y') }}  
                            </div>
                        </td> 
                    </tr>
                    <tr>
                        <td colspan="2" class="align-center" style="padding: 10px;">
                            <b style="font-size:17px;">PROCEDIMENTO REALIZADO</b>
                        </td>
                    </tr>
                    <tr>
                    <td colspan="2" class="lined-text">
                {{$procedure->procedure->name}}
            </td>
                    </tr>
                    <tr>
                        <td colspan="2" class='align-center' style='padding:10px;'>
              
                            <img src="{{ asset('storage/' . $procedure->user->carimbo) }}" alt="Carimbo do médico"  style="width: 70px;" id="carimboPreview">
                            <div style='border-top:1px solid black; padding:10px;'>
                                <br>
                                ASSINATURA E CARIMBO DO PROFISSIONAL
                            </div> 
                        </td>
                    </tr>
                </table>
            </td>
            @endforeach
        </tr>
    </table>
    @if(!$loop->last) <!-- Condição para evitar a quebra na última página -->
        <div class="page-break"></div> <!-- Quebra de página -->
    @endif
    @endforeach


    <h1>ANEXOS </h1>
    @foreach($anexos->chunk(2) as $chunk) 
    <table>
        <tr>
            @foreach($chunk as $key => $anexo)
            <td class="col">
                <table>
                    <tr>
                        <td>PACIENTE: {{ $client->name }}</td>
                        <td>
                            <div style='border-bottom:1px solid black;'>
                                  DATA: {{ $anexo->created_at->format('d/m/Y') }}  
                            </div>
                        </td> 
                    </tr>
                    <tr>
                        <td colspan="2" class="align-center" style="padding: 10px;">
                            <b style="font-size:17px;">ANEXO</b>
                        </td>
                    </tr>
                    <tr>
                    <td colspan="2" class="lined-text align-center">
                      <a href="{{ asset('storage/' . $anexo->file_path) }}" target='_blank'> 
                            <img src="{{ asset('storage/' . $anexo->file_path) }}" alt="anexo"  style="width: 200px;" id="carimboPreview">
                      </a>
                    </td>
                    </tr>
                    <tr>
                        <td colspan="2" class='align-center' style='padding:10px;'>
                                
                            <img src="{{ asset('storage/' . $anexo->user->carimbo) }}" alt="Carimbo do médico"  style="width: 70px;" id="carimboPreview">
                            <div style='border-top:1px solid black; padding:10px;'>
                                <br>
                                ASSINATURA E CARIMBO DO PROFISSIONAL
                            </div> 
                        </td>
                    </tr>
                </table>
            </td>
            @endforeach
        </tr>
    </table>
    @if(!$loop->last) <!-- Condição para evitar a quebra na última página -->
        <div class="page-break"></div> <!-- Quebra de página -->
    @endif
    @endforeach

</body>
</html>
