@props(['id', 'title' => 'Novo Registro', 'footer' => null, 'size' => 'md'])

<div class="modal fade" id="{{ $id }}" role="dialog" aria-labelledby="{{ $id }}-label"
    aria-hidden="true" style="overflow:hidden;">
    <div {{ $attributes->merge(['class' => 'modal-dialog modal-' . $size]) }} role="document">
        <div class="modal-content">
            @if ($title)
                <div class="modal-header">
                    @if ($title)
                        <h5 class="modal-title" id="{{ $id }}-label">{{ $title }}</h5>
                    @endif

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="modal-body">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @else
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        &nbsp;
                        Cancelar
                    </button>
                    <button type="submit" form="crud-form" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        &nbsp;
                        Salvar
                    </button>
                </div>
            @endisset
        </div>
    </div>
</div>
