<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home() { return $this->render(null); }
    public function table(int $table) { return $this->render($table); }

    private function render(?int $table)
    {
        $products = Product::where('available', true)->orderBy('category')->orderBy('name')->get();
        $categories = $products->pluck('category')->unique()->values();
        return view('storefront', compact('products', 'categories', 'table'));
    }
}
