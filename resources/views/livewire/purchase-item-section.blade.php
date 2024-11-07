<div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped text-nowrap">
            <thead>
                <tr>
                    <th class="col-1">Código</th>
                    <th class="col-7">Produto</th>
                    <th class="col-1">Quantidade</th>
                    <th class="col-1">Valor Unit.</th>
                    <th class="col-1">Valor Total</th>
                    @if (!$this->purchase->hasPayables())
                        <th class="col-1">Ações</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if (!$this->purchase->hasPayables())
                    <tr>
                        <td wire:ignore colspan="2">
                            <select id="product-select" name="product-select" style="width: 100%;"></select>
                        </td>
                        <td>
                            <input wire:model="quantity" type="text" class="form-control numeric" name="quantity"
                                onchange="updateTotal()">
                        </td>
                        <td>
                            <input wire:model="unit_price" type="text" class="form-control numeric" name="unit_price"
                                onchange="updateTotal()">
                        </td>
                        <td>
                            <input wire:model="total_price" type="text" class="form-control numeric"
                                name="total_price" readonly>
                        </td>
                        <td>
                            <button wire:click="add" wire:loading.attr="disabled" class="btn btn-sm btn-primary my-1">
                                Adicionar
                            </button>
                        </td>
                    </tr>
                @endif

                @forelse ($this->purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->sequential }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->quantity, 2, '.', ',') }}</td>
                        <td>{{ number_format($item->unit_price, 2, '.', ',') }}</td>
                        <td>{{ number_format($item->total_price, 2, '.', ',') }}</td>
                        @if (!$this->purchase->hasPayables())
                            <td>
                                <button wire:click="remove('{{ $item->id }}')" class="btn btn-sm btn-danger"
                                    @disabled($purchase->finished)>
                                    Excluir
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Nenhum produto adicionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h5 class="mb-3">Total: {{ number_format($purchase->total, 2, '.', ',') }}</h5>

    @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endforeach

    @if (!$purchase->finished)
        <button wire:click="payables" wire:loading.attr="disabled" class="btn btn-primary">
            Contas a Pagar
        </button>
    @endif

    <script>
        function updateTotal() {
            var quantity = document.getElementsByName('quantity')[0].value;
            var unit_price = document.getElementsByName('unit_price')[0].value;
            var total_price = quantity * unit_price;

            @this.set('quantity', quantity);
            @this.set('unit_price', unit_price);
            @this.set('total_price', total_price);
        }
    </script>
</div>

@script
    <script>
        $wire.on('item-added', (event) => {
            $('#product-select').val(null).trigger('change');
        });

        $(document).ready(function() {
            $('.numeric').inputmask('currency', {
                'rightAlign': false,
                'autoUnmask': true,
                'removeMaskOnSubmit': true,
                'digits': 2,
            });

            $('#product-select').select2({
                theme: 'bootstrap4',
                placeholder: 'Selecione um Produto',
                ajax: {
                    url: "{{ route('ajax.products.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        search: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                },
                language: {
                    searching: function() {
                        return "Pesquisando";
                    },
                    noResults: function() {
                        return "Nenhum resultado encontrado.";
                    },
                },
            }).on('select2:select', () => @this.set('product_id', $('#product-select').select2('val')));

            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        });
    </script>
@endscript
