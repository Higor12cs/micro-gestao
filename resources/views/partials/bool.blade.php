@props(['bool'])

@if ($bool)
    <span class="badge badge-success">Sim</span>
@else
    <span class="badge badge-danger">Não</span>
@endif
