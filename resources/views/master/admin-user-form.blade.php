@extends('layouts.app')

@section('title', isset($user) ? 'Editar Administrador' : 'Novo Administrador')

@section('content')
<div class="d-flex justify-content-center align-items-center">
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%; max-width: 600px;">
        <div class="card-body p-4">
            <h4 class="card-title mb-4">{{ isset($user) ? 'Editar Administrador' : 'Novo Administrador' }}</h4>

            <form action="{{ isset($user) ? route('admin-users.update', $user->id) : route('admin-users.store') }}" method="POST">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control cpf @error('cpf') is-invalid @enderror" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf ?? '') }}" required>
                    @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control phone @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', $user->telephone ?? '') }}" required>
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ isset($user) ? 'Nova Senha (deixe em branco para manter a atual)' : 'Senha' }}</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ !isset($user) ? 'required' : '' }}>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ !isset($user) ? 'required' : '' }}>
                </div>

                <div class="mb-3">
                    <label for="clinica_id" class="form-label">Clínica</label>
                    <select class="form-select @error('clinica_id') is-invalid @enderror" id="clinica_id" name="clinica_id">
                        <option value="">Selecione uma clínica</option>
                        @foreach($clinicas as $clinica)
                            <option value="{{ $clinica->id }}" {{ old('clinica_id', $user->clinica_id ?? '') == $clinica->id ? 'selected' : '' }}>
                                {{ $clinica->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('clinica_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin-users') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Atualizar' : 'Criar' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 