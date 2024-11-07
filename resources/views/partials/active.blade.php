@props(['active'])

@if ($active)
    <span class="badge badge-success">Ativo</span>
@else
    <span class="badge badge-danger">Inativo</span>
@endif
