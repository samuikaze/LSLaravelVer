<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function index()
    {
        $bc = [
            ['url' => route('contact'), 'name' => '連絡我們']
        ];
        return view('frontend.contact', compact('bc'));
    }
}
