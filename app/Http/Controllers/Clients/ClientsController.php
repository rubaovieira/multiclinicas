<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Clinica;
use App\Models\HealthPlan;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientsController extends Controller
{
    protected function model()
    {
        return Client::class;
    }

    public function index()
    {
        $query = $_GET['query'] ?? '';
        $clients = Client::query()->where('clinica_id', Auth::user()->clinica_id)
            ->select('*')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('address', 'like', "%$query%")
                    ->orWhere('date_birth', 'like', "%$query%")
                    ->orWhere('caregiver_responsible', 'like', "%$query%")
                    ->orWhere('cpf', 'like', "%$query%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('clients.clients', compact('clients'));
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->clinica_id = Auth::user()->clinica_id;
        $user->perfil = 'cliente';
        $user->save();

        $date = explode('/', $request->date_birth);
        $request->merge(['date_birth' => $date[2] . '-' . $date[1] . '-' . $date[0]]);

        $data = $request->validate($this->validationRules());
        $item = $this->model()::create($data);

        $item->user_id = $user->id;
        $item->save();

        //adicionar também um servico para o cliente
        $service = new Service();
        $service->client_id = $item->id;
        $service->status = 'EM ANDAMENTO';
        $service->diagnostico = $request->diagnosis;
        $service->save();





        //redirecionar para service-client/{id}
        return redirect()->route('service-client', ['id' => $item->id])->withErrors([
            'mensagem' => 'cliente criado com sucesso',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'

    }

    public function update(Request $request, $id)
    {
        $date = explode('/', $request->date_birth);
        $request->merge(['date_birth' => $date[2] . '-' . $date[1] . '-' . $date[0]]);

        $data = $request->validate($this->validationRules());
        $item = $this->model()::findOrFail($request->id);
        $item->update($data);

        // 🔁 Atualiza o user vinculado, se existir
        if ($item->user_id) {
            $user = \App\Models\User::find($item->user_id);
            if ($user) {
                $userData = [
                    'name' => $item->name,
                    'email' => $request->email,
                    'cpf' => $item->cpf,
                    'address' => $item->address,
                    'telephone' => $item->telephone,
                ];

                // Se uma nova senha foi fornecida, atualiza
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);
            }
        }

        return redirect()->route('clients')->withErrors([
            'mensagem' => 'cliente atualizado com sucesso',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }



    public function show(Request $request)
    {
        $client = Client::with('user')->findOrFail($request->id);
        $health_plans = HealthPlan::all()->where('clinica_id', Auth::user()->clinica_id);

        return view('clients.edit', compact('client', 'health_plans'));
    }

    public function destroy(Request $request)
    {
        $location = Client::findOrFail($request->id);
        $location->active = 0;
        $location->save();

        return redirect()->route('clients')->withErrors([
            'mensagem' => 'Cliente desativado com sucesso',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
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

    public function new()
    {
        $health_plans = HealthPlan::all()
            ->where('active', 1)->where('clinica_id', Auth::user()->clinica_id);
        return view('clients.new', compact('health_plans'));
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


    public function showRegistrationForm($slug = null)
    {

        $clinica = Clinica::where('slug', $slug)->first();

        return view('client.register', [
            'clinica' => $clinica,
            'slug' => $slug,
        ]);
    }



    public function register(Request $request, $slug)
    {
        $clinica = Clinica::where('slug', $slug)->first();

        // Validações básicas
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($clinica) {
                    return $query->where('clinica_id', $clinica->id);
                }),
            ],
            'password' => 'required|string|min:6|confirmed',
            'cpf' => 'required|string|max:14|unique:users',
            'date_birth' => 'required|date',
            'telephone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ], [
            'email.unique' => 'Este email já está cadastrado nesta clínica.',
            'cpf.unique' => 'Este CPF já está cadastrado nesta clínica.',
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'date_birth.required' => 'A data de nascimento é obrigatória.',
            'telephone.required' => 'O telefone é obrigatório.',
            'address.required' => 'O endereço é obrigatório.',
        ]);

        // Verificar se o email já existe na clínica
        $existingUser = User::where('email', $request->email)
            ->where('clinica_id', $clinica->id)
            ->first();

        if ($existingUser) {
            return back()->withErrors([
                'email' => 'Este email já está cadastrado nesta clínica.'
            ])->withInput();
        }

        // Criar o usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'perfil' => "cliente",
            'cpf' => $request->cpf,
            'clinica_id' => intval($clinica->id),
            'address' => $request->address,
            'telephone' => $request->telephone,
        ]);

        // Buscar o último plano de saúde da clínica
        $healthPlan = \App\Models\HealthPlan::where('clinica_id', $clinica->id)
            ->latest()
            ->first();

        // Criar o cliente com o created_by definido
        $clientData = [
            'name' => $request->name,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'date_birth' => $request->date_birth,
            'cpf' => $request->cpf,
            'health_plan_id' => $healthPlan->id,
            'clinica_id' => $clinica->id,
            'user_id' => $user->id,
            'created_by' => $user->id,
        ];

        $client = \App\Models\Client::create($clientData);

        // Autenticar o usuário
        Auth::login($user);

        return redirect()->route('client.appointments')->with('success', 'Cadastro realizado com sucesso!');
    }
}
