@extends('adminlte::page')

@section('title', 'Fornecedores')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Fornecedores</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fornecedores</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary create-entity mb-auto" data-action-url="/ajax/suppliers">Novo Fornecedor</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="">Lista de Fornecedores</span>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="suppliers-table" class="table data-table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="col-1">Código</th>
                            <th class="col-3">Nome</th>
                            <th class="col-3">Sobrenome</th>
                            <th class="col-4">Razão Social</th>
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
                <x-input name="first_name" label="Nome" class="col-md-6" />
                <x-input name="last_name" label="Sobrenome" class="col-md-6" />
            </div>

            <div class="row">
                <x-input name="legal_name" label="Razão Social" class="col-md-12" />
            </div>

            <div class="row">
                <x-input name="cpf_cnpj" label="CPF/CNPJ" class="col-md-4" />
                <x-input name="rg" label="RG" class="col-md-4" />
                <x-input name="ie" label="Inscrição Estadual" class="col-md-4" />
            </div>

            <div class="row">
                <x-input type="date" name="birth_date" label="Data de Nascimento" class="col-md-3" />
                <x-input type="email" name="email" label="Email" class="col-md-3" />
                <x-input name="phone" label="Telefone" class="col-md-3" />
                <x-input name="whatsapp" label="WhatsApp" class="col-md-3" />
            </div>

            <div class="row">
                <x-input name="zip_code" label="CEP" class="col-md-3" />
                <x-input name="address" label="Endereço" class="col-md-7" />
                <x-input name="number" label="Número" class="col-md-2" />
            </div>

            <div class="row">
                <x-input name="complement" label="Complemento" class="col-md-6" />
                <x-input name="neighborhood" label="Bairro" class="col-md-6" />
            </div>

            <div class="row">
                <x-input name="city" label="Cidade" class="col-md-4" />
                <x-input name="state" label="Estado" class="col-md-4" />
                <x-input name="country" label="País" class="col-md-4" />
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
            //InputMask
            $('#cpf_cnpj').inputmask([
                '999.999.999-99',
                '99.999.999/9999-99'
            ], {
                'autoUnmask': true,
                'removeMaskOnSubmit': true,
            });

            $('#phone, #whatsapp').inputmask('(99) 9999-9999[9]', {
                'autoUnmask': true,
                'removeMaskOnSubmit': true,
            });

            //DataTable
            $('#suppliers-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '{{ asset('translations/dataTables_pt-BR.json') }}',
                },
                processing: true,
                serverSide: true,
                ajax: '{{ route('ajax.suppliers.index') }}',
                columns: [{
                        data: 'sequential',
                        name: 'sequential'
                    },
                    {
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name'
                    },
                    {
                        data: 'legal_name',
                        name: 'legal_name',
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
