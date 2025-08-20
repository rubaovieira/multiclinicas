<?php

namespace App\Http\Controllers\Procedures;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Hash; 
use App\Models\Procedure;
use App\Models\HealthPlan;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ProceduresController extends Controller
{
    protected function model()
    {
        return Procedure::class; 
    }

    public function index()
    {  
        $query = $_GET['query'] ?? '';  
        $procedures = Procedure::query() 
        ->select('*')   
        ->where('clinica_id', Auth::user()->clinica_id)
        ->where(function($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        }) 
        ->orderBy('active','desc')
        ->orderBy('name')
        ->paginate(10);   
        
        return view('procedures.procedures', compact('procedures'));
    }

    public function create(Request $request)
    {
          
        $service = new Procedure();
        $service->name = $request->name; 
        $service->preparo = '';
        $service->save();

        return redirect()->route('procedures')->withErrors([
            'mensagem' => 'Procedimento ativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
 
    }

    public function update(Request $request, $id)
    {  
        $item = $this->model()::findOrFail($request->id);
        $item->name = $request->name;
        $item->save();
        
        return redirect()->route('procedures')->withErrors([
            'mensagem' => 'Procedimento atualizado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }



    public function show(Request $request)
    { 
        $procedure = Procedure::findOrFail($request->id);  

        return view('procedures.edit', compact('procedure'));
    }

    public function destroy(Request $request)
    {
        $location = Procedure::findOrFail($request->id);
        $location->active = 0;
        $location->save();

        return redirect()->route('procedures')->withErrors([
            'mensagem' => 'Procedimento desativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

    public function activate(Request $request)
    {
        $location = Procedure::findOrFail($request->id);
        $location->active = 1;
        $location->save();

        return redirect()->route('procedures')->withErrors([
            'mensagem' => 'Procedimento ativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }


    public function new()
    {
   
        return view('procedures.new');
    }


    protected function validationRules()
    {
        return [
            'name' => 'required|string', 
            'address' => 'string',
            'telephone' => 'string',
            'caregiver_responsible' => 'string',
            'date_birth' => 'date',
            'cpf' => 'string',
            'diagnosis' => 'string',
            'health_plan_id' => 'string'  
        ];
    }
 
}
