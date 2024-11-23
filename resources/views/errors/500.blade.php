@extends('adminlte::page')

@section('title', 'Erro 500')

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Erro 500 - Erro no servidor</h4>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body text-center">
            <h2>Ocorreu um erro no servidor</h2>
            <p class="mb-4">Caso acredite que este erro pode ser algum problema no sistema, contate o suporte.</p>
            <a href="{{ route('home.index') }}" class="btn btn-primary">
                <i class="fas fa-home mr-2"></i>Página Inicial
            </a>
        </div>
    </div>
@stop
