@extends('adminlte::page')

@section('title', 'Kardex')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Kardex</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kardex</li>
                </ol>
            </nav>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('kardex.redirect') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="form-group col-12">
                        <label for="product-select">Produto</label>
                        <select id="product-select" name="product_id"
                            class="form-control @error('product_id') is-invalid @enderror" style="width: 100%;"></select>
                        @error('product_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </div>
    </div>

    @if ($product)
        <div class="card mt-3">
            <div class="card-header">Movimentações do Produto: {{ $product->name }}</div>
            <div class="card-body">
                <table id="kardex-table" class="table table-bordered table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Custo Unitário</th>
                            <th>Custo Total</th>
                            <th>Documento Relacionado</th>
                            <th>Usuário</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('#product-select').select2({
                theme: 'bootstrap4',
                placeholder: 'Selecione um Produto',
                ajax: {
                    url: "{{ route('ajax.products.search') }}",
                    dataType: "json",
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
            });

            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            @if ($product)
                $('#kardex-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('kardex.movements', ['product' => $product->sequential]) }}',
                    language: {
                        url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                    },
                    columns: [{
                            data: 'created_at.timestamp',
                            name: 'created_at'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'unit_cost',
                            name: 'unit_cost'
                        },
                        {
                            data: 'total_cost',
                            name: 'total_cost'
                        },
                        {
                            data: 'related_document',
                            name: 'related_document'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                    ],
                    columnDefs: [{
                        targets: [0],
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return row.created_at.display;
                            }
                            return data;
                        }
                    }]
                });
            @endif
        });
    </script>
@endpush
