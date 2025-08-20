@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
<div class="d-flex justify-content-center align-items-center">
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">
            <h3 class="card-title mb-4">Novo Agendamento</h3>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.appointments.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="medico_id" class="form-label">Médico</label>
                        <select class="form-select @error('medico_id') is-invalid @enderror" id="medico_id" name="medico_id" required>
                            <option value="">Selecione o médico</option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}" {{ old('medico_id') == $medico->id ? 'selected' : '' }}>
                                    {{ $medico->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('medico_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="paciente_id" class="form-label">Paciente</label>
                        <select class="form-select @error('paciente_id') is-invalid @enderror" id="paciente_id" name="paciente_id" required>
                            <option value="">Selecione o paciente</option>
                            @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                                    {{ $paciente->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('paciente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_hora_inicio" class="form-label">Data e Hora</label>
                        <input type="datetime-local" class="form-control @error('data_hora_inicio') is-invalid @enderror" 
                            id="data_hora_inicio" name="data_hora_inicio" 
                            value="{{ old('data_hora_inicio') }}" required>
                        @error('data_hora_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="col-md-6">
                        <label for="tipo" class="form-label">Tipo de Consulta</label>
                        <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                            <option value="">Selecione o tipo</option>
                            <option value="presencial" {{ old('tipo') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="telemedicina" {{ old('tipo') == 'telemedicina' ? 'selected' : '' }}>Telemedicina</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.appointments') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 