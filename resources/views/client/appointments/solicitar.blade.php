@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
    <div class="d-flex justify-content-center align-items-center">
        <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
            <div class="card-body p-4">
                <h3 class="card-title mb-4">Novo Agendamento</h3>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('client.appointments.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="medico_id" class="form-label">Médico</label>
                            <select class="form-select @error('medico_id') is-invalid @enderror" id="medico_id"
                                name="medico_id" required>
                                <option value="">Selecione o médico</option>
                                @foreach ($medicos as $medico)
                                    <option value="{{ $medico->id }}"
                                        {{ old('medico_id') == $medico->id ? 'selected' : '' }}>
                                        {{ $medico->name }} - {{ $medico->observation }}
                                    </option>
                                @endforeach
                            </select>
                            @error('medico_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control @error('data') is-invalid @enderror" id="data"
                                name="data" value="{{ old('data') }}" required>
                            @error('data')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Grade de Horários -->
                    <div id="horarios-container" class="mb-3" style="display: none;">
                        <h5 class="mb-3">Horários Disponíveis</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="loading-horarios" style="display: none;">
                                    <i class="bi bi-hourglass-split me-2"></i>Carregando horários disponíveis...
                                </div>
                                <div id="horarios-disponiveis" class="row">
                                    <!-- Os horários serão carregados aqui via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="data_hora_inicio" id="data_hora_inicio">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
  
@endpush

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const medicoSelect = document.getElementById('medico_id');
            const dataInput = document.getElementById('data');
            const horariosContainer = document.getElementById('horarios-container');
            const loadingHorarios = document.getElementById('loading-horarios');
            const horariosDisponiveis = document.getElementById('horarios-disponiveis');
            const btnSubmit = document.getElementById('btn-submit');
            const dataHoraInicioInput = document.getElementById('data_hora_inicio');

            function carregarHorarios() {
                const medicoId = medicoSelect.value;
                const data = dataInput.value;

                if (!medicoId || !data) {
                    horariosContainer.style.display = 'none';
                    btnSubmit.disabled = true;
                    return;
                }

                loadingHorarios.style.display = 'block';
                horariosDisponiveis.innerHTML = '';

                fetch(`{{ route('client.appointments.available-times') }}?medico_id=${medicoId}&data=${data}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingHorarios.style.display = 'none';
                        horariosContainer.style.display = 'block';

                        if (data.horarios.length === 0) {
                            horariosDisponiveis.innerHTML =
                                '<div class="col-12"><p class="text-muted">Nenhum horário disponível para esta data.</p></div>';
                            return;
                        }

                        // Cria uma única coluna para todos os horários
                        const coluna = document.createElement('div');
                        coluna.className = 'col-12';

                        data.horarios.forEach((horario, index) => {
                            const div = document.createElement('div');
                            div.className = 'form-check mb-2';

                            const input = document.createElement('input');
                            input.type = 'radio';
                            input.className = 'form-check-input';
                            input.name = 'horario';
                            input.id = `horario_${index}`;
                            input.value = horario.value;

                            const label = document.createElement('label');
                            label.className = 'form-check-label';
                            label.htmlFor = `horario_${index}`;
                            label.textContent = horario.label;

                            if (!horario.disponivel) {
                                input.disabled = true;
                                label.classList.add('horario-indisponivel');
                            } else {
                                label.classList.add('horario-disponivel');
                                input.addEventListener('change', function() {
                                    if (this.checked) {
                                        dataHoraInicioInput.value = this.value;
                                        btnSubmit.disabled = false;
                                    }
                                });
                            }

                            div.appendChild(input);
                            div.appendChild(label);
                            coluna.appendChild(div);
                        });

                        horariosDisponiveis.appendChild(coluna);
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        loadingHorarios.style.display = 'none';
                        horariosDisponiveis.innerHTML =
                            '<div class="col-12"><p class="text-danger">Erro ao carregar horários disponíveis.</p></div>';
                    });
            }

            medicoSelect.addEventListener('change', carregarHorarios);
            dataInput.addEventListener('change', carregarHorarios);

            document.querySelector('form').addEventListener('reset', function() {
                btnSubmit.disabled = true;
            });
        });
    </script>
@endsection
