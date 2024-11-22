@extends('adminlte::page')

@section('title', 'Recebíveis Pedido')

@section('plugins.Select2', true)
@section('plugins.moment', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Recebíveis Pedido #{{ str_pad($order->sequential, 5, '0', STR_PAD_LEFT) }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Recebíveis</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('orders.edit', $order->sequential) }}" class="btn btn-secondary create-entity mb-auto">Voltar</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <x-input type="text" name="customer" label="Fornecedor" value="{{ $order->customer->first_name }}"
                    class="col-md-9" :disabled="true" />

                <x-input type="date" name="date" label="Data" value="{{ $order->date->format('Y-m-d') }}"
                    class="col-md-3" :disabled="true" />
            </div>

            <div class="row">
                <x-input type="text" name="total" label="Valor Total"
                    value="R$ {{ number_format($order->total_price, 2, ',', '.') }}" class="col-md-12" :disabled="true" />
            </div>
        </div>

        <div class="card-body">
            <form id="receivables-form" method="POST" action="{{ route('orders.receivables.store', $order->sequential) }}">
                @csrf

                <div class="row">
                    <div class="form-group col-6">
                        <label for="receivables_quantity">Quantidade Pagamentos</label>
                        <input type="number" value="1" id="receivables_quantity" class="form-control" min="1">
                    </div>
                    <div class="form-group col-6">
                        <label for="first_due_date">Primeiro Vencimento</label>
                        <input type="date" value="{{ now()->addMonth()->format('Y-m-d') }}" id="first_due_date"
                            class="form-control">
                    </div>
                </div>
                <button type="button" id="generate-receivables" class="btn btn-primary">Gerar Recebíveis</button>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th class="col-5">Vencimento</th>
                            <th class="col-5">Valor</th>
                            <th class="col-2">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="receivables-table">
                        @if (old('receivables'))
                            @foreach (old('receivables') as $index => $receivable)
                                <tr>
                                    <td>
                                        <input type="date" name="receivables[{{ $index }}][due_date]"
                                            value="{{ $receivable['due_date'] }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="receivables[{{ $index }}][amount]"
                                            value="{{ $receivable['amount'] }}" class="form-control amount-input">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row">Excluir</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
                @error('receivables')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" id="store-receivables" class="btn btn-primary" disabled>Salvar Recebíveis</button>
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

                if (total.toFixed(2) == {{ $order->total_price }}) {
                    $('#total-sum').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#total-sum').removeClass('text-success').addClass('text-danger');
                }
            };

            const toggleSaveButton = () => {
                const hasRows = $('#receivables-table tr').length > 0;
                $('#store-receivables').prop('disabled', !hasRows);
            };

            $('#generate-receivables').on('click', function() {
                const quantity = parseInt($('#receivables_quantity').val());
                const firstDueDate = $('#first_due_date').val();
                const total = {{ $order->total_price }};
                const receivables = [];
                let receivablesTotal = 0;

                $('#receivables-table').empty();

                if (quantity > 0 && firstDueDate) {
                    for (let i = 0; i < quantity - 1; i++) {
                        const amount = Math.round(total / quantity);
                        receivablesTotal += amount;

                        receivables.push({
                            amount: amount,
                            due_date: moment(firstDueDate).add(i, 'months').format('YYYY-MM-DD')
                        });
                    }

                    receivables.push({
                        amount: total - receivablesTotal,
                        due_date: moment(firstDueDate).add(quantity - 1, 'months').format(
                            'YYYY-MM-DD')
                    });

                    receivables.forEach((receivable, index) => {
                        $('#receivables-table').append(`
                            <tr>
                                <td>
                                    <input type="date" name="receivables[${index}][due_date]" value="${receivable.due_date}" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="receivables[${index}][amount]" value="${receivable.amount}" class="form-control amount-input">
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
