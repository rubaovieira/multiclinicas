<?php

namespace App\Http\Controllers\SendEmails;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Hash; 
use App\Models\Outbox;   
use Illuminate\Support\Facades\Mail;

class SendEmailsController extends Controller
{
    protected function model()
    {
        return Outbox::class; 
    }

    function sendEmail(Request $request)
    {
        //criar codigo de verificação 
        $verificationCode = rand(10000, 99999);

        // Salvar o código de verificação no banco de dados
        $outbox = new Outbox();
        $outbox->message = $verificationCode;
        $outbox->code = $verificationCode;
        $outbox->from = env('MAIL_FROM_ADDRESS');
        $outbox->to = $request->email;
        $outbox->type = 'email';
        $outbox->active = 1;
        $outbox->save(); 
 
        // Envio do email
        Mail::send('emails.emailpadrao', ['verificationCode' => $verificationCode], function ($message) use ($request) {
            $message->to($request->email) // Email capturado diretamente do request
                    ->subject('Código de Verificação')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));  
        }); 

        return response()->json(['message' => 'Email enviado com sucesso!']);  
    }

    public function verifyEmail(Request $request)
    {
        $outbox = Outbox::where('to', $request->email)
                        ->where('code', $request->code)
                        ->where('active', 1)
                        ->first();

        if ($outbox) {
            $outbox->active = 0; // Desativa o código após verificação
            $outbox->save();
            return response()->json(['message' => 'Código de verificação correto!']);
        } else {
            return response()->json(['message' => 'Código de verificação incorreto!'], 400);
        }
    }

 
}
