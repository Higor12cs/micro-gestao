@props(['id', 'entity', 'modal' => true, 'sequential' => null, 'edit' => true])

<div class="text-nowrap">
    @if ($modal)
        <button data-id="{{ $id }}" class="btn btn-secondary btn-sm edit-entity"
            data-entity="{{ $entity }}">Editar</button>
    @else
        @if ($edit)
            <a href="{{ route($entity . '.edit', $sequential) }}" class="btn btn-secondary btn-sm edit-entity">Editar</a>
        @else
            <a href="{{ route($entity . '.show', $sequential) }}" class="btn btn-secondary btn-sm edit-entity">Visualizar</a>
        @endif
    @endif

    <button data-id="{{ $id }}" class="btn btn-danger btn-sm delete-entity" data-entity="{{ $entity }}"
        data-refresh-event="table-refresh">Excluir</button>
</div>
