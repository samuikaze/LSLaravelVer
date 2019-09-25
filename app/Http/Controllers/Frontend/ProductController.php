<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::orderBy('prodOrder')->get();
        $bc = [
            ['url' => route('product'), 'name' => '作品一覽']
        ];
        return view('frontend.product', compact('product', 'bc'));
    }
}
