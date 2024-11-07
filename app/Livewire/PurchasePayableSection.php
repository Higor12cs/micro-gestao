<?php

namespace App\Livewire;

use App\Models\Purchase;
use Livewire\Component;

class PurchasePayableSection extends Component
{
    public Purchase $purchase;

    public bool $hasPayables = false;

    public $payables_quantity = 1;

    public $first_due_date;

    public $payables = [];

    public function mount(Purchase $purchase)
    {
        if ($purchase->payables->count() > 0) {
            $this->hasPayables = true;
            $this->payables = $purchase->payables->map(function ($payable) {
                return [
                    'due_date' => $payable->due_date->format('Y-m-d'),
                    'amount' => $payable->amount,
                ];
            })->toArray();
        }

        $this->purchase = $purchase;
        $this->first_due_date = $this->first_due_date ?: now()->format('Y-m-d');
    }

    public function createPayables()
    {
        $this->validate([
            'payables_quantity' => 'required|integer|min:1',
            'first_due_date' => 'required|date|after_or_equal:today',
        ], [
            'payables_quantity.required' => 'O campo quantidade de parcelas é obrigatório.',
            'payables_quantity.integer' => 'O campo quantidade de parcelas deve ser um número inteiro.',
            'payables_quantity.min' => 'O campo quantidade de parcelas deve ser no mínimo 1.',
            'first_due_date.required' => 'O campo data de vencimento da primeira parcela é obrigatório.',
            'first_due_date.date' => 'O campo data de vencimento da primeira parcela deve ser uma data válida.',
            'first_due_date.after_or_equal' => 'O campo data de vencimento da primeira parcela deve ser uma data igual ou posterior a hoje.',
        ]);

        $this->payables = [];
        $payables_total = 0;

        for ($i = 0; $i < $this->payables_quantity - 1; $i++) {
            $this->payables[] = [
                'amount' => round($this->purchase->total / $this->payables_quantity, 0),
                'due_date' => \Carbon\Carbon::parse($this->first_due_date)->addMonths($i)->format('Y-m-d'),
            ];

            $payables_total += round($this->purchase->total / $this->payables_quantity, 0);
        }

        $this->payables[] = [
            'amount' => $this->purchase->total - $payables_total,
            'due_date' => \Carbon\Carbon::parse($this->payables ? max(array_column($this->payables, 'due_date')) : $this->first_due_date)->addMonth()->format('Y-m-d'),
        ];
    }

    public function savePayables()
    {
        $this->validate([
            'payables.*.due_date' => 'required|date|after_or_equal:today',
            'payables.*.amount' => 'required|numeric|min:0.01',
        ], [
            'payables.*.due_date.required' => 'O campo data de vencimento é obrigatório.',
            'payables.*.due_date.date' => 'O campo data de vencimento deve ser uma data válida.',
            'payables.*.due_date.after_or_equal' => 'O campo data de vencimento deve ser uma data igual ou posterior a hoje.',
            'payables.*.amount.required' => 'O campo valor é obrigatório.',
            'payables.*.amount.numeric' => 'O campo valor deve ser um número.',
            'payables.*.amount.min' => 'O campo valor deve ser no mínimo R$ 0,01.',
        ]);

        // The payables total amount must be equal to the purchase total amount
        if (array_sum(array_column($this->payables, 'amount')) != $this->purchase->total) {
            $this->addError('payables', 'A soma dos valores das parcelas deve ser igual ao valor total da compra.');

            return;
        }

        // Loop through each payable and add tenant_id
        $payables = collect($this->payables)->map(function ($payable) {
            $payable['tenant_id'] = $this->purchase->tenant_id;
            $payable['supplier_id'] = $this->purchase->supplier_id;
            $payable['created_by'] = auth()->id();

            return $payable;
        })->toArray();

        // Now call createMany
        $this->purchase->payables()->createMany($payables);

        return to_route('purchases.index');
    }
}
