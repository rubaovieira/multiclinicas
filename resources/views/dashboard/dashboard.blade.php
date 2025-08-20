@extends('layouts.app')

@section('title', 'Início')

@section('content')

<div class="d-flex justify-content-center align-items-center"> 
    <div class="row">
        @foreach($modules as $module)
            <div class="col-lg-auto pb-4" title="{{ $module['name'] }}"
                onclick="window.location='{{ route($module['route']) }}'"
             style="cursor:pointer;">
                <div class="card text-center" style="width: 15rem; height: 15rem; background-color: white; border: none; transition: transform 0.2s; border:1px solid {{ $module['color'] }}; ">
                    <div class="card-body d-flex justify-content-center align-items-center flex-column">
                        <i class="{{ $module['icon'] }}" style="font-size:40px; color:{{ $module['color'] }}"></i> 
                        <h5 class="card-title" style="color: {{ $module['color'] }}; ">{{ $module['name'] }}</h5>  
                    </div>
                </div>
            </div>
        @endforeach 
    </div>  
</div>

<style>
.card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
</style>
@endsection
