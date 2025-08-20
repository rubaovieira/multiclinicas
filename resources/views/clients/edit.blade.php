@extends('layouts.app')

@section('title', 'Editar Cliente') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">   
            <form action="{{ route('edit-client', ['id' => $client->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf 
                <h4>Informações Pessoais</h4>
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Nome Completo</label>
                    <input id="name" type="text" name="name" class="form-control" required placeholder="Digite o nome completo" value="{{ $client->name }}">
                </div>

                <div class="form-group mb-3">
                    <label for="date_birth" class="form-label">Data de Nascimento</label>
                    <input id="date_birth" type="text" name="date_birth" class="form-control date" required value="{{ date('d/m/Y', strtotime($client->date_birth)) }}">
                </div>

                <div class="form-group mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" type="text" name="cpf" class="form-control cpf" required placeholder="Digite o CPF" value="{{ $client->cpf }}">
                </div>

                <h4>Informações de Contato</h4>
                <div class="form-group mb-3">
                    <label for="telephone" class="form-label">Telefone</label>
                    <input id="telephone" type="text" name="telephone" class="form-control phone" required placeholder="Digite o telefone" value="{{ $client->telephone }}">
                </div>

                <div class="form-group mb-3">
                    <label for="address" class="form-label">Endereço</label>
                    <input id="address" type="text" name="address" class="form-control" required placeholder="Digite o endereço" value="{{ $client->address }}">
                </div>

                <div class="form-group mb-3">
                    <label for="caregiver_responsible" class="form-label">Responsável</label>
                    <input id="caregiver_responsible" type="text" name="caregiver_responsible" class="form-control" required placeholder="Nome do responsável" value="{{ $client->caregiver_responsible }}">
                </div>

                <h4>Informações de Saúde</h4>
                <div class="form-group mb-3">
                    <label for="diagnosis" class="form-label">Diagnóstico</label>
                    <input id="diagnosis" type="text" name="diagnosis" class="form-control" required placeholder="Digite o diagnóstico" value="{{ $client->diagnosis }}">
                </div>

                <div class="form-group mb-3">
                    <label for="health_plan_id" class="form-label">Plano de Saúde</label>
                    <select name="health_plan_id" class="form-control" required>
                        <option value="">Selecione um plano de saúde</option>
                        @foreach($health_plans as $healthPlan)
                            <option value="{{ $healthPlan->id }}" {{ $client->health_plan_id == $healthPlan->id ? 'selected' : '' }}>
                                {{ $healthPlan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <h4>Informações de Acesso</h4>
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control" required placeholder="Digite o email" value="{{ $client->user->email ?? '' }}">
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Nova Senha</label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                    <small class="text-muted">Preencha apenas se desejar alterar a senha</small>
                </div>

                <button type="submit" class="btn btn-primary w-100">Atualizar Cliente</button>
            </form>   
        </div>
    </div>
</div>
 
@endsection
