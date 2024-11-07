<div>
    @if (!$hasPayables)
        <form wire:submit="createPayables">
            <div class="row">
                <div class="form-group col-6">
                    <label for="payables_quantity">Quantidade Pagamentos</label>
                    <input wire:model="payables_quantity" type="number" class="form-control">
                </div>

                <div class="form-group col-6">
                    <label for="first_due_date">Primeiro Vencimento</label>
                    <input wire:model="first_due_date" type="date" class="form-control">
                </div>
            </div>

            @error('payables_quantity')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            @error('first_due_date')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary">Gerar Pagáveis</button>
        </form>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th class="col-2">Vencimento</th>
                <th class="col-2">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payables as $payable)
                <tr>
                    <td>
                        <input type="date" wire:model="payables.{{ $loop->index }}.due_date"
                            class="form-control @error('payables.' . $loop->index . '.due_date') is-invalid @enderror"
                            @disabled($hasPayables)>
                        @error('payables.' . $loop->index . '.due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="number" step="any" wire:model="payables.{{ $loop->index }}.amount"
                            class="form-control amount @error('payables.' . $loop->index . '.amount') is-invalid @enderror"
                            @disabled($hasPayables)>
                        @error('payables.' . $loop->index . '.amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Nenhuma parcela gerada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @error('payables')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    @if (!$hasPayables)
        <button wire:click="savePayables" class="btn btn-primary">Salvar Pagáveis</button>
    @endif
</div>
