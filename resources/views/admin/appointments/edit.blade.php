@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Agendamento</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.appointments.update', $appointment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="medico_id" class="col-md-4 col-form-label text-md-right">Médico</label>
                            <div class="col-md-6">
                                <select id="medico_id" class="form-control @error('medico_id') is-invalid @enderror" name="medico_id" required>
                                    <option value="">Selecione o médico</option>
                                    @foreach($medicos as $medico)
                                        <option value="{{ $medico->id }}" {{ $appointment->medico_id == $medico->id ? 'selected' : '' }}>
                                            {{ $medico->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('medico_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="paciente_id" class="col-md-4 col-form-label text-md-right">Paciente</label>
                            <div class="col-md-6">
                                <select id="paciente_id" class="form-control @error('paciente_id') is-invalid @enderror" name="paciente_id" required>
                                    <option value="">Selecione o paciente</option>
                                    @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->id }}" {{ $appointment->paciente_id == $paciente->id ? 'selected' : '' }}>
                                            {{ $paciente->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('paciente_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="data_hora_inicio" class="col-md-4 col-form-label text-md-right">Data e Hora</label>
                            <div class="col-md-6">
                                <input id="data_hora_inicio" type="datetime-local" class="form-control @error('data_hora_inicio') is-invalid @enderror" name="data_hora_inicio" value="{{ date('Y-m-d\TH:i', strtotime($appointment->data_hora_inicio)) }}" required>
                                @error('data_hora_inicio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Atualizar Agendamento
                                </button>
                                <a href="{{ route('admin.appointments') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 