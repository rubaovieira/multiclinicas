<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        
        $users = User::query()
            ->where('perfil', 'admin')
            ->when($query, function($q) use ($query) {
                return $q->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('cpf', 'like', "%{$query}%");
                });
            })
            ->with('clinica')
            ->orderBy('name')
            ->paginate(10);

        return view('master.admin-users', compact('users'));
    }

    public function create()
    {
        $clinicas = Clinica::where('status', 'ativo')->orderBy('nome')->get();
        return view('master.admin-user-form', compact('clinicas'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|max:255|unique:users',
            'telefone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'clinica_id' => 'required|exists:clinicas,id',
        ], [
            'password.confirmed' => 'A confirmação de senha não corresponde à senha.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'clinica_id.required' => 'A clínica é obrigatória.',
            'clinica_id.exists' => 'A clínica selecionada é inválida.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.max' => 'O telefone deve ter no máximo 255 caracteres.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.max' => 'O CPF deve ter no máximo 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um email válido.',
            
        ]);



        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'telephone' => $request->telefone,
            'password' => Hash::make($request->password),
            'perfil' => 'admin',
            'clinica_id' => $request->clinica_id,
            'created_by' => Auth::user()->id,
        ]);

        
        //redirecionar para service-client/{id}
        return redirect()->route('admin-users')->withErrors([
            'mensagem' => 'Administrador criado com sucesso',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'

    }

    public function edit($id)
    {
        $user = User::where('perfil', 'admin')->findOrFail($id);
        $clinicas = Clinica::where('status', 'ativo')->orderBy('nome')->get();
        return view('master.admin-user-form', compact('user', 'clinicas'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'cpf' => 'required|string|max:14|unique:users,cpf,' . $id,
            'telefone' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
            'clinica_id' => 'nullable|exists:clinicas,id'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'telephone' => $request->telefone,
            'clinica_id' => $request->clinica_id
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        
        //redirecionar para service-client/{id}
        return redirect()->route('admin-users')->withErrors([
            'mensagem' => 'Administrador atualizado com sucesso',
        ])->with('toastType', 'success');   //'success', 'warning', 'danger'

    }

    public function deactivate($id)
    {
        $user = User::where('perfil', 'admin')->findOrFail($id);
        $user->update(['active' => false]);

        return redirect()->route('admin-users')->with('success', 'Administrador desativado com sucesso!');
    }

    public function activate($id)
    {
        $user = User::where('perfil', 'admin')->findOrFail($id);
        $user->update(['active' => true]);

        return redirect()->route('admin-users')->with('success', 'Administrador ativado com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin-users')->with('success', 'Administrador excluído com sucesso!');
    }
}
