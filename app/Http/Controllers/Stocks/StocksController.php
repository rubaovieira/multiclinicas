<?php

namespace App\Http\Controllers\Stocks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Hash; 
use App\Models\Service;
use App\Models\Client; 
use App\Models\Product;
use App\Models\InventoryControl;
use App\Models\HealthPlan;  
use App\Models\ServiceEvolution;
use App\Models\ServiceFile;
use App\Models\UserConfigSchedules;
use App\Models\UserSchedules;
use Mpdf\Mpdf; 
use Illuminate\Support\Facades\Auth;

class StocksController extends Controller
{
    protected function model()
    {
        return Stock::class; 
    }

    public function index()
    {  
        $query = $_GET['query'] ?? '';  

        $stocks = Product::where('active', 1)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->with('inventoryMovements') // Certifique-se de carregar os movimentos de inventário
            ->where(function ($q) use ($query) {
                $q->where('products.name', 'like', "%$query%");
            })
            ->get()
            ->sortBy('current_quantity'); // Ordena pela quantidade atual
    
        return view('stocks.index', compact('stocks'));
    }
    

  
    public function create()
    {
        return view('stocks.new');
    }


    public function edit($id)
    {
        $product = Product::find($id); // Pegue o produto pelo ID
        return view('stocks.new', compact('product', 'id')); // Passe o produto para a view de edição
    }


    public function create_exit()
    {
        $prods = Product::where('active', 1)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->get();
        
        return view('stocks.new_exit', compact('prods'));
    }

    public function create_new(Request $request)
    {
        // Validação dos dados recebidos
        $data = $request->validate([
            'name' => 'required|string', 
            'qtd_min' => 'required|string'
        ]);
    
        if ($request->has('product_id') && $request->product_id) {
            // Editar o produto existente
            $product = Product::find($request->product_id);
            
            if ($product) {
                // Atualizar o produto com os novos dados
                $product->update($data);
                $mensagem = 'Produto atualizado com sucesso';
            } else {
                $mensagem = 'Produto não encontrado';
            }
        } else {
            // Criar um novo produto
            $product = Product::create($data);
            $mensagem = 'Produto criado com sucesso';
        }
    
        // Redirecionar de volta com mensagem de sucesso
        return redirect()->back()->with([
            'mensagem' => $mensagem,
            'toastType' => 'success' // Tipo da mensagem
        ]);
    }
    


    public function create_new_exit(Request $request)
    { 
        $data = $request->validate([
            'product_id' => 'required|string',
            'qtd' => 'required|integer'  
        ]);
        $data['qtd'] = abs($data['qtd']) * -1;
        $item = InventoryControl::create($data);
        return redirect()->back()->withErrors([
            'mensagem' => 'Saida criada com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
        
    }
 

    public function create_entry()
    {
        $prods = Product::where('active', 1)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->get();
        
        return view('stocks.new_entry', compact('prods'));
    }

    public function create_new_entry(Request $request)
    { 
        $data = $request->validate([
            'product_id' => 'required|string',
            'qtd' => 'required|integer'  
        ]);
        $item = InventoryControl::create($data);
        return redirect()->back()->withErrors([
            'mensagem' => 'Entrada criada com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }
 
    public function activate(Request $request)
    {
        $location = Product::findOrFail($request->id);
        $location->active = 1;
        $location->save();

        return redirect()->back()->withErrors([
            'mensagem' => 'Ativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

    public function destroy(Request $request)
    {
        $location = Product::findOrFail($request->id);
        $location->active = 0;
        $location->save();

        return redirect()->back()->withErrors([
            'mensagem' => 'Desativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }
   
    public function index_exit()
    {  
        $query = $_GET['query'] ?? '';  

        $exits = InventoryControl::join('products', 'inventory_controls.product_id', '=', 'products.id')
        ->where('inventory_controls.qtd', '<', 0) 
        ->where('products.clinica_id', Auth::user()->clinica_id)
        ->where(function ($q) use ($query) {
            $q->where('products.name', 'like', "%$query%");
        })
        ->select('inventory_controls.*') // Evita problemas ao selecionar apenas colunas de inventory_controls
        ->paginate(10);
    
        return view('stocks.index_exit', compact('exits'));
    }

    public function index_entry()
    {  
        
        $query = $_GET['query'] ?? '';  

        $entrys = InventoryControl::join('products', 'inventory_controls.product_id', '=', 'products.id')
        ->where('inventory_controls.qtd', '>', 0) 
        ->where('products.clinica_id', Auth::user()->clinica_id)
        ->where(function ($q) use ($query) {
            $q->where('products.name', 'like', "%$query%");
        })
        ->select('inventory_controls.*') // Evita problemas ao selecionar apenas colunas de inventory_controls
        ->paginate(10);
        
        return view('stocks.index_entry', compact('entrys'));
    }

    public function index_product()
    {  
        $query = $_GET['query'] ?? '';  

        $products = Product::where('clinica_id', Auth::user()->clinica_id)
            ->where(function ($q) use ($query) {
                $q->where('products.name', 'like', "%$query%");
            })
            ->orderBy('active', 'desc')
            ->select('products.*')
            ->paginate(10);
        
        return view('stocks.index_products', compact('products'));
    }

    public function destroy_inventory_controls(Request $request)
    {
        $location = InventoryControl::findOrFail($request->id);
        $location->active = 0;
        $location->save();

        return redirect()->back()->withErrors([
            'mensagem' => 'Desativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'

    }

    public function activate_inventory_controls(Request $request)
    {
        $location = InventoryControl::findOrFail($request->id);
        $location->active = 1;
        $location->save();

        return redirect()->back()->withErrors([
            'mensagem' => 'Ativado com sucesso', 
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

   
   
}
