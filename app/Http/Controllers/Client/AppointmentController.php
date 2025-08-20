<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // Configurações de tempo (em minutos)
    private const INTERVALO_HORARIOS = 60; // 60 = 1 hora, 30 = 30 minutos
    private const DURACAO_CONSULTA = 60; // Duração padrão da consulta
    private const TEMPO_ANTECEDENCIA = 15; // Tempo de antecedência para entrar na sala
    private const HORARIO_INICIO_PADRAO = '00:00:00'; // Horário de início padrão
    private const HORARIO_FIM_PADRAO = '23:00:00'; // Horário de fim padrão

    public function index(Request $request)
    {
        $query = Appointment::with(['medico'])
            ->where('paciente_id', Auth::id())
            ->where('clinica_id', Auth::user()->clinica_id);

        if ($request->has('query')) {
            $searchTerm = $request->query('query');

            // Verifica se o termo de busca é uma data no formato brasileiro (dd/mm/yyyy)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $searchTerm)) {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $searchTerm)->format('Y-m-d');
                $query->whereDate('data_hora_inicio', $date);
            } else {
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('medico', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    })
                        ->orWhere('data_hora_inicio', 'like', "%{$searchTerm}%");
                });
            }
        }

        $appointments = $query->orderBy('data_hora_inicio', 'desc')->paginate(10);
        return view('client.appointments.index', compact('appointments'));
    }



    public function solicitarAtendimento()
    {
        $medicos = User::where('perfil', 'medico')
            ->where('clinica_id', Auth::user()->clinica_id)
            ->orderBy('name')
            ->get();

        return view('client.appointments.solicitar', compact('medicos'));
    }


    public function store(Request $request)
    {

        // $inicio = Carbon::parse($request->data_hora_inicio);
        $inicio = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo');


        // if ($inicio->lt(now()->subMinutes(1))) {
        //     return back()->withErrors(['data_hora_inicio' => 'A data e hora não podem estar no passado.'])->withInput();
        // }

        $request->validate([
            'medico_id' => 'required|exists:users,id',
            'data_hora_inicio' => 'required|date',
        ], [
            'medico_id.required' => 'O médico é obrigatório.',
            'data_hora_inicio.required' => 'A data e hora são obrigatórias.',
            'data_hora_inicio.date' => 'A data e hora devem ser uma data válida.',
        ]);

        // Verifica se o médico está disponível no horário escolhido
        $dataHoraInicio = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo');
        $dataHoraFim = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo')->addMinutes(self::DURACAO_CONSULTA);

        // Verifica disponibilidade do médico
        $verificaInicio = $dataHoraInicio->copy();
        $verificaFim = $dataHoraFim->copy();

        $medicoOcupado = Appointment::where('medico_id', $request->medico_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where(function ($query) use ($verificaInicio, $verificaFim) {
                $query->where(function ($q) use ($verificaInicio, $verificaFim) {
                    $q->whereBetween('data_hora_inicio', [$verificaInicio, $verificaFim->subMinute()])
                        ->orWhereBetween('data_hora_fim', [$verificaInicio->addMinute(), $verificaFim])
                        ->orWhere(function ($q) use ($verificaInicio, $verificaFim) {
                            $q->where('data_hora_inicio', '<', $verificaInicio)
                                ->where('data_hora_fim', '>', $verificaFim);
                        });
                });
            })
            ->where('status', '!=', 'cancelado')
            ->first();

        if ($medicoOcupado) {
            $horarioConflito = Carbon::parse($medicoOcupado->data_hora_inicio)->format('d/m/Y H:i');
            $horarioConflitoFim = Carbon::parse($medicoOcupado->data_hora_fim)->format('H:i');

            return redirect()
                ->route('client.appointments.solicitar')
                ->withInput()
                ->with('error', "O médico já possui um agendamento neste horário. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}");
        }

        // Verifica disponibilidade do paciente
        $verificaInicio = $dataHoraInicio->copy();
        $verificaFim = $dataHoraFim->copy();

        $pacienteOcupado = Appointment::where('paciente_id', Auth::id())
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where(function ($query) use ($verificaInicio, $verificaFim) {
                $query->where(function ($q) use ($verificaInicio, $verificaFim) {
                    $q->whereBetween('data_hora_inicio', [$verificaInicio, $verificaFim->subMinute()])
                        ->orWhereBetween('data_hora_fim', [$verificaInicio->addMinute(), $verificaFim])
                        ->orWhere(function ($q) use ($verificaInicio, $verificaFim) {
                            $q->where('data_hora_inicio', '<', $verificaInicio)
                                ->where('data_hora_fim', '>', $verificaFim);
                        });
                });
            })
            ->where('status', '!=', 'cancelado')
            ->first();

        if ($pacienteOcupado) {
            $horarioConflito = Carbon::parse($pacienteOcupado->data_hora_inicio)->format('d/m/Y H:i');
            $horarioConflitoFim = Carbon::parse($pacienteOcupado->data_hora_fim)->format('H:i');
            $medicoConflito = User::find($pacienteOcupado->medico_id)->name;

            return redirect()
                ->route('client.appointments.solicitar')
                ->withInput()
                ->with('error', "O paciente já possui um agendamento neste horário com o Dr(a). {$medicoConflito}. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}");
        }


        $appointment = new Appointment();
        $appointment->medico_id = $request->medico_id;
        $appointment->paciente_id = Auth::id();
        $appointment->clinica_id = Auth::user()->clinica_id;
        $appointment->data_hora_inicio = $dataHoraInicio;
        $appointment->data_hora_fim = $dataHoraFim;
        $appointment->tipo = 'telemedicina';
        $appointment->status = 'solicitado cliente';
        
        $appointment->save();

        // bem aqui depois de criar o agendamento, o admin deve receber um email para aceitar o agendamento
        // e depois de aceitar o agendamento, o cliente deve receber um email para confirmar a consulta

        // Buscar admins da clínica ordenados por data de criação
        $admins = User::where('clinica_id', Auth::user()->clinica_id)
            ->where('perfil', 'admin')
            ->where('active', true)
            ->orderBy('created_at', 'asc')
            ->get();

        // Se encontrou algum admin, envia mensagem para o primeiro (mais antigo)
        if ($admins->isNotEmpty()) {
            $admin = $admins->first();
            $medico = User::find($appointment->medico_id);
            $paciente = User::find($appointment->paciente_id);
            
            $mensagem = "Novo agendamento solicitado!\n\n";
            $mensagem .= "Médico: {$medico->name}\n";
            $mensagem .= "Paciente: {$paciente->name}\n";
            $mensagem .= "Data: " . Carbon::parse($appointment->data_hora_inicio)->format('d/m/Y') . "\n";
            $mensagem .= "Horário: " . Carbon::parse($appointment->data_hora_inicio)->format('H:i') . " às " . Carbon::parse($appointment->data_hora_fim)->format('H:i') . "\n";
            $mensagem .= "Tipo: Telemedicina\n\n";
            $mensagem .= "Por favor, acesse o sistema para confirmar este agendamento.";

        
            // Envia mensagem via WhatsApp
            Controller::sendZap($admin->telephone, $mensagem);
        }

        return redirect()->route('client.appointments')->with('success', 'Agendamento criado com sucesso!');
    }




    public function getAvailableTimes(Request $request)
    {
        $medicoId = $request->query('medico_id');
        $data = $request->query('data');

        if (!$medicoId || !$data) {
            return response()->json(['horarios' => []]);
        }

        // Busca a configuração de horários do médico
        $configSchedule = \App\Models\UserConfigSchedule::where('user_id', $medicoId)
            ->where('active', true)
            ->first();

        $horariosDisponiveis = [];
        $dataSelecionada = Carbon::parse($data);
        $diaSemana = $dataSelecionada->dayOfWeek; // Carbon usa 0-6, nosso banco usa 1-7

        // Se não tiver configuração de horário, assume que está disponível das 8h às 18h
        if (!$configSchedule) {
            $inicio = Carbon::parse($data . ' ' . self::HORARIO_INICIO_PADRAO);
            $fim = Carbon::parse($data . ' ' . self::HORARIO_FIM_PADRAO);
            
            while ($inicio < $fim) {
                $horarioFim = $inicio->copy()->addMinutes(self::INTERVALO_HORARIOS);
                
                // Verifica se já existe agendamento do médico neste horário
                $agendamentoMedico = \App\Models\Appointment::where('medico_id', $medicoId)
                    ->where('status', '!=', 'cancelado')
                    ->where(function($query) use ($inicio, $horarioFim) {
                        $query->where(function($q) use ($inicio, $horarioFim) {
                            $q->where(function($q) use ($inicio, $horarioFim) {
                                $q->where('data_hora_inicio', '<', $horarioFim)
                                    ->where('data_hora_fim', '>', $inicio);
                            });
                        });
                    })
                    ->first();

                // Verifica se o paciente já tem agendamento neste horário
                $agendamentoPaciente = \App\Models\Appointment::where('paciente_id', Auth::id())
                    ->where('medico_id', $medicoId)
                    ->where('status', '!=', 'cancelado')
                    ->where(function($query) use ($inicio, $horarioFim) {
                        $query->where(function($q) use ($inicio, $horarioFim) {
                            $q->where(function($q) use ($inicio, $horarioFim) {
                                $q->where('data_hora_inicio', '<', $horarioFim)
                                    ->where('data_hora_fim', '>', $inicio);
                            });
                        });
                    })
                    ->first();

                $horariosDisponiveis[] = [
                    'value' => $inicio->format('Y-m-d H:i:s'),
                    'label' => $inicio->format('H:i') . ' - ' . $horarioFim->format('H:i'),
                    'disponivel' => !$agendamentoMedico && !$agendamentoPaciente
                ];

                $inicio->addMinutes(self::INTERVALO_HORARIOS);
            }
        } else {
            // Busca os horários configurados para o dia da semana
            $horariosConfigurados = \App\Models\UserSchedule::where('user_config_schedule_id', $configSchedule->id)
                ->where('day', $diaSemana)
                ->where('active', true)
                ->orderBy('start') // Ordena os horários configurados
                ->get();

            foreach ($horariosConfigurados as $horario) {
                $inicio = Carbon::parse($data . ' ' . $horario->start);
                $fim = Carbon::parse($data . ' ' . $horario->end);

                while ($inicio < $fim) {
                    $horarioFim = $inicio->copy()->addMinutes(self::INTERVALO_HORARIOS);
                    
                    // Verifica se já existe agendamento do médico neste horário
                    $agendamentoMedico = \App\Models\Appointment::where('medico_id', $medicoId)
                        ->where('status', '!=', 'cancelado')
                        ->where(function($query) use ($inicio, $horarioFim) {
                            $query->where(function($q) use ($inicio, $horarioFim) {
                                $q->where(function($q) use ($inicio, $horarioFim) {
                                    $q->where('data_hora_inicio', '<', $horarioFim)
                                        ->where('data_hora_fim', '>', $inicio);
                                });
                            });
                        })
                        ->first();

                    // Verifica se o paciente já tem agendamento neste horário
                    $agendamentoPaciente = \App\Models\Appointment::where('paciente_id', Auth::id())
                        ->where('medico_id', $medicoId)
                        ->where('status', '!=', 'cancelado')
                        ->where(function($query) use ($inicio, $horarioFim) {
                            $query->where(function($q) use ($inicio, $horarioFim) {
                                $q->where(function($q) use ($inicio, $horarioFim) {
                                    $q->where('data_hora_inicio', '<', $horarioFim)
                                        ->where('data_hora_fim', '>', $inicio);
                                });
                            });
                        })
                        ->first();

                    $horariosDisponiveis[] = [
                        'value' => $inicio->format('Y-m-d H:i:s'),
                        'label' => $inicio->format('H:i') . ' - ' . $horarioFim->format('H:i'),
                        'disponivel' => !$agendamentoMedico && !$agendamentoPaciente
                    ];

                    $inicio->addMinutes(self::INTERVALO_HORARIOS);
                }
            }
        }

        // Ordena os horários disponíveis pelo horário de início
        usort($horariosDisponiveis, function($a, $b) {
            return strtotime($a['value']) - strtotime($b['value']);
        });

        return response()->json(['horarios' => $horariosDisponiveis]);
    }
}
