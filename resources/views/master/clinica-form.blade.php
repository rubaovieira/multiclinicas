@extends('layouts.app')

@section('title', isset($clinica) ? 'Editar Clínica' : 'Nova Clínica')

@section('content')
<div class="d-flex justify-content-center align-items-center">
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%; max-width: 600px;">
        <div class="card-body p-4">
            <h4 class="card-title mb-4">{{ isset($clinica) ? 'Editar Clínica' : 'Nova Clínica' }}</h4>

            <form action="{{ isset($clinica) ? route('clinics.update', $clinica->id) : route('clinics.store') }}" method="POST">
                @csrf
                @if(isset($clinica))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Clínica</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $clinica->nome ?? '') }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(isset($clinica))
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="ativo" {{ old('status', $clinica->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ old('status', $clinica->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('clinics') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ isset($clinica) ? 'Atualizar' : 'Criar' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 