<div class="p-6 sm:px-20 bg-white border-b border-gray-200">

    @if (session()->has('message'))
        <div class="relative flex shadow bg-indigo-500 text-white text-sm font-bold p-4" role="alert"
            x-data="{show: true}" x-show="show">
            <p>{{ session('message') }}</p>
            <button role="button" aria-label="close alert" class="absolute top-0 bottom-0 right-0 p-4"
                @click="show = false">
                ×
            </button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="mt-8 pb-4 text-2xl flex justify-between">
        <div>Products List</div>
        {{-- Add Button Action --}}
        <div class="mr-2">
            <x-jet-button wire:click="confirmProductAdd" class="bg-indigo-700 hover:bg-indigo-900">
                Add Product
            </x-jet-button>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    Active
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">

                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-6 py-4">
                                        {{ $product->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ number_format($product->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} ">
                                            {{ $product->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{-- Edit Button Action --}}
                                        <x-jet-button wire:click="confirmProductEdit( {{ $product->id }})"
                                            class="bg-orange-500 hover:bg-orange-700">
                                            Edit
                                        </x-jet-button>
                                        {{-- Delete Button Action --}}
                                        <x-jet-danger-button wire:click="confirmProductDeletion( {{ $product->id }})"
                                            wire:loading.attr="disabled">
                                            Delete
                                        </x-jet-danger-button>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Section --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>

    {{-- Modal Section --}}
    <x-jet-confirmation-modal wire:model="confirmingProductDeletion">
        <x-slot name="title">
            {{ __('Delete Product') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete Product? ') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingProductDeletion', false)" wire:loading.attr="disabled">
                {{ __('Conceal') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteProduct({{ $confirmingProductDeletion }})"
                wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

    <x-jet-dialog-modal wire:model="confirmingProductUpdate">
        <x-slot name="title">
            {{ isset($this->product->id) ? 'Edit Product' : 'Add Product' }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="product.name" />
                <x-jet-input-error for="product.name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4 mt-4">
                <x-jet-label for="price" value="{{ __('Price') }}" />
                <x-jet-input id="price" type="text" class="mt-1 block w-full" wire:model.defer="product.price" />
                <x-jet-input-error for="product.price" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4 mt-4">
                <label class="flex products-center">
                    <input type="checkbox" wire:model.defer="product.active" class="form-checkbox" />
                    <span class="ml-2 text-sm text-gray-600">Active</span>
                </label>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingProductUpdate', false)" wire:loading.attr="disabled">
                {{ __('Conceal') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="saveProduct()" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>

</div>
