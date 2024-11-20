<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Purchase;
use Livewire\Attributes\Rule;
use Livewire\Component;

class PurchaseItemSection extends Component
{
    public Purchase $purchase;

    public Product $product;

    #[Rule('required', message: 'O campo produto é obrigatório.')]
    #[Rule('exists:products,id', message: 'O produto selecionado é inválido.')]
    public $product_id;

    #[Rule('required', message: 'O campo quantidade é obrigatório.')]
    #[Rule('numeric', message: 'O campo quantidade deve ser um número.')]
    #[Rule('gt:0', message: 'O campo quantidade deve ser maior que 0.')]
    public $quantity = 1;

    #[Rule('required', message: 'O campo preço unitário é obrigatório.')]
    #[Rule('numeric', message: 'O campo preço unitário deve ser um número.')]
    #[Rule('gt:0', message: 'O campo preço unitário deve ser maior que 0.')]
    public $unit_price = 0;

    #[Rule('required', message: 'O campo preço total é obrigatório.')]
    #[Rule('numeric', message: 'O campo preço total deve ser um número.')]
    #[Rule('gt:0', message: 'O campo preço total deve ser maior que 0.')]
    public $total_price = 0;

    public function add()
    {
        if ($this->purchase->hasPayables()) {
            $this->addError('purchase', 'A compra já possui contas a pagar.');
            return;
        }

        $this->validate();

        $this->purchase->items()->create([
            'tenant_id' => auth()->user()->tenant_id,
            'product_id' => $this->product->id,
            'quantity' => $this->quantity,
            'unit_cost' => $this->product->cost_price,
            'total_cost' => $this->product->cost_price * $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'type' => 'in',
            'created_by' => auth()->user()->id,
        ]);

        $this->updateOrderValues();
        $this->reset('product_id', 'quantity', 'unit_price', 'total_price');
        $this->dispatch('item-added');
    }

    public function remove(string $id)
    {
        if ($this->purchase->hasPayables()) {
            $this->addError('purchase', 'A compra já possui contas a pagar.');
            return;
        }

        $this->purchase->items()->findOrFail($id)->delete();
        $this->updateOrderValues();
    }

    public function payables()
    {
        if ($this->purchase->total == 0) {
            $this->addError('purchase', 'O valor total da compra deve ser maior que zero.');
            return;
        }

        return to_route('purchases.payables', $this->purchase);
    }

    public function updatedProductId()
    {
        $this->product = Product::findOrFail($this->product_id);
        $this->unit_price = $this->product->cost_price;
        $this->total_price = $this->product->cost_price * $this->quantity;
    }

    private function updateOrderValues()
    {
        // TODO: Add the cost to the entities

        $this->purchase->update([
            'total' => $this->purchase->items()->sum('total_price'),
        ]);
    }
}
