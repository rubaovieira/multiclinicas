<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ServiceLocations;
use Illuminate\Support\Facades\Auth;

class DashboardController
{
    public function dashboard()
    {

        $modules = [
            [
                'name' => 'Pacientes',
                'route' => 'clients',
                'icon' => 'bi bi-people',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Atendimentos',
                'route' => 'services',
                'icon' => 'bi bi-list-ul',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Procedimentos',
                'route' => 'procedures',
                'icon' => 'bi bi-receipt-cutoff',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Convênios',
                'route' => 'health_plans',
                'icon' => 'bi bi-credit-card-2-front',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Produtos',
                'route' => 'produtos',
                'icon' => 'bi bi-check2-square',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Estoque',
                'route' => 'controle_estoque',
                'icon' => 'bi bi-box-seam',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Saidas do Estoque',
                'route' => 'saidas',
                'icon' => 'bi bi-box-arrow-up',
                'color' => '#56A4EE',
            ],
            [
                'name' => 'Entradas do Estoque',
                'route' => 'entradas',
                'icon' => 'bi bi-box-arrow-down',
                'color' => '#56A4EE',
            ],
        ];

        if (auth()->user()->perfil === 'admin') {
            $modules[] =  [
                'name' => 'Equipe',
                'route' => 'users',
                'icon' => 'bi bi-person',
                'color' => '#56A4EE',
            ];
        }

        if (auth()->user()->perfil === 'cliente') {
            $modules = [[
                'name' => 'Minhas Consultas',
                'route' => 'client.appointments',
                'icon' => 'bi bi-list-ul',
                'color' => '#56A4EE',
            ]];
        }

        if (auth()->user()->perfil === 'medico') {
            $modules = [[
                'name' => 'Minhas Consultas',
                'route' => 'client.appointments',
                'icon' => 'bi bi-list-ul',
                'color' => '#56A4EE',
            ]];
        }


        if (auth()->user()->perfil === 'master') {
            $modules = [
                [
                    'name' => 'Administradores',
                    'route' => 'admin-users',
                    'icon' => 'bi bi-person',
                    'color' => '#56A4EE',
                ], 
                [
                    'name' => 'Clínicas',
                    'route' => 'clinics',
                    'icon' => 'bi bi-person',
                    'color' => '#56A4EE',
                ]
            ];
        }
        return view('dashboard.dashboard', compact('modules'));
    }
}
