@extends('adminlte::page')

@section('title', 'Erro 404')

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Erro 404 - Página não encontrada</h4>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body text-center">
            <h2>A página que você está procurando não existe</h2>
            <p class="mb-4">Você pode ter digitado o endereço errado ou a página foi movida.</p>
            <a href="{{ route('home.index') }}" class="btn btn-primary">
                <i class="fas fa-home mr-2"></i>Página Inicial
            </a>
        </div>
    </div>
@stop
