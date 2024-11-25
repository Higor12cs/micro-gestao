@extends('adminlte::page')

@section('title', 'Erro 403')

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Erro 403 - Acesso Proibido</h4>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body text-center">
            <h2>Acesso Proibido</h2>
            <p class="mb-4">Você não tem permissão para acessar este recurso. Se achar que isso é um erro, entre em contato
                com o suporte.</p>
            <a href="{{ route('home.index') }}" class="btn btn-primary">
                <i class="fas fa-home mr-2"></i>Página Inicial
            </a>
        </div>
    </div>
@stop
