@extends('adminlte::page')

@section('title', 'Contas')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Contas</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contas</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary create-entity mb-auto" data-action-url="/ajax/accounts">Novo Marca</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Lista de Contas</span>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="accounts-table" class="table data-table table-striped table-bordered" style="width:100%">
                    <thead>
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
            <x-adminlte-input name="name" label="Nome" />

            <div class="row">
                <x-adminlte-input name="branch" label="Agência" fgroup-class="col-md-6" />
                <x-adminlte-input name="account" label="Conta" fgroup-class="col-md-6" />
            </div>

            <x-adminlte-select name="type" label="Tipo">
                <option value="">-</option>
                <option value="checking_account">Conta Corrente</option>
                <option value="cash_account">Caixa</option>
            </x-adminlte-select>

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
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" id="confirm-delete" class="btn btn-danger">Excluir</button>
        </x-slot>
    </x-modal>
@stop

@section('js')
    <script src="{{ asset('js/crud.js') }}" type="module"></script>
    <script>
        $(document).ready(function() {
            $('#accounts-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('ajax.accounts.index') }}',
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
