<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public function index()
    {
        $bc = [
            ['url' => route('about'), 'name' => '關於團隊']
        ];
        return view('frontend.about', compact('bc'));
    }
}
