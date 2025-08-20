<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Hash; 
use App\Models\Service;
use App\Models\Client;
use App\Models\ServiceMedicine;
use App\Models\InventoryControl;
use App\Models\ServiceProcedure;
use App\Models\Procedure;
use App\Models\HealthPlan;
use App\Models\ServiceMedicineTimes;
use App\Models\ServiceMedicineTimeMinistereds;
use App\Models\ServiceEvolution;
use App\Models\ServiceFile;
use App\Models\UserConfigSchedules;
use App\Models\UserSchedules;
use Mpdf\Mpdf; 
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{
    protected function model()
    {
        return Service::class; 
    }

    public function index()
    {  
        $query = $_GET['query'] ?? '';  
        $status = $_GET['status'] ?? '';
        $data = $_GET['data'] ?? '';
        $services = Service::with('client') // Carrega os dados do cliente relacionados
        ->join('clients', 'services.client_id', '=', 'clients.id') // Realiza o join com a tabela clients
        ->where('services.clinica_id', Auth::user()->clinica_id)
        ->where(function ($q) use ($query) {
            $q->where('clients.name', 'like', "%$query%")
              ->orWhere('clients.address', 'like', "%$query%")
              ->orWhere('clients.date_birth', 'like', "%$query%")
              ->orWhere('clients.caregiver_responsible', 'like', "%$query%")
              ->orWhere('clients.cpf', 'like', "%$query%");
        })
        ->where(function ($q) use ($data) {
            if ($data != '') {
                $q->where('services.created_at', 'like', "%$data%");
            }
        })
        ->where(function ($q) use ($status) {
            if ($status != '') {
                $q->where('services.status', $status);
            }
        })
        ->select('services.*') // Seleciona todos os campos da tabela services
        ->orderBy('services.status', 'asc') // Ordena pelo status
        ->orderBy('services.created_at', 'desc') // Ordena pela data de criação
        ->paginate(10); // Pagina os resultados
    
        $procedures = Procedure::all()
        ->where('active', 1)
        ->where('clinica_id', Auth::user()->clinica_id);
       
        
        return view('services.index', compact('services', 'procedures'));
    }

  
    public function create()
    {

        $clients = Client::all()->where('clinica_id', Auth::user()->clinica_id); 
        $client_id = request()->input('id');
 
        return view('services.new', compact('clients', 'client_id'));
    }

    public function create_new(Request $request)
    {
        $request->merge(['status' => 'EM ANDAMENTO']);
        $data = $request->validate([
            'client_id' => 'required|string', 
            'status' => 'required|string', 
            'diagnostico' => 'string',
        ]);

        $item = Service::create($data);
 
        return redirect()->route('service-client', ['id' => $item->client_id])->withErrors([
            'mensagem' => 'Serviço criado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
        
    }

    public function update(Request $request, $id)
    { 
        $date = explode('/', $request->date_birth);  
        $request->merge(['date_birth' => $date[2].'-'.$date[1].'-'.$date[0]]); 

        $data = $request->validate($this->validationRules());
        $item = $this->model()::findOrFail($request->id);
        $item->update($data); 

        return redirect()->route('clients')->withErrors([
            'mensagem' => 'cliente atualizado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }
 

    public function show(Request $request)
    { 
        $services_client =  Service::with('receitas') 
        ->where('client_id', $request->id)
        ->orderby('status', 'asc')
        ->orderby('created_at', 'desc')
        ->get();   

        $client = Client::findOrFail($request->id);

        $procedures = Procedure::all()
        ->where('active', 1);


        $stocks = Product::where('active', 1)
        ->with('inventoryMovements') 
        ->get()
        ->sortBy('current_quantity');  
        

        // dd($services_client);
       
        return view('services.services_client', compact('services_client', 'client', 'procedures', 'stocks'));
    }
 
    public function activate(Request $request)
    {
        $location = Client::findOrFail($request->id);
        $location->active = 1;
        $location->save();

        return redirect()->route('clients')->withErrors([
            'mensagem' => 'Cliente ativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }
 
    public function service_medicine_add(Request $request){

        $nextTimes = json_decode($request->input('next_times'), true);
      
        $data = $request->validate([
            'service_id' => 'required|string', 
            'product_id' => 'required|string', 
            'observation' =>  'nullable|string', 
            'posology' => 'required|string',
            'start_time' => 'required|string',
        ]);

        $data['medicamento'] = Product::findOrFail($data['product_id'])->name;
 
        $item = ServiceMedicine::create($data);

        foreach ($nextTimes as $time) { 
            ServiceMedicineTimes::create([
                'service_medicine_id' => $item->id,
                'time' => $time,
                'active' => 1
            ]);
        }    
      
        $client_id = Service::findOrFail($request->service_id)->client_id;
  
        return back()->withErrors([
            'mensagem' => 'Procedimento adicionado com sucesso', 
        ])->with('toastType', 'success'); //'success', 'warning', 'danger'
    }

    public function service_procedure_add(Request $request){
        $data = $request->validate([
            'service_id' => 'required|string', 
            'procedure_id' => 'required|string', 
            'observation' => 'nullable|string',
        ]);

        $item = ServiceProcedure::create($data);

        $client_id = Service::findOrFail($request->service_id)->client_id;
  
        return back()->withErrors([
            'mensagem' => 'Procedimento adicionado com sucesso', 
        ])->with('toastType', 'success'); //'success', 'warning', 'danger'
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

    function service_history(Request $request){
 
        $service = Service::findOrFail($request->service_id); 
        $items = $service->service_items();
        $client = $service->client; 
        return view('services.service_history', compact('items', 'client', 'service'));
    }

    function finish(Request $request){ 
        $service = Service::findOrFail($request->id); 
        $service->status = 'FINALIZADO';
        $service->save();  
    }

    function open(Request $request){
        $service = Service::findOrFail($request->id); 
        $service->status = 'EM ANDAMENTO';
        $service->save(); 
    }

    public function destroy(Request $request, $id)
    {
        $type = $request->input('type');
        $operator = auth()->user()->id; // Você pode pegar o nome do usuário logado ou o ID

        if ($type === 'procedure') {
            $item = \App\Models\ServiceProcedure::findOrFail($id);
        } elseif ($type === 'medicine') {
            $item = \App\Models\ServiceMedicine::findOrFail($id);
        } elseif ($type === 'evolution') { 
            $item = \App\Models\ServiceEvolution::findOrFail($id);
        }elseif ($type === 'file') { 
            $item = \App\Models\ServiceFile::findOrFail($id); 
        }else {
            return redirect()->back()->with('error', 'Tipo de item inválido.');
        }

        // Atualiza o status do item para "inativo"
        $item->active = false; // Ou true, dependendo do que você quer 
        $item->deleted_at = now(); // Define a hora da exclusão 
        $item->deleted_by = $operator; // Define o operador que fez a exclusão   
        $item->save();

        return redirect()->back()->with('success', 'Item excluído com sucesso!');
    }

    public function service_medicine_minister(Request $request)
    { 

        //ver escala para ver se o usuario está autorizado
        $user_schedule = UserConfigSchedules::where('user_id', auth()->id())->where('active', 1)->first();
        if($user_schedule){ 

            $date = new \DateTime(); // Prefixo "\" para usar a classe do namespace global
            $date->sub(new \DateInterval('PT3H')); // Prefixo também para DateInterval
            $time_minus_3h = $date->format('H:i'); // Formata para H:i
              
            $dias = [
               1 => '1 - Segunda', 
               2 => '2 - Terça', 
               3 => '3 - Quarta', 
               4 => '4 - Quinta', 
               5 => '5 - Sexta', 
               6 => '6 - Sábado', 
               7 => '7 - Domingo']; 

            $UserSchedules = UserSchedules::where('user_config_schedule_id', $user_schedule->id)
            ->where('day', $dias[date('w')])
            ->where('active', 1)
            ->where('start', '<=', $time_minus_3h)
            ->where('end', '>=', $time_minus_3h)
            ->first(); 
    
            if(!$UserSchedules){  
                return redirect()->back()->withErrors([
                    'mensagem' => 'Você não está autorizado a ministrar medicamentos neste horário', 
                ])->with('toastType', 'warning');   //'success', 'warning', 'danger'
            } 
        } 

        $ministered = ServiceMedicineTimeMinistereds::create([
            'service_medicine_time_id' => $request->service_medicine_time_id, 
            'late' => false,
            'qtd' => $request->qtd,
            'description' => $request->observation,
            'active' => 1
        ]);  

        $serviceMedicineTime = ServiceMedicineTimes::findOrFail($request->service_medicine_time_id);
       
        $ministered_id = $ministered->id; 

        if($serviceMedicineTime->serviceMedicine->product_id){
            $data['service_medicine_time_ministered_id'] = $ministered_id;
            $data['product_id'] = $request->product_id;
            $data['qtd'] = $request->qtd * -1;
            $data['product_id'] = $serviceMedicineTime->serviceMedicine->product_id;
            InventoryControl::create($data);
        }

        return redirect()->back()->with('success', 'Medicamento ministrado com sucesso!');
    }

    public function service_evolution_add(Request $request)
    {
        $service = Service::findOrFail($request->service_id);
        $qtd  = ServiceEvolution::where('service_id', $service->id)->where('created_by', auth()->id())->count();
        if($qtd >= $service->limit_evolution && $service->limit_evolution != 0){
          return back()->withErrors([
            'mensagem' => 'Você atingiu o limite de evoluções para este atendimento', 
          ])->with('toastType', 'danger'); //'success', 'warning', 'danger'
        }
 
        $data = $request->validate([
            'service_id' => 'required|string', 
            'evolution_text' => 'required|string', 
        ]);

        $item = ServiceEvolution::create($data);
        return back()->withErrors([
            'mensagem' => 'Evolução adicionada com sucesso', 
        ])->with('toastType', 'success'); //'success', 'warning', 'danger'
    }

    public function service_attachment(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|string',  
        ]);
        
       
            // Verifica se há arquivos enviados
            if ($request->hasFile('filepond')) {
                // Obtém os arquivos enviados
                $files = $request->file('filepond'); 
        
                // Processa cada arquivo
                foreach ($files as $file) {
                    // Verifica se o arquivo é válido
                    if ($file->isValid()) {
                        // Gera um nome único para o arquivo
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        
                        // Define o caminho onde o arquivo será armazenado
                        $path = 'imagens/anexos/' . $request->service_id; // Ajuste o caminho conforme necessário
                        
                        // Salva o arquivo no diretório especificado
                        $filePath = $file->storeAs($path, $fileName, 'public'); // 'public' é o disco padrão para armazenamento público
        
                        // Cria um novo registro na tabela ServiceFile
                       $file_insert = ServiceFile::create([
                            'service_id' => $request->service_id,
                            'file_name' => $fileName,
                            'file_path' => $filePath, // O caminho do arquivo salvo
                            'file_extension' => $file->getClientOriginalExtension(),
                            'active' => 1,
                            'created_at' => now(), // Ajuste o campo created_at
                            'updated_at' => now(), // Ajuste o campo updated_at
                            'created_by' => auth()->id(), // Caso você queira adicionar o ID do usuário logado
                        ]);
                    }
                }
        
                // Retorna uma resposta de sucesso
                return response()->json(['message' => 'Arquivos enviados com sucesso!', 'id' => $file_insert->id], 200);
            }
        
            // Se não houver arquivos, retorna erro
            return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
       
         
    }

    public function delete_attachment(Request $request)
    {
       
    

        $fileId = $request->json('id'); 
        $file = ServiceFile::find($fileId);
        if ($file) {
            // Realiza a ação desejada, como marcar como inativo
            $file->active = 0;
            $file->save();
        } else {
            // Se não encontrar o arquivo
            return response()->json(['error' => 'Arquivo não encontrado.'], 404);
        }

        return response()->json(['message' => 'Arquivos excluirdo com sucesso!'], 200);
    }

    function print(Request $request){ 
        $item = Service::findOrFail($request->id); 
        $datas_service_medicines = $item->service_medicines()->get()->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('d/m/Y');
        })->sortKeys();

        $groupedTimes = $item->service_medicines()->get()->flatMap(function ($item) {
            // Pluck para pegar apenas os horários
            return $item->serviceMedicineTimes->pluck('time');
        })->unique()->sort()->values();  // Remove duplicados, ordena e remove as chaves numéricas
          
        $serviceMedicines = $item->service_medicines()->with('serviceMedicineTimes')->get();
 
        $datas_ministradas_agrupadas = $item->service_medicines()->get()->flatMap(function ($serviceMedicine) {
            return $serviceMedicine->serviceMedicineTimes->flatMap(function ($serviceMedicineTime) use ($serviceMedicine) {
                return $serviceMedicineTime->serviceMedicineTimeMinisteredsAll->map(function ($ministered) use ($serviceMedicineTime, $serviceMedicine) {
                    return [
                        'medicamento' => $serviceMedicine->medicamento,  // Medicamento
                        'date' => \Carbon\Carbon::parse($ministered->created_at),  // Data como objeto Carbon
                        'time' => $serviceMedicineTime->time,  // Hora
                        'qtd' => $ministered->qtd,  // Quantidade
                        'description' => $ministered->description,  // Descrição
                        'carimbo' => $ministered->user->carimbo ?? null,  // Carimbo
                    ];
                });
            });
        });
        
        // Agrupar por data no formato 'Y-m-d' e ordenar
        $datas_ministradas_agrupadas = $datas_ministradas_agrupadas->groupBy(function ($item) {
            return $item['date']->format('Y-m-d');  // Agrupar por 'Y-m-d'
        })->map(function ($group) {
            // Ordenar cada grupo pela data
            return $group->sortBy('date');
        })->sortKeys();  // Ordenar as chaves (datas) do grupo em ordem crescente
        
 

          
          $datas_e_carimbos = $item->service_medicines()->get()->flatMap(function ($serviceMedicine) {
            return $serviceMedicine->serviceMedicineTimes->flatMap(function ($serviceMedicineTime) use ($serviceMedicine) {
                return $serviceMedicineTime->serviceMedicineTimeMinisteredsAll->map(function ($ministered) use ($serviceMedicineTime, $serviceMedicine) {
                    return [
                        'date' => \Carbon\Carbon::parse($ministered->created_at)->format('d/m/Y'),  // Data de criação do ministrado
                        'carimbo' => $ministered->user->carimbo ?? null, // Carimbo do médico
                    ];
                });
            });
        })->groupBy('date')  // Agrupar por data
          ->map(function ($group) {
                // Remover carimbos duplicados dentro de uma mesma data
                return $group->unique('carimbo');
          })
          ->sortKeys();  // Ordenar as chaves (datas) em ordem crescente
   


        $evolucoes = $item->service_evolutions()->get()->sortKeys();
 
        $client = $item->client; 

        //agrupar todos os medicamentos
        $medicamentos = $item->service_medicines()->get()->groupBy('medicamento');

        $procedures  = $item->service_procedures()->get();
 

        $anexos = $item->service_files()->get();
 
      
        // Carregar a view do relatório
        $pdfView = view('reports.reports-atendimento', compact('item', 'procedures', 'anexos',  'medicamentos', 'client',  'evolucoes', 'datas_service_medicines', 'groupedTimes', 'datas_ministradas_agrupadas', 'datas_e_carimbos'))->render();

            // Gerar o PDF com o conteúdo da view
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',   // Certifique-se de definir o modo correto de codificação
            'format' => 'A4',     // Definir o formato do papel, pode ser 'A4' ou outro
            'orientation' => 'L', // Definir a orientação como paisagem ('L' para landscape)
        ]);
         
         // Subtrai 3 horas da data atual
        $date = new \DateTime();
        $date->modify('-3 hours');

        // Formata a data para o padrão desejado (j-m-Y H:i)
        $formattedDate = $date->format('d/m/Y H:i');

        // Define o rodapé com a data ajustada
        $mpdf->SetFooter('Relatório gerado em: ' . $formattedDate);

        $mpdf->WriteHTML($pdfView);

        // Gerar o arquivo PDF e retornar para o usuário
        return $mpdf->Output('relatorio.pdf', 'I'); // 'I' para exibir no navegador, 'D' para forçar o download
        
    }

    function qtd_evolution(Request $request){
        $service = Service::findOrFail($request->id);
        $service->limit_evolution = $request->qtd;
        $service->save();  
        return response()->json(['message' => 'Quantidade de evolução salva!'], 200);
    }
}
