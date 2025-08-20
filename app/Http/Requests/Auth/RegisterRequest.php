<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'cpf' => 'required|string|max:14|unique:users',
            'date_birth' => 'required|date',
            'telephone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            // 'diagnosis' => 'required|string|max:255',
            'clinica_id' => 'required|exists:clinicas,id',
        ];
    }

    //translates the error messages
    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'email.unique' => 'O email informado já está cadastrado',
            'password.required' => 'O campo senha é obrigatório',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'A confirmação de senha não corresponde',
            'cpf.required' => 'O campo CPF é obrigatório',
            'cpf.unique' => 'O CPF informado já está cadastrado',
            'date_birth.required' => 'O campo data de nascimento é obrigatório',
            'date_birth.date' => 'O campo data de nascimento deve ser uma data válida',
            'telephone.required' => 'O campo telefone é obrigatório',
            'address.required' => 'O campo endereço é obrigatório',
            // 'diagnosis.required' => 'O campo diagnóstico é obrigatório',
            'clinica_id.required' => 'A clínica é obrigatória',
            'clinica_id.exists' => 'A clínica informada não existe',
        ];
    }
}
