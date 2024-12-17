@extends('adminlte::page')

@section('title', 'Usuários')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Usuários</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Usuários</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary create-entity mb-auto" data-action-url="/ajax/users">Novo Usuário</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Lista de Usuários</span>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="users-table" class="table data-table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="col-1">Código</th>
                            <th class="col-5">Nome</th>
                            <th class="col-4">Email</th>
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
                <x-input name="email" label="Email" class="col-12" />
            </div>

            <div class="row">
                <x-input name="password" label="Senha" class="col-6" type="password" />
                <x-input name="password_confirmation" label="Confirmação de Senha" class="col-6" type="password" />
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

@section('js')
    <script src="{{ asset('js/crud.js') }}" type="module"></script>
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('ajax.users.index') }}',
                columns: [{
                        data: 'sequential',
                        name: 'sequential'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
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
