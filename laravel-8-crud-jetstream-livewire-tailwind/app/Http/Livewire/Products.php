<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class Products extends Component
{

    use WithPagination;


    public $product;
    public $confirmingProductUpdate = false;
    public $confirmingProductDeletion = false;

    protected $rules = [
        'product.name' => 'required|string|min:4',
        'product.price' => 'required|numeric|between:1,100',
        'product.active' => 'boolean'
    ];

    public function render()
    {
        $products = Product::where('user_id', auth()->user()->id)->paginate(10);
        return view('livewire.products', [
            'products' => $products,
        ]);
    }

    public function confirmProductAdd()
    {
        $this->reset(['product']);
        $this->confirmingProductUpdate = true;
    }

    public function confirmProductEdit(Product $product)
    {
        $this->resetErrorBag();
        $this->product = $product;
        $this->confirmingProductUpdate = true;
    }

    public function saveProduct()
    {
        $this->validate();

        if (isset($this->product->id)) {
            $this->product->save();
            session()->flash('message', 'Product Saved Successfully');
        } else {
            auth()->user()->products()->create([
                'name' => $this->product['name'],
                'price' => $this->product['price'],
                'active' => $this->product['active'] ?? 0
            ]);
            session()->flash('message', 'Product Added Successfully');
        }

        $this->confirmingProductUpdate = false;
    }


    public function confirmProductDeletion($id)
    {
        $this->confirmingProductDeletion = $id;
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        $this->confirmingProductDeletion = false;
        session()->flash('message', 'Product Deleted Successfully');
    }
}
