![display](https://repository-images.githubusercontent.com/425272523/3708f28e-813a-4e29-8800-c1389632ffb9)

# Laravel 8 - Creating Crud with Jetstream Livewire & Tailwind Modal

This guide walks you through the process of building a Laravel 8 application that uses Jetstream Livewire and tailwind for UI.

Check this tutorial on my [Blog](https://dev.djamelkorei.me/laravel-8-creating-crud-with-jetstream-livewire-and-tailwind-modal) 👋
## What You Will build
You will build a Laravel application with full CRUD (Create, Read, Update, and Delete)

## What You Need
- A favorite text editor or IDE
- PHP >= 7.3
- Composer
- Node.js
- Npm

## Setup A New Project 
Create a new Laravel project by using Composer:
```bash
composer create-project laravel/laravel laravel-8-crud-jetstream-livewire-tailwind
cd laravel-8-crud-jetstream-livewire-tailwind
php artisan serve
``` 

#### Installing Jetstream
You may use Composer to install Jetstream into your new Laravel project:
```
composer require laravel/jetstream 
```

#### Install Jetstream With Livewire
```
php artisan jetstream:install livewire
```
#### Configure Database Connection
go to you `.env` file & update the database variables
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE= #you_database_name
DB_USERNAME= #your_username
DB_PASSWORD= #your_password
```

#### Finalizing The Installation
```
npm install
npm run dev
php artisan migrate
```

## Setup The Product Model
Create a new model using the Artisan CLI's command, `-mf` flag to create a migration and a factory for the product model
```
php artisan make:model Product -mf
``` 

#### Update The Migration Class
Go to the file `database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php` and update the table columns
```php
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->double('price', 8, 2);
        $table->boolean('active');
        $table->integer('user_id')->index();
        $table->timestamps();
    });
}
```

#### Update The Models
Go to the file `app/Models/Product.php` and update the product model class
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'active'];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
```

Go to the file `app/Models/user.php` and update the user model class
```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }
}

```

#### Update The Product Factory Class
Go to the file `database/factories/ProductFactory.php` and update the factory class
```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->randomNumber(2),
            'active' => $this->faker->boolean(),
            'user_id' => User::factory()
        ];
    }
}
```

#### Create New Records With Tinker
Before start creating the records, you should migrate the product table using Artisan CLI's command
```
php artisan migrate
```  
- Run the `tinker` Artisan command
- Create a user record
- Create the product records

```
php artisan tinker 
App\Models\User::factory()->count(1)->create(['name' => 'admin', 'email' => 'admin@admin.com']);
App\Models\Product::factory()->count(50)->create(['user_id' => 1]);
```

## Setup Livewire Product Component
Create a new livewire component using the Artisan CLI's command:
```
php artisan make:livewire products
``` 
#### Define The Product Route
Go to the file `routes/web.php` and add the product route
```php
Route::middleware(['auth:sanctum', 'verified'])->get('/products', function () {
    return view('products');
})->name('products');
```
#### Create The Product View
Create a new file `resources/views/products.blade.php` and pass the code below
```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:products />
            </div>
        </div>
    </div>
</x-app-layout>
```
#### Add Link To The Products View
Go to the file `resources/views/navigation-dropdown.blade.php` and update the componentn, pass the snippet code below
```html
<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-jet-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-jet-nav-link>
    
    <!-- Products Link -->
    <x-jet-nav-link href="{{ route('products') }}" :active="request()->routeIs('products')">
        {{ __('Products') }}
    </x-jet-nav-link>
</div>
```
```html
<!-- Responsive Navigation Menu -->
<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <x-jet-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-jet-responsive-nav-link>

        <!-- Products Link -->
        <x-jet-responsive-nav-link href="{{ route('products') }}" :active="request()->routeIs('products')">
            {{ __('Products') }}
        </x-jet-responsive-nav-link>
    </div>
```

## Setup The List Products
Go to the file `app/Http/Livewire/Products.php` and update the class
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class Products extends Component
{

    use WithPagination;

    public function render()
    {
        $products = Product::where('user_id', auth()->user()->id)->paginate(10);
        return view('livewire.products', [
            'products' => $products,
        ]);
    }
}
```
Go to the file `resources/views/livewire/products.blade.php` and update the view 
```html
<div class="p-6 sm:px-20 bg-white border-b border-gray-200">

    {{-- Header Section --}}
    <div class="mt-8 pb-4 text-2xl">
        <div>Products List</div>
    </div>

    {{-- Table Section --}}
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left">
                            ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left">
                            Name
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left">
                            Price
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left">
                            Active
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left">
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
                                {{-- Delete Button Action --}}
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

</div>
```

## Setup The Update Products

Go to the file `app/Http/Livewire/Products.php` and update the class
```php
public $product;
public $confirmingProductUpdate = false;

protected $rules = [
    'product.name' => 'required|string|min:4',
    'product.price' => 'required|numeric|between:1,100',
    'product.active' => 'boolean'
];
```
```php
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
```
Go to the file `resources/views/livewire/products.blade.php` and update the view
```html
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
```
```html
{{-- Edit Button Action --}}
<x-jet-button wire:click="confirmProductEdit( {{ $product->id }})"
    class="bg-orange-500 hover:bg-orange-700">
    Edit
</x-jet-button>
```
```html
{{-- Modal Section --}}
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
```
## Setup The Delete Products
Go to the file `app/Http/Livewire/Products.php` and update the class
```php
public $confirmingProductDeletion = false;
```
```php
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
```
Go to the file `resources/views/livewire/products.blade.php` and update the view
```html
{{-- Delete Button Action --}}
<x-jet-danger-button wire:click="confirmProductDeletion( {{ $product->id }})"
    wire:loading.attr="disabled">
    Delete
</x-jet-danger-button>
```
```html
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
```

## Setup The Alert Message
Go to the file `resources/views/livewire/products.blade.php` and update the view
```html
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
```

## Test
First run the the command bellow for compiling new assets 
```
npm run dev
```
We are ready to run our crud application
```
php artisan serve
```
Now you can open the URL bellow on your browser
```
http://localhost:8000/products
```
Login credentials `email=admin@admin.com` & `password=password`


## Summary

Congratulations 🎉 ! You have written a Full CRUD Application by using Laravel 8. You did it without having to write a single line of JavaScript and that is with the help of Livewire.

## Blog

Check new tutorials on my [Blog](https://dev.djamelkorei.me/) 👋
