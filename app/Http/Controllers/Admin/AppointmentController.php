<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Services\DailyCoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    private $dailyCoService;

    // Configurações de tempo (em minutos)
    private const DURACAO_CONSULTA = 60; // Duração padrão da consulta
    private const TEMPO_ANTECEDENCIA = 15; // Tempo de antecedência para entrar na sala


    public function __construct(DailyCoService $dailyCoService)
    {
        $this->dailyCoService = $dailyCoService;
    }

    public function index(Request $request)
    {
        $query = Appointment::with(['medico', 'paciente'])
            ->where('clinica_id', Auth::user()->clinica_id);

        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('paciente', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })
                    ->orWhereHas('medico', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $appointments = $query->orderBy('data_hora_inicio', 'desc')->paginate(10);
        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $medicos = User::where('perfil', 'medico')
            ->where('clinica_id', Auth::user()->clinica_id)
            ->orderBy('name')
            ->get();

        $pacientes = User::where('perfil', 'cliente')
            ->where('clinica_id', Auth::user()->clinica_id)
            ->orderBy('name')
            ->get();

        return view('admin.appointments.create', compact('medicos', 'pacientes'));
    }

    public function store(Request $request)
    {

        // $inicio = Carbon::parse($request->data_hora_inicio);
        $inicio = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo');


        if ($inicio->lt(now()->subMinutes(1))) {
            return back()->withErrors(['data_hora_inicio' => 'A data e hora não podem estar no passado.'])->withInput();
        }

        $request->validate([
            'medico_id' => 'required|exists:users,id',
            'paciente_id' => 'required|exists:users,id',
            'data_hora_inicio' => 'required|date',
        ]);

        // Verifica se o médico está disponível no horário escolhido
        $dataHoraInicio = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo');
        $dataHoraFim = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo')->addMinutes(self::DURACAO_CONSULTA);

        // Verifica disponibilidade do médico
        $medicoOcupado = Appointment::where('medico_id', $request->medico_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where(function ($query) use ($dataHoraInicio, $dataHoraFim) {
                $query->where(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                    $q->whereBetween('data_hora_inicio', [$dataHoraInicio, $dataHoraFim->subMinute()])
                        ->orWhereBetween('data_hora_fim', [$dataHoraInicio->addMinute(), $dataHoraFim])
                        ->orWhere(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                            $q->where('data_hora_inicio', '<', $dataHoraInicio)
                                ->where('data_hora_fim', '>', $dataHoraFim);
                        });
                });
            })
            ->where('status', '!=', 'cancelado')
            ->first();

        if ($medicoOcupado) {
            $horarioConflito = Carbon::parse($medicoOcupado->data_hora_inicio)->format('d/m/Y H:i');
            $horarioConflitoFim = Carbon::parse($medicoOcupado->data_hora_fim)->format('H:i');

            return redirect()
                ->route('admin.new-appointment')
                ->withInput()
                ->with('error', "O médico já possui um agendamento neste horário. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}");
        }

        // Verifica disponibilidade do paciente
        $pacienteOcupado = Appointment::where('paciente_id', $request->paciente_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where(function ($query) use ($dataHoraInicio, $dataHoraFim) {
                $query->where(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                    $q->whereBetween('data_hora_inicio', [$dataHoraInicio, $dataHoraFim->subMinute()])
                        ->orWhereBetween('data_hora_fim', [$dataHoraInicio->addMinute(), $dataHoraFim])
                        ->orWhere(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                            $q->where('data_hora_inicio', '<', $dataHoraInicio)
                                ->where('data_hora_fim', '>', $dataHoraFim);
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
                ->route('admin.new-appointment')
                ->withInput()
                ->with('error', "O paciente já possui um agendamento neste horário com o Dr(a). {$medicoConflito}. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}");
        }

        // Configura o nbf (not before) para 15 minutos antes do agendamento
        $dataHoraInicioOriginal = $request->data_hora_inicio;
        $dataHoraInicio = Carbon::parse($dataHoraInicioOriginal, 'America/Sao_Paulo');
        $dataHoraInicioUtc = $dataHoraInicio->copy()->timezone('UTC');

        $nbf = $dataHoraInicioUtc->copy()->subMinutes(self::TEMPO_ANTECEDENCIA)->timestamp;
        $exp = $dataHoraInicioUtc->copy()->addMinutes(self::DURACAO_CONSULTA)->timestamp;

        // Cria a sala na Daily.co com nbf e exp configurados
        $room = $this->dailyCoService->createRoom($nbf, $exp);

        if (!$room['success']) {
            return back()->with('error', 'Erro ao criar sala de telemedicina: ' . $room['error']);
        }

        $appointment = new Appointment();
        $appointment->medico_id = $request->medico_id;
        $appointment->paciente_id = $request->paciente_id;
        $appointment->clinica_id = Auth::user()->clinica_id;
        $appointment->data_hora_inicio = $dataHoraInicio;
        $appointment->data_hora_fim = $dataHoraInicio->copy()->addMinutes(self::DURACAO_CONSULTA);
        $appointment->tipo = 'telemedicina';
        $appointment->status = 'pendente';
        $appointment->link_telemedicina = $room['url'];
        $appointment->nome_sala = $room['room_name'];
        $appointment->sala_expira_em = $room['expires_at'];
        $appointment->nbf = $nbf;
        $appointment->exp = $exp;
        $appointment->save();

        return redirect()->route('admin.appointments')->with('success', 'Agendamento criado com sucesso!');
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Verifica se o agendamento pertence à clínica do admin
        if ($appointment->medico->clinica_id !== Auth::user()->clinica_id) {
            return back()->with('error', 'Você não tem permissão para excluir este agendamento.');
        }

        // Se for uma consulta de telemedicina, tenta excluir a sala
        if ($appointment->tipo === 'telemedicina' && $appointment->nome_sala) {
            $result = $this->dailyCoService->deleteRoom($appointment->nome_sala);

            if (!$result['success']) {
                return back()->with('error', 'Erro ao excluir sala de telemedicina: ' . $result['error']);
            }
        }

        $appointment->delete();

        return redirect()->route('admin.appointments')->with('success', 'Agendamento excluído com sucesso!');
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Log para debug
        Log::info('Data original do banco: ' . $appointment->data_hora_inicio);
        Log::info('Data formatada: ' . \Carbon\Carbon::parse($appointment->data_hora_inicio)->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'));

        // Verifica se o agendamento pertence à clínica do admin
        if ($appointment->medico->clinica_id !== Auth::user()->clinica_id) {
            return back()->with('error', 'Você não tem permissão para editar este agendamento.');
        }

        $medicos = User::where('perfil', 'medico')
            ->where('clinica_id', Auth::user()->clinica_id)
            ->orderBy('name')
            ->get();

        $pacientes = User::where('perfil', 'cliente')
            ->where('clinica_id', Auth::user()->clinica_id)
            ->orderBy('name')
            ->get();

        return view('admin.appointments.edit', compact('appointment', 'medicos', 'pacientes'));
    }

    public function update(Request $request, $id)
    {

        $appointment = Appointment::findOrFail($id);

        // Verifica se o agendamento pertence à clínica do admin
        if ($appointment->medico->clinica_id !== Auth::user()->clinica_id) {
            return back()->with('error', 'Você não tem permissão para editar este agendamento.');
        }

        // Se estiver apenas atualizando o status para confirmado
        if ($request->has('status') && $request->status == 'confirmado') {
            // Configura o nbf (not before) para 15 minutos antes do agendamento
            $dataHoraInicio = Carbon::parse($appointment->data_hora_inicio, 'America/Sao_Paulo');
            $dataHoraInicioUtc = $dataHoraInicio->copy()->timezone('UTC');

            $nbf = $dataHoraInicioUtc->copy()->subMinutes(self::TEMPO_ANTECEDENCIA)->timestamp;
            $exp = $dataHoraInicioUtc->copy()->addMinutes(self::DURACAO_CONSULTA)->timestamp;

            // Cria a sala na Daily.co com nbf e exp configurados
            $room = $this->dailyCoService->createRoom($nbf, $exp);

            if (!$room['success']) {
                return back()->with('error', 'Erro ao criar sala de telemedicina: ' . $room['error']);
            }

            $appointment->status = 'confirmado';
            $appointment->link_telemedicina = $room['url'];
            $appointment->nome_sala = $room['room_name'];
            $appointment->sala_expira_em = $room['expires_at'];
            $appointment->nbf = $nbf;
            $appointment->exp = $exp;
            $appointment->save();

            return redirect()->route('admin.appointments')->with('success', 'Agendamento confirmado com sucesso!');
        }

        $request->validate([
            'medico_id' => 'required|exists:users,id',
            'paciente_id' => 'required|exists:users,id',
            'data_hora_inicio' => 'required|date',
        ]);

        $inicio = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo');

        if ($inicio->lt(now()->subMinutes(1))) {
            return back()->withErrors(['data_hora_inicio' => 'A data e hora não podem estar no passado.'])->withInput();
        }

        // Verifica se o médico está disponível no horário escolhido
        $dataHoraInicio = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo');
        $dataHoraFim = Carbon::parse($request->data_hora_inicio, 'America/Sao_Paulo')->addMinutes(self::DURACAO_CONSULTA);

        // Cria variáveis separadas para verificação
        $verificaInicio = $dataHoraInicio->copy();
        $verificaFim = $dataHoraFim->copy();

        // Verifica disponibilidade do médico
        $medicoOcupado = Appointment::where('medico_id', $request->medico_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where('id', '!=', $id) // Exclui o próprio agendamento da verificação
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

            return back()
                ->withInput()
                ->with('error', "O médico já possui um agendamento neste horário. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}");
        }

        // Cria novas variáveis para verificação do paciente
        $verificaInicio = $dataHoraInicio->copy();
        $verificaFim = $dataHoraFim->copy();

        // Verifica disponibilidade do paciente
        $pacienteOcupado = Appointment::where('paciente_id', $request->paciente_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where('id', '!=', $id) // Exclui o próprio agendamento da verificação
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

            return back()
                ->withInput()
                ->with('error', "O paciente já possui um agendamento neste horário com o Dr(a). {$medicoConflito}. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}");
        }

        // Se for uma consulta de telemedicina, exclui a sala antiga e cria uma nova
        if ($appointment->tipo === 'telemedicina' && $appointment->nome_sala) {
            $result = $this->dailyCoService->deleteRoom($appointment->nome_sala);

            if (!$result['success']) {
                return back()->with('error', 'Erro ao excluir sala de telemedicina: ' . $result['error']);
            }

            // Configura o nbf (not before) para 15 minutos antes do agendamento
            $dataHoraInicioUtc = $dataHoraInicio->copy()->timezone('UTC');

            $nbf = $dataHoraInicioUtc->copy()->subMinutes(self::TEMPO_ANTECEDENCIA)->timestamp;
            $exp = $dataHoraInicioUtc->copy()->addMinutes(self::DURACAO_CONSULTA)->timestamp;

            // Cria a nova sala na Daily.co com nbf e exp configurados
            $room = $this->dailyCoService->createRoom($nbf, $exp);

            if (!$room['success']) {
                return back()->with('error', 'Erro ao criar sala de telemedicina: ' . $room['error']);
            }

            $appointment->link_telemedicina = $room['url'];
            $appointment->nome_sala = $room['room_name'];
            $appointment->sala_expira_em = $room['expires_at'];
            $appointment->nbf = $nbf;
            $appointment->exp = $exp;
        }

        $appointment->medico_id = $request->medico_id;
        $appointment->paciente_id = $request->paciente_id;
        $appointment->data_hora_inicio = $dataHoraInicio;
        $appointment->data_hora_fim = $dataHoraFim;

        $appointment->save();

        return redirect()->route('admin.appointments')->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function view($id)
    {
        $appointment = Appointment::with(['medico', 'paciente'])->findOrFail($id);

        // Verifica se o agendamento pertence à clínica do admin
        if ($appointment->medico->clinica_id !== Auth::user()->clinica_id) {
            return back()->with('error', 'Você não tem permissão para visualizar este agendamento.');
        }

        return view('admin.appointments.view', compact('appointment'));
    }

    public function checkAvailability($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Verifica se o agendamento pertence à clínica do admin
        if ($appointment->medico->clinica_id !== Auth::user()->clinica_id) {
            return response()->json([
                'available' => false,
                'message' => 'Você não tem permissão para verificar este agendamento.'
            ]);
        }

        $dataHoraInicio = Carbon::parse($appointment->data_hora_inicio, 'America/Sao_Paulo');
        $dataHoraFim = Carbon::parse($appointment->data_hora_inicio, 'America/Sao_Paulo')->addMinutes(self::DURACAO_CONSULTA);

        // Verifica disponibilidade do médico
        $medicoOcupado = Appointment::where('medico_id', $appointment->medico_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($dataHoraInicio, $dataHoraFim) {
                $query->where(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                    $q->whereBetween('data_hora_inicio', [$dataHoraInicio, $dataHoraFim->subMinute()])
                        ->orWhereBetween('data_hora_fim', [$dataHoraInicio->addMinute(), $dataHoraFim])
                        ->orWhere(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                            $q->where('data_hora_inicio', '<', $dataHoraInicio)
                                ->where('data_hora_fim', '>', $dataHoraFim);
                        });
                });
            })
            ->where('status', '!=', 'cancelado')
            ->first();

        if ($medicoOcupado) {
            $horarioConflito = Carbon::parse($medicoOcupado->data_hora_inicio)->format('d/m/Y H:i');
            $horarioConflitoFim = Carbon::parse($medicoOcupado->data_hora_fim)->format('H:i');

            return response()->json([
                'available' => false,
                'message' => "O médico já possui um agendamento neste horário. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}"
            ]);
        }

        // Verifica disponibilidade do paciente
        $pacienteOcupado = Appointment::where('paciente_id', $appointment->paciente_id)
            ->where('clinica_id', Auth::user()->clinica_id)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($dataHoraInicio, $dataHoraFim) {
                $query->where(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                    $q->whereBetween('data_hora_inicio', [$dataHoraInicio, $dataHoraFim->subMinute()])
                        ->orWhereBetween('data_hora_fim', [$dataHoraInicio->addMinute(), $dataHoraFim])
                        ->orWhere(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                            $q->where('data_hora_inicio', '<', $dataHoraInicio)
                                ->where('data_hora_fim', '>', $dataHoraFim);
                        });
                });
            })
            ->where('status', '!=', 'cancelado')
            ->first();

        if ($pacienteOcupado) {
            $horarioConflito = Carbon::parse($pacienteOcupado->data_hora_inicio)->format('d/m/Y H:i');
            $horarioConflitoFim = Carbon::parse($pacienteOcupado->data_hora_fim)->format('H:i');
            $medicoConflito = User::find($pacienteOcupado->medico_id)->name;

            return response()->json([
                'available' => false,
                'message' => "O paciente já possui um agendamento neste horário com o Dr(a). {$medicoConflito}. Conflito com agendamento das {$horarioConflito} até {$horarioConflitoFim}"
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Horário disponível para agendamento.'
        ]);
    }
}
