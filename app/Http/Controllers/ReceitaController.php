<?php

namespace App\Http\Controllers;

use App\Models\Receita;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceitaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'receita_text' => 'required|string',
        ]);

        $receita = new Receita();
        $receita->service_id = $request->service_id;
        $receita->receita_text = $request->receita_text;
        $receita->created_by = Auth::id();
        $receita->save();

        return redirect()->back()->with('success', 'Receita adicionada com sucesso!');
    }

    public function destroy($id)
    {
        $receita = Receita::findOrFail($id);

        // Verifica se o usuário tem permissão para excluir
        if (Auth::user()->perfil !== 'admin' && Auth::user()->perfil !== 'medico') {
            return back()->with('error', 'Você não tem permissão para excluir esta receita.');
        }

        $receita->deleted_by = Auth::id();
        $receita->save();
        $receita->delete();

        return redirect()->back()->with('success', 'Receita excluída com sucesso!');
    }

    public function print($id)
    {
        $receita = Receita::with(['service.client', 'createdBy'])->findOrFail($id);
        $receita->service->client->date_birth = Carbon::parse($receita->service->client->date_birth)->format('d/m/Y');

        // Dados do médico (usuário que criou a receita)
        $medico = $receita->createdBy;

        // Renderiza a view como HTML
        $html = view('services.print_receita', compact('receita', 'medico'))->render();

        // Gera o PDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P', // Retrato
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('receita.pdf', 'I'); // 'I' para abrir no navegador
    }
}
