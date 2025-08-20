@extends('layouts.app')

@section('title', 'Agendamentos')

@section('content')
    <div class="d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Botão Criar Novo Agendamento -->
                <div class="text-end mb-3">
                    <a href="{{ route('admin.new-appointment') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i>
                        Novo Agendamento
                    </a>
                </div>

                <!-- Formulário de Busca -->
                <form action="{{ route('admin.appointments') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control"
                            placeholder="Buscar por paciente, médico ou data" aria-label="Buscar"
                            value="{{ $_GET['query'] ?? '' }}">
                        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data/Hora Início</th>
                                <th>Data/Hora Fim</th>
                                <th>Médico</th>
                                <th>Paciente</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        @if (count($appointments) == 0)
                            <tbody>
                                <td colspan="7">
                                    <div class="alert alert-warning" role="alert">
                                        Nenhum agendamento encontrado.
                                    </div>
                                </td>
                            </tbody>
                        @endif
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->data_hora_fim)->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                    </td>
                                    <td>{{ $appointment->medico->name }}</td>
                                    <td>{{ $appointment->paciente->name }}</td>
                                    <td>
                                        @if ($appointment->tipo === 'telemedicina')
                                            <span class="badge bg-info">Telemedicina</span>
                                        @else
                                            <span class="badge bg-primary">Presencial</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $now = \Carbon\Carbon::now();

                                            $start = \Carbon\Carbon::parse($appointment->data_hora_inicio);
                                            $end = \Carbon\Carbon::parse($appointment->data_hora_fim);

                                            if ($appointment->status == 'solicitado cliente') {
                                                $status = 'Solicitado pelo cliente';
                                                $class = 'bg-danger';
                                                $icon = 'bi-clock';
                                            } elseif ($now->greaterThan($end)) {
                                                $status = 'concluído';
                                                $class = 'bg-secondary';
                                                $icon = 'bi-check-circle';
                                            } elseif ($now->lessThan($start)) {
                                                $status = 'pendente';
                                                $class = 'bg-warning';
                                                $icon = 'bi-clock';
                                            } else {
                                                $status = 'em andamento';
                                                $class = 'bg-success';
                                                $icon = 'bi-play-circle';
                                            }
                                        @endphp
                                        <span class="badge {{ $class }} text-white">
                                            <i class="bi {{ $icon }} me-1"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="d-flex gap-2">

                                        @if ($appointment->status == 'solicitado cliente')
                                            <a href="{{ route('admin.view-appointment', ['id' => $appointment->id]) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle"></i> Ver consulta
                                            </a>
                                        @endif


                                        @if ($appointment->tipo === 'telemedicina' && $appointment->link_telemedicina)
                                            <a href="{{ $appointment->link_telemedicina }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="bi bi-camera-video"></i> Entrar
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="bi bi-camera-video"></i> Entrar
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.edit-appointment', ['id' => $appointment->id]) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.delete-appointment', ['id' => $appointment->id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Deseja realmente cancelar este agendamento?')">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="container mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">Mostrando {{ $appointments->firstItem() }} a {{ $appointments->lastItem() }} de
                            {{ $appointments->total() }} resultados</p>
                        <div>
                            {{ $appointments->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
