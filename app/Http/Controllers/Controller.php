<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

abstract class Controller
{
    public static function sendZap($phone, $message)
    {
        // Formatar o número de telefone
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 11) {
            $phone = '55' . $phone;
        }

        // Preparar a mensagem
        $message = "*{$message}*";

        // Configurar a chamada para a API do WhatsApp
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.z-api.io/instances/3E11803E9DB6706EF63BDABA2DE1C2A1/token/1606CCA07B98C1B3D49C97ED/send-text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "phone" => $phone,
                "message" => $message
            ]),
            CURLOPT_HTTPHEADER => array(
                "client-token: F249eb64e28f5473c86b7b62814bd85abS",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::error('Erro ao enviar mensagem via WhatsApp: ' . $err);
            return false;
        }

        return true;
    }
}
