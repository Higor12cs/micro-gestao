@extends('adminlte::page')

@section('title', 'Relatórios de Pedidos')

@section('plugins.DateRangePicker', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Relatórios de Pedidos</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Relatórios</li>
                </ol>
            </nav>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Relatórios de Pedidos</span>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.orders.report') }}" method="POST" target="_blank">
                @csrf
                <x-adminlte-date-range name="date" />
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </form>
        </div>
    </div>
@stop
