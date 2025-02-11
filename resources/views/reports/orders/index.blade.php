@extends('adminlte::page')

@section('title', 'Relatórios de Pedidos')

@section('plugins.DateRangePicker', true)

@php
    $config = [
        'startDate' => "js:moment().startOf('month')",
        'endDate' => "js:moment().endOf('month')",
        'locale' => [
            'format' => 'DD/MM/YYYY', // Formato brasileiro sem horas
            'applyLabel' => 'Aplicar',
            'cancelLabel' => 'Cancelar',
            'fromLabel' => 'De',
            'toLabel' => 'Até',
            'customRangeLabel' => 'Personalizado',
            'daysOfWeek' => ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
            'monthNames' => [
                'Janeiro',
                'Fevereiro',
                'Março',
                'Abril',
                'Maio',
                'Junho',
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro',
            ],
            'firstDay' => 0,
        ],
        'alwaysShowCalendars' => true,
        'showCustomRangeLabel' => true,
        'ranges' => [
            'Hoje' => ['js:moment()', 'js:moment()'],
            'Ontem' => ["js:moment().subtract(1, 'days')", "js:moment().subtract(1, 'days')"],
            'Últimos 7 dias' => ["js:moment().subtract(6, 'days')", 'js:moment()'],
            'Últimos 30 dias' => ["js:moment().subtract(29, 'days')", 'js:moment()'],
            'Este mês' => ["js:moment().startOf('month')", "js:moment().endOf('month')"],
            'Mês passado' => [
                "js:moment().subtract(1, 'month').startOf('month')",
                "js:moment().subtract(1, 'month').endOf('month')",
            ],
        ],
    ];
@endphp

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
                <x-adminlte-date-range name="date" :config="$config" />
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-print"></i>
                    &nbsp;
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>
@stop
