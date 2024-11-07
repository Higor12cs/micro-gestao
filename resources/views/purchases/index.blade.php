@extends('adminlte::page')

@section('title', 'Compras')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Compras</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Compras</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary create-entity mb-auto">Nova Compra</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Lista de Compras</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="purchases-table" class="table data-table table-striped table-bordered" style="width:100%">
                    <thead class="text-nowrap">
                        <tr>
                            <th class="col-1">Código</th>
                            <th class="col-1">Data</th>
                            <th class="col-7">Fornecedor</th>
                            <th class="col-1">Finalizado?</th>
                            <th class="col-1">Valor</th>
                            <th class="col-1">Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <x-modal id="confirm-delete-modal" title="Excluir Registro" size="md">
        <span>Tem certeza que deseja excluir este registro?</span>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" id="confirm-delete" class="btn btn-danger">Excluir</button>
        </x-slot>
    </x-modal>
@stop

@section('js')
    <script src="{{ asset('js/crud.js') }}" type="module"></script>
    <script>
        $(document).ready(function() {
            $('#purchases-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('ajax.purchases.index') }}',
                columns: [{
                        data: 'sequential',
                        name: 'sequential'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'finished',
                        name: 'finished'
                    },
                    {
                        data: 'total',
                        name: 'total'
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
@stop
