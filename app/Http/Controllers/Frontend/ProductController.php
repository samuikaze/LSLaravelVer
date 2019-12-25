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
        $reldate = [];
        foreach($product as $pd){
            array_push($reldate, (empty($pd->prodRelDate)) ? '發售日未定' : date("Y / m / d", strtotime($pd->prodRelDate)));
        }
        $bc = [
            ['url' => route('product'), 'name' => '作品一覽']
        ];
        return view('frontend.product', compact('product', 'bc', 'reldate'));
    }
}
