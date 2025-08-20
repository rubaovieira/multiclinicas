<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        
        $clinicas = Clinica::query()
            ->when($query, function($q) use ($query) {
                return $q->where('nome', 'like', "%{$query}%");
            })
            ->orderBy('nome')
            ->paginate(10);

        return view('master.clinicas', compact('clinicas'));
    }

    public function create()
    {
        return view('master.clinica-form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        Clinica::create($request->all());

        return redirect()->route('clinics')->with('success', 'Clínica criada com sucesso!');
    }

    public function edit($id)
    {
        $clinica = Clinica::findOrFail($id);
        return view('master.clinica-form', compact('clinica'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $clinica = Clinica::findOrFail($id);
        $clinica->update($request->all());

        return redirect()->route('clinics')->with('success', 'Clínica atualizada com sucesso!');
    }

    public function deactivate($id)
    {
        $clinica = Clinica::findOrFail($id);
        $clinica->update(['status' => 'inativo']);

        return redirect()->route('clinics')->with('success', 'Clínica desativada com sucesso!');
    }

    public function activate($id)
    {
        $clinica = Clinica::findOrFail($id);
        $clinica->update(['status' => 'ativo']);

        return redirect()->route('clinics')->with('success', 'Clínica ativada com sucesso!');
    }

    public function destroy($id)
    {
        $clinica = Clinica::findOrFail($id);
        $clinica->delete();

        return redirect()->route('clinics')->with('success', 'Clínica excluída com sucesso!');
    }

    public function restore($id)
    {
        $clinica = Clinica::withTrashed()->findOrFail($id);
        $clinica->restore();

        return redirect()->route('clinics')->with('success', 'Clínica restaurada com sucesso!');
    }
} 