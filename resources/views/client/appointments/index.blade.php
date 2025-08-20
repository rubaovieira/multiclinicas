@extends('layouts.app')

@section('title', 'Minhas Consultas')

@section('content')
    <div class="d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Formulário de Busca -->
                <form action="{{ route('client.appointments') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control" placeholder="Buscar por médico ou data"
                            aria-label="Buscar" value="{{ request('query') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>

                <a href="{{ route('client.appointments.solicitar') }}" class="btn btn-primary mb-3">Solicitar nova consulta</a>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data/Hora Início</th>
                                <th>Data/Hora Fim</th>
                                <th>Médico</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->data_hora_fim)->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                    </td>
                                    <td>{{ $appointment->medico->name }}</td>
                                    <td>
                                        @if ($appointment->tipo === 'telemedicina')
                                            <span class="badge bg-info">
                                                <i class="bi bi-camera-video"></i> Telemedicina
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="bi bi-person"></i> Presencial
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $now = \Carbon\Carbon::now('America/Sao_Paulo');
                                            $start = \Carbon\Carbon::parse(
                                                $appointment->data_hora_inicio,
                                                'America/Sao_Paulo',
                                            );
                                            $end = \Carbon\Carbon::parse(
                                                $appointment->data_hora_fim,
                                                'America/Sao_Paulo',
                                            );

                                            if($appointment->status == 'solicitado cliente'){
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
                                    <td>
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Nenhuma consulta encontrada</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="container mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0 text-muted">
                            Mostrando {{ $appointments->firstItem() }} a {{ $appointments->lastItem() }}
                            de {{ $appointments->total() }} resultados
                        </p>
                        <div>
                            {{ $appointments->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
