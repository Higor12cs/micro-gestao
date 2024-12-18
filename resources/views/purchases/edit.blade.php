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
                    class="col-md-9" disabled />
                <x-input type="date" name="date" label="Data" value="{{ $purchase->date->format('Y-m-d') }}"
                    class="col-md-3" disabled />
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="col-6">Produto</th>
                            <th class="col-1">Quantidade</th>
                            <th class="col-2">Valor Unit.</th>
                            <th class="col-2">Valor Total</th>
                            <th class="col-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="new-item-row">
                            <td>
                                <select id="new-product-select" class="form-control" style="width: 100%;"></select>
                            </td>
                            <td>
                                <input type="number" id="new-quantity" class="form-control" min="1" value="1">
                            </td>
                            <td>
                                <input type="text" id="new-unit_cost" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" id="new-total-cost" class="form-control" readonly>
                            </td>
                            <td>
                                <button id="add-item-button" class="btn btn-sm btn-primary">Adicionar</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="items-table-body">
                    </tfoot>
                </table>
            </div>

            <h5 id="total-display" class="mt-3">Total: R$ 0,00</h5>

            <a href="{{ route('purchases.payables.index', $purchase->sequential) }}"
                class="btn btn-primary mt-3">Pagáveis</a>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            const formatCurrency = (value) =>
                new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);

            const parseCurrency = (formattedValue) =>
                parseFloat(formattedValue.replace(/[^0-9,-]+/g, '').replace(/\./g, '').replace(',', '.'));

            const purchaseId = "{{ $purchase->id }}";

            const fetchItems = () => {
                $.get(`/compras/${purchaseId}/itens`, function(data) {
                    let html = '';
                    let total = 0;

                    data.forEach(({
                        id,
                        product,
                        quantity,
                        unit_cost,
                        total_cost
                    }) => {
                        quantity = parseFloat(quantity);
                        unit_cost = parseFloat(unit_cost);
                        total_cost = parseFloat(total_cost);

                        total += total_cost;

                        html += `
                        <tr data-id="${id}">
                            <td>${product.name}</td>
                            <td>${quantity.toFixed(2)}</td>
                            <td>${formatCurrency(unit_cost)}</td>
                            <td>${formatCurrency(total_cost)}</td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-item">Excluir</button>
                            </td>
                        </tr>`;
                    });

                    $('#items-table-body').html(html);
                    $('#total-display').text(`Total: ${formatCurrency(total)}`);
                });
            };

            const initializeSelect2 = () => {
                $('#new-product-select').select2({
                    theme: 'bootstrap4',
                    ajax: {
                        url: '/ajax/products/search',
                        dataType: 'json',
                        delay: 250,
                        processResults: (data) => ({
                            results: data.map(product => ({
                                id: product.id,
                                text: product.text
                            }))
                        })
                    },
                    placeholder: 'Selecione um produto',
                    language: {
                        searching: () => "Pesquisando",
                        noResults: () => "Nenhum resultado encontrado."
                    }
                }).on('select2:select', function(e) {
                    const productId = e.params.data.id;
                    $.get(`/ajax/products/${productId}`, function(product) {
                        $('#new-unit_cost').val(formatCurrency(product.cost_price));
                        updateTotalCost();
                    });
                });
            };

            const updateTotalCost = () => {
                const quantity = $('#new-quantity').val();
                const unitCost = parseCurrency($('#new-unit_cost').val());
                const totalCost = (quantity * unitCost).toFixed(2);
                $('#new-total-cost').val(formatCurrency(totalCost));
            };

            const resetForm = () => {
                $('#new-product-select').val(null).trigger('change');
                $('#new-quantity').val(1);
                $('#new-unit_cost').val('');
                $('#new-total-cost').val('');
            };

            const addItem = () => {
                const productId = $('#new-product-select').val();
                const quantity = $('#new-quantity').val();
                const unitCost = parseCurrency($('#new-unit_cost').val());

                if (!productId || !quantity || !unitCost) {
                    alert('Preencha todos os campos antes de adicionar.');
                    return;
                }

                const formData = {
                    product_id: productId,
                    quantity,
                    unit_cost: unitCost
                };

                $.post(`/compras/${purchaseId}/itens`, formData, function() {
                    fetchItems();
                    resetForm();
                }).fail(function() {
                    alert('Erro ao adicionar o item. Tente novamente.');
                });
            };

            const deleteItem = (itemId) => {
                $.ajax({
                    url: `/compras/${purchaseId}/itens/${itemId}`,
                    type: 'DELETE',
                    success: fetchItems,
                    error: () => alert('Erro ao excluir o item.')
                });
            };

            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            $(document).on('click', '.delete-item', function() {
                const itemId = $(this).closest('tr').data('id');
                deleteItem(itemId);
            });

            $('#add-item-button').on('click', addItem);
            $('#new-quantity').on('input', updateTotalCost);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            initializeSelect2();
            fetchItems();
        });
    </script>
@endpush
