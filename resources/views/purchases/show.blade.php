@extends('adminlte::page')

@section('title', 'Editar Compra')

@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Editar Compra #{{ str_pad($purchase->sequential, 5, '0', STR_PAD_LEFT) }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
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
        </div>

        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col-1">Código</th>
                            <th class="col-7">Produto</th>
                            <th class="col-1 text-right">Quantidade</th>
                            <th class="col-1 text-right">Valor Unit.</th>
                            <th class="col-1 text-right">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->items as $item)
                            <tr>
                                <td>{{ $item->product->sequential }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="text-right">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4">Total</td>
                            <td class="text-right">R$ {{ number_format($purchase->total, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="table-responsive pt-3">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col-2">Código</th>
                            <th class="col-5">Vencimento</th>
                            <th class="col-5 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->payables as $payable)
                            <tr>
                                <td>{{ $payable->sequential }}</td>
                                <td>{{ $payable->due_date->format('d/m/Y') }}</td>
                                <td class="text-right">R$ {{ number_format($payable->amount, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
