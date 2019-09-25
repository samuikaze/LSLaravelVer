<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecruitController extends Controller
{
    public function index()
    {
        $bc = [
            ['url' => route('recruit'), 'name' => '招募新血']
        ];
        return view('frontend.recruit', compact('bc'));
    }
}
