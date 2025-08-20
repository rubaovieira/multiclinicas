<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('paciente')
            ->where('medico_id', Auth::id());

        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->whereHas('paciente', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })
            ->orWhere('data_hora_inicio', 'like', "%{$searchTerm}%");
        }

        $appointments = $query->orderBy('data_hora_inicio', 'desc')
            ->paginate();

        return view('appointments.index', compact('appointments'));
    }
} 