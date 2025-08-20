<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Clinica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ServiceLocations;
use App\Models\UserServiceLocations;
use App\Models\UserConfigSchedules;
use App\Models\UserSchedules;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    protected function model()
    {
        return User::class;
    }

    public function showLoginForm($slug = null)
    {
        $clinica = Clinica::where('slug', $slug)->first();
        return view('auth.login', [
            'clinica' => $clinica,
            'slug' => $slug,
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
            'cpf' => 'required|string|max:14|unique:users',
            // 'date_birth' => 'required|date',
            'telephone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ], [
            'email.unique' => 'O email informado já está cadastrado',
            'cpf.unique' => 'O CPF informado já está cadastrado',
            // 'date_birth.date' => 'O campo data de nascimento deve ser uma data válida',
            'telephone.required' => 'O campo telefone é obrigatório',
            'address.required' => 'O campo endereço é obrigatório',
        ]);


        $data =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'perfil' => $request->perfil,
            'observation' => $request->observation,
            'cpf' => $request->cpf,
            'address' => $request->address,
            'telephone' => $request->telephone,
            'advice' => $request->advice,
            'clinica_id' => Auth::user()->clinica_id,
        ]);
        $id = $data->id;
        if ($request->hasFile('carimbo')) {
            $files = glob('storage/carimbos/' . $id . '/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            $file = $request->file('carimbo');
            $path = 'carimbos/' . $id;
            $filePath = $file->store($path, 'public');  // Salva o arquivo na pasta 'carimbos' do storage
            $user = User::find($id);
            $user->carimbo = $filePath;  // Salva o caminho do arquivo no banco
            $user->save();
        }

        return redirect()->route('users')->withErrors([
            'mensagem' => 'Usuário cadastrado com sucesso!',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'

    }

    public function login(Request $request, $slug)
    {

        $clinica = Clinica::where('slug', $slug)->first();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');


        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('home');
        }

        return back()->withErrors([
            'mensagem' => 'Senha ou email incorretos',
        ])->with('toastType', 'danger');
    }

    public function logout()
    {
        $user = Auth::user();
        Auth::logout();

        if ($user->perfil === 'master') {
            // return redirect()->route('login', ['slug' => 'master']);
            return redirect()->route('login');
        }

        return redirect()->route('login', ['slug' => $user->clinica->slug]);
    }

    public function list()
    {
        $query = $_GET['query'] ?? '';
        $users = User::where('clinica_id', Auth::user()->clinica_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%")
                    ->orWhere('observation', 'like', "%$query%")
                    ->orWhere('perfil', 'like', "%$query%");
            })
            ->orderBy('active', 'desc')
            ->paginate(10);
        return view('auth.users', ['users' => $users]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->active = 0;
        $user->save();
        return redirect()->route('users')->withErrors([
            'mensagem' => 'Usuário desativado com sucesso!',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

    public function activate($id)
    {
        $user = User::find($id);
        $user->active = 1;
        $user->save();
        return redirect()->route('users')->withErrors([
            'mensagem' => 'Usuário ativado com sucesso!',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

    public function show($id)
    {
        if (auth()->user()->perfil != 'admin') {
            return redirect()->route('users')->withErrors([
                'mensagem' => 'Você não tem permissão para acessar essa página!',
            ])->with('toastType', 'danger');   //'success', 'warning', 'danger'
        }

        $user = User::find($id);
        //buscar carimbo 
        $files = glob('storage/carimbos/' . $id . '/*');
        $carimbo = null;
        if (count($files) > 0) {
            $carimbo = $files[0];
        }
        return view('auth.edit', ['user' => $user, 'carimbo' => $carimbo]);
    }

    public function update(Request $request, $id)
    {

        if (auth()->user()->perfil != 'admin') {
            return redirect()->route('users')->withErrors([
                'mensagem' => 'Você não tem permissão para acessar essa página!',
            ])->with('toastType', 'danger');   //'success', 'warning', 'danger'
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->perfil = $request->perfil;
        $user->observation = $request->observation;
        $user->cpf = $request->cpf;
        $user->address = $request->address;
        $user->telephone = $request->telephone;
        $user->advice = $request->advice;
        if ($request->hasFile('carimbo')) {
            $files = glob('storage/carimbos/' . $id . '/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            $file = $request->file('carimbo');
            $path = 'carimbos/' . $id;
            $filePath = $file->store($path, 'public');  // Salva o arquivo na pasta 'carimbos' do storage
            $user->carimbo = $filePath;  // Salva o caminho do arquivo no banco
        }
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()->route('users')->withErrors([
            'mensagem' => 'Usuário atualizado com sucesso!',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'
    }

    public function schedule($id)
    {
        $user = User::find($id);
        return view('auth.schedule', ['user' => $user]);
    }

    public function schedule_update(Request $request, $id)
    {
        $user = User::find($id);

        $user_config_schedule = UserConfigSchedules::where('user_id', $id)
            ->where('active', true)
            ->get();

        $user_config_schedule_id = 0;
        if ($user_config_schedule->count() > 0) {
            $user_config_schedule_id = $user_config_schedule[0]->id;
        } else {
            $configSchedule = UserConfigSchedules::create([
                'user_id' => $id
            ]);
            $user_config_schedule_id = $configSchedule->id;
        }

        if (!$request->marcado) {
            return redirect()->route('users')->withErrors([
                'mensagem' => 'Marque pelo menos um dia da semana!',
            ])->with('toastType', 'danger');   //'success', 'warning', 'danger'
        }

        foreach ($request->marcado as $key =>  $marcado) {
            $apagardias = UserSchedules::where('user_config_schedule_id', $user_config_schedule_id)
                ->where('day', $key)
                ->where('active', true)
                ->get();
            if ($apagardias->count() > 0) {
                foreach ($apagardias as $item) {
                    $item->active = 0;
                    $item->save();
                }
            }
        }

        foreach ($request->inicio_manha as $key => $horario) {
            // Adicione isso antes da criação para debug
            // dd($horario['tempoAtendimento'], $horario['quantidade']);   
            if (isset($request->marcado[$key])) {
                if ($horario != "" && $request->fim_manha[$key] != "") {
                    UserSchedules::create([
                        'user_id' => $request->profissional_id,
                        'day' => $key,
                        'start' => $horario,
                        'end' => $request->fim_manha[$key],
                        'user_config_schedule_id' => $user_config_schedule_id,
                        'turn' =>  'inicio_manha'
                    ]);
                }
            } else {
                $apagardias = UserSchedules::where('user_config_schedule_id', $user_config_schedule_id)
                    ->where('day', $key)
                    ->where('active', true)
                    ->get();
                if ($apagardias->count() > 0) {
                    foreach ($apagardias as $item) {
                        $item->active = 0;
                        $item->save();
                    }
                }
            }
        }


        foreach ($request->inicio_tarde as $key => $horario) {
            if (isset($request->marcado[$key])) {
                if ($horario != "" && $request->fim_tarde[$key] != "") {
                    UserSchedules::create([
                        'user_id' => $request->profissional_id,
                        'day' => $key,
                        'start' => $horario,
                        'end' => $request->fim_tarde[$key],
                        'user_config_schedule_id' => $user_config_schedule_id,
                        'turn' =>  'inicio_tarde'
                    ]);
                }
            }
        }

        return redirect()->route('users')->withErrors([
            'mensagem' => 'Escala configurada com sucesso!',
        ])->with('toastType', 'success');
    }


    function buscarhorarios()
    {
        $user_id = $_GET['user_id'];
        $user_config_schedule = UserConfigSchedules::where('user_id', $user_id)
            ->where('active', true)
            ->get();
        if ($user_config_schedule->count() == 0) {
            echo json_encode([]);
            return;
        }

        $user_config_schedule = UserSchedules::where('user_config_schedule_id', $user_config_schedule[0]->id)
            ->where('active', true)
            ->get();

        echo json_encode($user_config_schedule);
    }
}
