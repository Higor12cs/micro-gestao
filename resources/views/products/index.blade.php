@extends('adminlte::page')

@section('title', 'Produtos')

@section('plugins.Datatables', true)
@section('plugins.Inputmask', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Produtos</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Produtos</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary create-entity mb-auto" data-action-url="/ajax/products">Novo Produto</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Lista de Produtos</span>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="products-table" class="table data-table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="col-1">Código</th>
                            <th class="col-1">Código de Barras</th>
                            <th class="col-7">Nome</th>
                            <th class="col-1">Disponível</th>
                            <th class="col-1">Ativo</th>
                            <th class="col-1">Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <x-modal id="crud-modal" size="lg" class="modal-dialog-scrollable">
        <form id="crud-form">
            <div class="row">
                <x-input name="name" label="Nome" class="col-12" />
            </div>

            <div class="row">
                <x-input name="barcode" label="Código de Barras" class="col-12" />
            </div>

            <div class="row">
                <x-adminlte-select name="section_id" label="Seção" fgroup-class="col-md-4">
                    <option value="">-</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </x-adminlte-select>

                <x-adminlte-select name="group_id" label="Grupo" fgroup-class="col-md-4">
                    <option value="">-</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </x-adminlte-select>

                <x-adminlte-select name="brand_id" label="Marca" fgroup-class="col-md-4">
                    <option value="">-</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

            <div class="row">
                <x-input name="cost_price" label="Preço de Custo" class="col-md-4 money" value="0" />
                <x-input name="sale_price" label="Preço de Venda" class="col-md-4 money" value="0" />
                <x-input name="minimum_stock" label="Estoque Mínimo" class="col-md-4 number" value="0" />
            </div>

            <div class="icheck-primary" title="Ativo">
                <input type="checkbox" name="active" id="active" checked>
                <label for="active">
                    Ativo
                </label>
            </div>

            <input name="created_by" value="{{ auth()->id() }}" type="hidden" />
        </form>
    </x-modal>

    <x-modal id="confirm-delete-modal" title="Excluir Registro" size="md">
        <span>Tem certeza que deseja excluir este registro?</span>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-times"></i>
                &nbsp;
                Cancelar
            </button>
            <button type="button" id="confirm-delete" class="btn btn-danger">
                <i class="fas fa-trash"></i>
                &nbsp;
                Excluir
            </button>
        </x-slot>
    </x-modal>
@stop

@push('js')
    <script src="{{ asset('js/crud.js') }}" type="module"></script>
    <script>
        $(document).ready(function() {
            //InputMask
            $('#cost_price').inputmask('currency', {
                'autoUnmask': true,
                'removeMaskOnSubmit': true,
                'allowMinus': false,
                'prefix': 'R$ ',
                'digits': 2,
            });

            $('#sale_price').inputmask('currency', {
                'autoUnmask': true,
                'removeMaskOnSubmit': true,
                'allowMinus': false,
                'prefix': 'R$ ',
                'digits': 2,
            });

            $('#minimum_stock').inputmask('numeric', {
                'autoUnmask': true,
                'removeMaskOnSubmit': true,
                'allowMinus': false,
            });

            //DataTable
            $('#products-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('ajax.products.index') }}',
                columns: [{
                        data: 'sequential',
                        name: 'sequential',
                    },
                    {
                        data: 'barcode',
                        name: 'barcode',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'stock_total',
                        name: 'stock_total',
                    },
                    {
                        data: 'active',
                        name: 'active',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true
            });
        });
    </script>
@endpush
