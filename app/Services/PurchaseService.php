<?php

namespace App\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseService
{
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();

            return Purchase::create($data);
        });
    }

    public function update(string $sequential, array $data): Purchase
    {
        return DB::transaction(function () use ($sequential, $data) {
            $purchase = $this->findBySequential($sequential);
            $purchase->update($data);

            return $purchase;
        });
    }

    public function delete(string $sequential): void
    {
        DB::transaction(function () use ($sequential) {
            $purchase = $this->findBySequential($sequential);

            if ($purchase->hasPayables()) {
                throw new \Exception('Não é possível deletar uma compra com contas a pagar.');
            }

            $purchase->delete();
        });
    }

    public function findBySequential(string $sequential): Purchase
    {
        return Purchase::query()
            ->where('sequential', $sequential)
            ->firstOrFail();
    }

    public function getDataTable()
    {
        $query = Purchase::query()->with(['supplier', 'payables']);

        return DataTables::eloquent($query)
            ->editColumn('sequential', fn ($purchase) => str_pad($purchase->sequential, 5, '0', STR_PAD_LEFT))
            ->editColumn('date', fn ($purchase) => $purchase->date->format('d/m/Y'))
            ->editColumn('total', fn ($purchase) => 'R$ '.number_format($purchase->total, 2, ',', '.'))
            ->addColumn('supplier', fn ($purchase) => $purchase->supplier->legal_name ?? $purchase->supplier->first_name)
            ->addColumn('finished', fn ($purchase) => view('partials.bool', ['bool' => $purchase->hasPayables()]))
            ->addColumn('actions', fn ($purchase) => view('partials.actions', [
                'id' => $purchase->id,
                'entity' => 'purchases',
                'modal' => false,
                'sequential' => $purchase->sequential,
                'edit' => ! $purchase->hasPayables(),
            ]))
            ->make(true);
    }
}
