<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * 顯示首頁
     * @return view 視圖
     */
    public function home()
    {
        $bc = [
            ['url' => route('boardselect'), 'name' => '選擇討論板'],
        ];
        return view('backend.home', compact('bc'));
    }
}
