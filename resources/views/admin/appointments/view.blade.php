@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detalhes do Agendamento</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Médico:</strong>
                            <p>{{ $appointment->medico->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Paciente:</strong>
                            <p>{{ $appointment->paciente->name }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Data e Hora:</strong>
                            <p>{{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p>{{ ucfirst($appointment->status) }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tipo de Consulta:</strong>
                            <p>{{ ucfirst($appointment->tipo) }}</p>
                        </div>
                        @if($appointment->tipo == 'telemedicina')
                        <div class="col-md-6">
                            <strong>Link da Consulta:</strong>
                            <p><a href="{{ $appointment->link_telemedicina }}" target="_blank">{{ $appointment->link_telemedicina }}</a></p>
                        </div>
                        @endif
                    </div>
                    @if($appointment->status == 'solicitado cliente')
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                <i class="bi bi-check-circle"></i> Aceitar Agendamento
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja aceitar este agendamento?</p>
                <p>Médico: {{ $appointment->medico->name }}</p>
                <p>Paciente: {{ $appointment->paciente->name }}</p>
                <p>Data e Hora: {{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->format('d/m/Y H:i') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="acceptForm" action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="confirmado">
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const acceptForm = document.getElementById('acceptForm');
    
    acceptForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar disponibilidade antes de aceitar
        fetch(`/admin/appointments/{{ $appointment->id }}/check-availability`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                // Se estiver disponível, submeter o formulário
                acceptForm.submit();
            } else {
                // Se não estiver disponível, mostrar mensagem de erro
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao verificar a disponibilidade. Por favor, tente novamente.');
        });
    });
});
</script>
@endpush
@endsection 