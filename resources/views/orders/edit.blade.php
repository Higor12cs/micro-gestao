@extends('adminlte::page')

@section('title', 'Editar Pedido')

@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Editar Pedido #{{ str_pad($order->sequential, 5, '0', STR_PAD_LEFT) }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary create-entity mb-auto">Voltar</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <x-input type="text" name="customer" label="Fornecedor" value="{{ $order->customer->first_name }}"
                    class="col-md-9" disabled />
                <x-input type="date" name="date" label="Data" value="{{ $order->date->format('Y-m-d') }}"
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
                                <input type="text" id="new-unit_price" class="form-control" readonly>
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

            <a href="{{ route('orders.receivables.index', $order->sequential) }}" class="btn btn-primary mt-3">Recebíveis</a>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // ---------- Helpers ----------
            const formatCurrency = (value) => {
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            };  

            const parseCurrency = (formattedValue) => {
                return parseFloat(
                    formattedValue
                    .replace(/[^0-9,-]+/g, '') 
                    .replace(/\./g, '') 
                    .replace(',', '.') 
                );
            };

            // ---------- Configuração Inicial ----------
            const orderId = "{{ $order->id }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inicializa o Select2 para produtos
            const initSelect2 = () => {
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
                            })),
                        }),
                    },
                    placeholder: 'Selecione um produto',
                    language: {
                        searching: () => "Pesquisando...",
                        noResults: () => "Nenhum resultado encontrado.",
                    },
                }).on('select2:select', function(e) {
                    handleProductSelect(e.params.data.id);
                });
            };

            // ---------- Funções de Atualização ----------
            const updateTotalPrice = () => {
                const quantity = parseFloat($('#new-quantity').val()) || 0;
                const unitPrice = parseCurrency($('#new-unit_price').val()) || 0;
                const totalPrice = (quantity * unitPrice).toFixed(2);

                $('#new-total-price').val(formatCurrency(totalPrice));
            };

            const updateItemsTable = (items) => {
                let html = '';
                let total = 0;

                items.forEach(item => {
                    const quantity = parseFloat(item.quantity);
                    const unitPrice = parseFloat(item.unit_price);
                    const totalPrice = parseFloat(item.total_price);
                    total += totalPrice;

                    html += `
                    <tr data-id="${item.id}">
                        <td>${item.product.name}</td>
                        <td>${quantity.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td>
                        <td>${formatCurrency(unitPrice)}</td>
                        <td>${formatCurrency(totalPrice)}</td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-item">Excluir</button>
                        </td>
                    </tr>`;
                });

                $('#items-table-body').html(html);
                $('#total-display').text(`Total: ${formatCurrency(total)}`);
            };

            // ---------- Requisições AJAX ----------
            const fetchItems = () => {
                $.get(`/pedidos/${orderId}/itens`, function(data) {
                    updateItemsTable(data);
                });
            };

            const handleProductSelect = (productId) => {
                $.get(`/ajax/products/${productId}`, function(product) {
                    $('#new-unit_price').val(formatCurrency(product.sale_price));
                    updateTotalPrice();
                });
            };

            const addItem = () => {
                const productId = $('#new-product-select').val();
                const quantity = $('#new-quantity').val();
                const unitPrice = $('#new-unit_price').val();

                if (!productId || !quantity || !unitPrice) {
                    alert('Preencha todos os campos antes de adicionar.');
                    return;
                }

                const formData = {
                    product_id: productId,
                    quantity: quantity,
                    unit_price: parseCurrency(unitPrice),
                };

                $.post(`/pedidos/${orderId}/itens`, formData, function() {
                    resetForm();
                    fetchItems();
                }).fail(function() {
                    alert('Erro ao adicionar o item. Tente novamente.');
                });
            };

            const deleteItem = (orderItemId) => {
                $.ajax({
                    url: `/pedidos/${orderId}/itens/${orderItemId}`,
                    type: 'DELETE',
                    success: fetchItems,
                    error: () => alert('Erro ao excluir o item.'),
                });
            };

            // ---------- Handlers de Eventos ----------
            $('#new-quantity').on('input', updateTotalPrice);

            $('#add-item-button').on('click', addItem);

            $(document).on('click', '.delete-item', function() {
                const orderItemId = $(this).closest('tr').data('id');
                deleteItem(orderItemId);
            });

            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            // ---------- Helpers e Inicialização ----------
            const resetForm = () => {
                $('#new-product-select').val(null).trigger('change');
                $('#new-quantity').val(1);
                $('#new-unit_price').val('');
                $('#new-total-price').val('');
            };

            const init = () => {
                initSelect2();
                fetchItems();
            };

            init(); // Inicializa o script
        });
    </script>
@endpush
