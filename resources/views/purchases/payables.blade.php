@extends('adminlte::page')

@section('title', 'Pagáveis Compra')

@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Pagáveis Compra #{{ str_pad($purchase->sequential, 5, '0', STR_PAD_LEFT) }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pagáveis</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary create-entity mb-auto">Voltar</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <x-input type="text" name="supplier" label="Fornecedor" value="{{ $purchase->supplier->first_name }}"
                    class="col-md-9" :disabled="true" />

                <x-input type="date" name="date" label="Data" value="{{ $purchase->date->format('Y-m-d') }}"
                    class="col-md-3" :disabled="true" />
            </div>

            <div class="row">
                <x-input type="text" name="total" label="Valor Total"
                    value="{{ number_format($purchase->total, 2, '.', ',') }}" class="col-md-12" :disabled="true" />
            </div>
        </div>

        <div class="card-body">
            <livewire:purchase-payable-section :purchase="$purchase" />
        </div>
    </div>
@stop
