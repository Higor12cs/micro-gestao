@extends('adminlte::page')

@section('title', 'Marcas')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Marcas</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Marcas</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary create-entity mb-auto" data-action-url="/ajax/brands">Novo Marca</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Lista de Marcas</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="brands-table" class="table data-table table-striped table-bordered" style="width:100%">
                    <thead class="text-nowrap">
                        <tr>
                            <th class="col-1">Código</th>
                            <th class="col-9">Nome</th>
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

            <div class="icheck-primary" title="Ativo">
                <input type="checkbox" name="active" id="active" checked>

                <label for="active">
                    Ativo
                </label>
            </div>

            <input name="created_by" value="{{ auth()->id() }}" type="hidden" />
        </form>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" form="crud-form" class="btn btn-primary">Salvar</button>
        </x-slot>
    </x-modal>

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
            $('#brands-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('ajax.brands.index') }}',
                columns: [{
                        data: 'sequential',
                        name: 'sequential'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'active',
                        name: 'active'
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
