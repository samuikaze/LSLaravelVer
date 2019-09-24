<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Carousel;

class IndexController extends Controller
{
    public function index()
    {
        $news = News::skip(0)->take(3)->orderBy('newsOrder', 'DESC')->get();
        $carouselCount = Carousel::count();
        $carousel = Carousel::orderBy('imgID', 'ASC')->get();
        return view('frontend.index', compact('news', 'carousel', 'carouselCount'));
    }
}
