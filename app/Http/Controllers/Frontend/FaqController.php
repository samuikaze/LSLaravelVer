<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        $bc = [
            ['url' => route('faq'), 'name' => '常見問題']
        ];
        return view('frontend.faq', compact('bc'));
    }
}
