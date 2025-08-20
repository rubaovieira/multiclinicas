<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class BreadcrumbHelper
{
    public static function generate()
    {
        // Obtenha o nome da rota atual
        $routeName = Route::currentRouteName();
        
        // Defina os breadcrumbs baseados nas rotas
        $breadcrumbs = [];

        // Adicione o "Início"
        $breadcrumbs[] = [
            'title' => 'Início',
            'url' => route('home'),
        ];

        switch ($routeName) {  
            case 'clients':
                $breadcrumbs[] = ['title' => 'Pacientes', 'url' => route('clients')];
                $breadcrumbs[] = ['title' => 'Lista', 'url' => ''];
                break;

            case 'edit-client':
                $breadcrumbs[] = ['title' => 'Pacientes', 'url' => route('clients')];
                $breadcrumbs[] = ['title' => 'Editar Cliente', 'url' => ''];
                break; 
                
            case 'new-client':
                $breadcrumbs[] = ['title' => 'Pacientes', 'url' => route('clients')];
                $breadcrumbs[] = ['title' => 'Novo Cliente', 'url' => ''];
                break;  
                
            case 'service-client':
                $breadcrumbs[] = ['title' => 'Pacientes', 'url' => route('clients')];
                $breadcrumbs[] = ['title' => 'Atendimentos do Paciente', 'url' => ''];
                break;  
            case 'services':
                $breadcrumbs[] = ['title' => 'Atendimentos', 'url' => route('services')];
                $breadcrumbs[] = ['title' => 'Lista', 'url' => ''];
                break;  
            case 'users':
                $breadcrumbs[] = ['title' => 'Equipe', 'url' => route('users')];
                $breadcrumbs[] = ['title' => 'Lista', 'url' => ''];
                break;  
            case 'register':
                $breadcrumbs[] = ['title' => 'Equipe', 'url' => route('users')];
                $breadcrumbs[] = ['title' => 'Novo Usuário', 'url' => ''];
                break; 
            case 'edit-users':
                $breadcrumbs[] = ['title' => 'Equipe', 'url' => route('users')];
                $breadcrumbs[] = ['title' => 'Editar Usuário', 'url' => ''];
                break; 
            case 'procedures':
                $breadcrumbs[] = ['title' => 'Procedimentos', 'url' => route('procedures')];
                $breadcrumbs[] = ['title' => 'Lista', 'url' => ''];
                break; 
            case 'health_plans':
                $breadcrumbs[] = ['title' => 'Convênios', 'url' => route('health_plans')];
                $breadcrumbs[] = ['title' => 'Lista', 'url' => ''];
                break; 
            case 'edit-health_plan':
                $breadcrumbs[] = ['title' => 'Convênios', 'url' => route('health_plans')];
                $breadcrumbs[] = ['title' => 'Editar convênio', 'url' => ''];
                break; 
            case 'new-health_plan':
                $breadcrumbs[] = ['title' => 'Convênios', 'url' => route('health_plans')];
                $breadcrumbs[] = ['title' => 'Cadastrar convênio', 'url' => ''];
                break;  
            case 'schedule':
                $breadcrumbs[] = ['title' => 'Equipe', 'url' => route('users')];
                $breadcrumbs[] = ['title' => 'Quadro', 'url' => ''];
                break; 
            case 'controle_estoque':
                $breadcrumbs[] = ['title' => 'Estoque', 'url' => route('controle_estoque')];
                $breadcrumbs[] = ['title' => 'Controle de Estoque', 'url' => ''];
                break; 
            case 'saidas':
                $breadcrumbs[] = ['title' => 'Estoque', 'url' => route('controle_estoque')];
                $breadcrumbs[] = ['title' => 'Saidas', 'url' => ''];
                break; 
            case 'entradas':
                $breadcrumbs[] = ['title' => 'Estoque', 'url' => route('controle_estoque')];
                $breadcrumbs[] = ['title' => 'Entradas', 'url' => ''];
                break; 
            case 'produtos':
                $breadcrumbs[] = ['title' => 'Estoque', 'url' => route('controle_estoque')];
                $breadcrumbs[] = ['title' => 'Produtos', 'url' => ''];
                break; 
        }

        return $breadcrumbs;
    }
}
