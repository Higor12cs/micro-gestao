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
                            <th class="col-2">Valor Unitário</th>
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
                                <input type="text" id="new-unit-price" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" id="new-total-price" class="form-control" readonly>
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

            <a href="{{ route('purchases.payables.index', $purchase->sequential) }}" class="btn btn-primary mt-3">Contas a
                Pagar</a>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const purchaseId = "{{ $purchase->id }}";

            const fetchItems = () => {
                $.get(`/compras/${purchaseId}/itens`, function(data) {
                    let html = '';
                    let total = 0;

                    data.forEach(item => {
                        const quantity = parseFloat(item.quantity);
                        const unitPrice = parseFloat(item.unit_price);
                        const totalPrice = parseFloat(item.quantity) * unitPrice;
                        total += totalPrice;

                        html += `
                            <tr data-id="${item.id}">
                                <td>${item.product.name}</td>
                                <td>${quantity.toFixed(2)}</td>
                                <td>R$ ${unitPrice.toFixed(2)}</td>
                                <td>R$ ${totalPrice.toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-item">Excluir</button>
                                </td>
                            </tr>`;
                    });

                    $('#items-table-body').html(html);
                    $('#total-display').text(`Total: R$ ${total}`);
                });
            };

            $('#new-product-select').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '/ajax/products/search',
                    dataType: 'json',
                    delay: 250,
                    processResults: (data) => ({
                        results: data.map(product => ({
                            id: product.id,
                            text: `${product.text}`,
                        })),
                    }),
                },
                placeholder: 'Selecione um produto',
                language: {
                    searching: function() {
                        return "Pesquisando";
                    },
                    noResults: function() {
                        return "Nenhum resultado encontrado.";
                    },
                },
            }).on('select2:select', function(e) {
                const productId = e.params.data.id;
                const productName = e.params.data.text;

                $.get(`/ajax/products/${productId}`, function(product) {
                    $('#new-unit-price').val(parseFloat(product.cost_price).toFixed(2));
                    updateTotalPrice();
                });
            });

            const updateTotalPrice = () => {
                const quantity = $('#new-quantity').val();
                const unitPrice = $('#new-unit-price').val();
                const totalPrice = (quantity * unitPrice).toFixed(2);
                $('#new-total-price').val(totalPrice);
            };
            $('#new-quantity').on('input', updateTotalPrice);

            $('#add-item-button').on('click', function() {
                const productId = $('#new-product-select').val();
                const quantity = $('#new-quantity').val();
                const unitPrice = $('#new-unit-price').val();

                if (!productId || !quantity || !unitPrice) {
                    alert('Preencha todos os campos antes de adicionar.');
                    return;
                }

                const formData = {
                    product_id: productId,
                    quantity: quantity,
                    unit_price: unitPrice,
                };

                $.post(`/compras/${purchaseId}/itens`, formData, function(response) {
                    fetchItems();
                    $('#new-product-select').val(null).trigger('change');
                    $('#new-quantity').val(1);
                    $('#new-unit-price').val('');
                    $('#new-total-price').val('');
                }).fail(function(xhr) {
                    alert('Erro ao adicionar o item. Tente novamente.');
                });

            });

            $(document).on('click', '.delete-item', function() {
                const itemId = $(this).closest('tr').data('id');

                $.ajax({
                    url: `/compras/${purchaseId}/itens/${itemId}`,
                    type: 'DELETE',
                    success: function() {
                        fetchItems();
                    },
                    error: function() {
                        alert('Erro ao excluir o item.');
                    }
                });
            });

            fetchItems();
        });
    </script>
@endpush
