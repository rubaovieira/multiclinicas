<?php

namespace App\Http\Controllers\HealthPlans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Hash; 
use App\Models\Procedure;
use App\Models\HealthPlan;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class HealthPlansController extends Controller
{
    protected function model()
    {
        return HealthPlan::class; 
    }

    public function index()
    {  
        $query = $_GET['query'] ?? '';  
        $health_plans = HealthPlan::query() 
        ->select('*')   
        ->where(function($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        }) 
        ->where('clinica_id', Auth::user()->clinica_id)
        ->orderBy('active','desc')
        ->orderBy('name')
        ->paginate(10);   
        
        return view('health_plans.health_plans', compact('health_plans'));
    }

    public function create(Request $request)
    {
          
        $service = new HealthPlan();
        $service->name = $request->name;  
        $service->save();

        return redirect()->route('health_plans')->withErrors([
            'mensagem' => 'Convênio criado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
 
    }

    public function update(Request $request, $id)
    {  
        $item = $this->model()::findOrFail($request->id);
        $item->name = $request->name;
        $item->save();
        
        return redirect()->route('health_plans')->withErrors([
            'mensagem' => 'Convênio atualizado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }



    public function show(Request $request)
    { 
        $health_plan = HealthPlan::findOrFail($request->id);  
        $clinica = $health_plan->clinica;

        return view('health_plans.edit', compact('health_plan', 'clinica'));
    }

    public function destroy(Request $request)
    {
        $location = HealthPlan::findOrFail($request->id);
        $location->active = 0;
        $location->save();

        return redirect()->route('health_plans')->withErrors([
            'mensagem' => 'Convênio desativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

    public function activate(Request $request)
    {
        $location = HealthPlan::findOrFail($request->id);
        $location->active = 1;
        $location->save();

        return redirect()->route('health_plans')->withErrors([
            'mensagem' => 'Convênio ativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }


    public function new()
    { 
        $clinica = Auth::user()->clinica;
        return view('health_plans.new', compact('clinica'));
    }


    protected function validationRules()
    {
        return [
            'name' => 'required|string',  
        ];
    }
 
}
