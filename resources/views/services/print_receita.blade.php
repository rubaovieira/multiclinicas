<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Receita Médica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }

        .container {
            border: 1px solid #000;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        td {
            padding: 8px;
            border: 1px solid #000;
        }

        .assinatura {
            height: 60px;
            border-bottom: 1px solid #000;
            width: 250px;
            margin: 0 auto 5px auto;
            display: block;
        }

        .footer {
            clear: both;
            margin-top: 100px;
            text-align: center;
        }

        .footer-left {
            display: none;
        }

        .footer-right {
            width: 100%;
            text-align: center;
        }

        .small-text {
            font-size: 12px;
            margin-top: 2px;
            text-align: center;
        }

        .carimbo {
            text-align: center;
            margin-bottom: 0;
            position: relative;
            top: -30px;
        }

        .carimbo img {
            height: 80px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h5>RECEITA MÉDICA</h5>
        </div>

        <table>
            <tr>
                <td colspan="4"><strong>IDENTIFICAÇÃO</strong></td>
            </tr>
            <tr>
                <td colspan="4">
                    {{ $medico->name }}<br>
                    CRM - {{ $medico->advice ?? '----' }} | CPF - {{ $medico->cpf ?? '-' }}<br>
                    {{-- {{ $medico->address ?? '-' }} &nbsp; Tel: {{ $medico->telephone ?? '-' }} --}}
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td><strong>PACIENTE:</strong> {{ $receita->service->client->name }}
                    <br>
                    <strong>CPF:</strong> {{ $receita->service->client->cpf ?? '-' }} | <strong>Data de Nascimento:</strong>
                    {{ $receita->service->client->date_birth ?? '-' }}
                    <br>
                    <strong>Endereço:</strong> - {{ $receita->service->client->address ?? '-' }}

                </td>
            </tr>
           
            <tr>
                <td><strong>PRESCRIÇÃO:</strong> {{ $receita->receita_text }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="footer-left">
                <p><strong>{{ $medico->city ?? 'Cidade' }}</strong>, {{ date('d/m/Y') }}</p>
            </div>
            <div class="footer-right">
                @if ($medico->carimbo)
                    <div class="carimbo">
                        <img src="{{ public_path('storage/' . $medico->carimbo) }}" alt="Carimbo do médico"
                            width="100px">
                    </div>
                @endif
                <div class="assinatura"></div>
                <p class="small-text">Assinatura e carimbo do médico</p>
                <p class="small-text">{{ $medico->name }} - CRM {{ $medico->advice ?? '-' }}</p>
                <p class="small-text"> {{ date('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>

</html>
