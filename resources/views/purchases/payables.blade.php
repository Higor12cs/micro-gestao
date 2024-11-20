@extends('adminlte::page')

@section('title', 'Pagáveis Compra')

@section('plugins.Select2', true)
@section('plugins.moment', true)

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
                    value="R$ {{ number_format($purchase->total, 2, ',', '.') }}" class="col-md-12" :disabled="true" />
            </div>
        </div>

        <div class="card-body">
            <form id="payables-form" method="POST" action="{{ route('purchases.store-payables', $purchase->sequential) }}">
                @csrf

                <div class="row">
                    <div class="form-group col-6">
                        <label for="payables_quantity">Quantidade Pagamentos</label>
                        <input type="number" value="1" id="payables_quantity" class="form-control" min="1">
                    </div>
                    <div class="form-group col-6">
                        <label for="first_due_date">Primeiro Vencimento</label>
                        <input type="date" value="{{ now()->addMonth()->format('Y-m-d') }}" id="first_due_date"
                            class="form-control">
                    </div>
                </div>
                <button type="button" id="generate-payables" class="btn btn-primary">Gerar Pagáveis</button>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th class="col-5">Vencimento</th>
                            <th class="col-5">Valor</th>
                            <th class="col-2">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="payables-table">
                        @if (old('payables'))
                            @foreach (old('payables') as $index => $payable)
                                <tr>
                                    <td>
                                        <input type="date" name="payables[{{ $index }}][due_date]"
                                            value="{{ $payable['due_date'] }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="payables[{{ $index }}][amount]"
                                            value="{{ $payable['amount'] }}" class="form-control amount-input">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row">Excluir</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                @error('payables')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" id="store-payables" class="btn btn-primary" disabled>Salvar Pagáveis</button>
                    <h5 class="mt-auto">Total das Parcelas: <span id="total-sum">R$ 0,00</span></h5>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            const formatCurrency = (value) => {
                return value.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
            };

            const updateTotalSum = () => {
                let total = 0;
                $('.amount-input').each(function() {
                    const value = parseFloat($(this).inputmask('unmaskedvalue') || 0);
                    total += value;
                });
                $('#total-sum').text(formatCurrency(total));

                if (total.toFixed(2) == {{ $purchase->total }}) {
                    $('#total-sum').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#total-sum').removeClass('text-success').addClass('text-danger');
                }
            };

            const toggleSaveButton = () => {
                const hasRows = $('#payables-table tr').length > 0;
                $('#store-payables').prop('disabled', !hasRows);
            };

            $('#generate-payables').on('click', function() {
                const quantity = parseInt($('#payables_quantity').val());
                const firstDueDate = $('#first_due_date').val();
                const total = {{ $purchase->total }};
                const payables = [];
                let payablesTotal = 0;

                $('#payables-table').empty();

                if (quantity > 0 && firstDueDate) {
                    for (let i = 0; i < quantity - 1; i++) {
                        const amount = Math.round(total / quantity);
                        payablesTotal += amount;

                        payables.push({
                            amount: amount,
                            due_date: moment(firstDueDate).add(i, 'months').format('YYYY-MM-DD')
                        });
                    }

                    payables.push({
                        amount: total - payablesTotal,
                        due_date: moment(firstDueDate).add(quantity - 1, 'months').format(
                            'YYYY-MM-DD')
                    });

                    payables.forEach((payable, index) => {
                        $('#payables-table').append(`
                            <tr>
                                <td>
                                    <input type="date" name="payables[${index}][due_date]" value="${payable.due_date}" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="payables[${index}][amount]" value="${payable.amount}" class="form-control amount-input">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">Excluir</button>
                                </td>
                            </tr>
                        `);
                    });

                    $('.amount-input').inputmask('currency', {
                        'autoUnmask': true,
                        'removeMaskOnSubmit': true,
                        'allowMinus': false,
                        'prefix': 'R$ ',
                        'digits': 2,
                    });

                    updateTotalSum();
                    toggleSaveButton();
                }
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateTotalSum();
                toggleSaveButton();
            });

            $(document).on('input', '.amount-input', function() {
                updateTotalSum();
            });

            toggleSaveButton();
        });
    </script>
@endpush
