@extends('layouts.app')

@section('title', 'Editar Convênio') 

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">   
            <form action="{{ route('edit-health_plan', ['id' => $health_plan->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf  
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input id="name" type="text" name="name" class="form-control" required placeholder="Digite o nome" value="{{ $health_plan->name }}">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Atualizar</button>
            </form>   
        </div>
    </div>
</div>
 
@endsection
