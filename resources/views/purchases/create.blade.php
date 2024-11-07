@extends('adminlte::page')

@section('title', 'Nova Compra')

@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Nova Compra</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Compras</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nova</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary create-entity mb-auto">Voltar</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="form-group col-md-9">
                        <label for="supplier-select">Fornecedor</label>
                        <select id="supplier-select" name="supplier_id"
                            class="form-control @error('supplier_id') is-invalid @enderror" style="width: 100%;"></select>
                        @error('supplier_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <x-input type="date" name="date" label="Data" value="{{ now()->format('Y-m-d') }}"
                        class="col-md-3" />

                    <input name="created_by" value="{{ auth()->id() }}" type="hidden" />
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('#supplier-select').select2({
                theme: 'bootstrap4',
                placeholder: 'Selecione um Fornecedor',
                ajax: {
                    url: "{{ route('ajax.suppliers.search') }}",
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
        });
    </script>
@endpush
