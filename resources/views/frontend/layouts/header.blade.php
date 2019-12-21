<div id="home" @if (Route::currentRouteName() == 'index') class="banner" style="background: black;" @else class="banner banner-load inner-banner" @endif>
    <header @if (Route::currentRouteName() == 'index') id="headerForCalc" @else style="padding: 15px;" @endif>
        @if (Route::currentRouteName() == 'index') <div class="header-wrap"> @endif
        <div class="header-bottom-w3layouts">
            <div class="main-w3ls-logo">
                <a href="{{ route('index') }}">
                    <h1><img src="{{ asset('images/logo.png') }}">洛嬉遊戲</h1>
                </a>
            </div>
            <nav class="navbar navbar-default">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle colorTran" data-toggle="dropdown">關於網站<b
                                    class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a data-fancybox href="https://www.youtube.com/watch?v=bhFvI6VAZTs">網站發表影片</a></li>
                                <li><a href="ebook/mobile/index.html">作品集</a></li>
                            </ul>
                        </li>
                        <li><a @if(in_array(Route::currentRouteName(), ['news', 'news.detail'])) class="active" @else class="colorTran" @endif href="{{ route('news') }}">最新消息</a></li>
                        <li><a @if(Route::currentRouteName() == 'product') class="active" @else class="colorTran" @endif href="{{ route('product') }}">作品一覽</a></li>
                        <li><a @if(in_array(Route::currentRouteName(), ['goods', 'gooddetail', 'goods.viewcart'])) class="active" @else class="colorTran" @endif href="{{ route('goods')}}">周邊產品</a></li>
                        <li><a @if(in_array(Route::currentRouteName(), ['boardselect', 'showboard', 'viewdiscussion', 'bbs.showcreatepostform', 'bbs.showreplypostform', 'bbs.showeditpostform', 'bbs.showdelconfirm'])) class="active" @else class="colorTran" @endif href="{{ route('boardselect') }}">討論專區</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle colorTran" data-toggle="dropdown">其他連結<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('about') }}">關於團隊</a></li>
                                <li><a href="{{ route('recruit') }}">招募新血</a></li>
                                <li><a href="{{ route('faq') }}">常見問題</a></li>
                                <li><a href="{{ route('contact') }}">連絡我們</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="clearfix"></div>
        @if (Route::currentRouteName() == 'index') </div> @endif
    </header>
    @if (Route::currentRouteName() == 'index')
        <!-- 圖片輪播 v3 -->
        @if ($carouselCount == 0)
            <div id="lsgames-index" class="carousel slide" data-ride="carousel">
                <!-- 底部指示器（小圓點） -->
                <ol class="carousel-indicators">
                    <li data-target="#lsgames-index" data-slide-to="0" class="active"></li>
                </ol>
                <!-- 輪播項目 -->
                <div class="carousel-inner" role="listbox">
                    <!-- 一個輪播項目 -->
                    <div class="item active">
                        <img src="{{ asset('images/carousel/default.jpg') }}" class="carousel-img">
                        <div class="carousel-caption carousel-text">
                            目前網站無輪播圖可顯示
                        </div>
                    </div>
                    <!-- /一個輪播項目 -->
                </div>
                <!-- /輪播項目 -->
            </div>
        @else
            <div id="lsgames-index" class="carousel slide" data-ride="carousel">
                <!-- 底部指示器（小圓點） -->
                <ol class="carousel-indicators">
                    @for ($i = 0; $i < $carouselCount; $i++)
                        <li data-target="#lsgames-index" data-slide-to="{{ $i }}" @if ($i == 0) class="active" @endif></li>
                    @endfor
                </ol>

                <!-- 輪播項目 -->
                <div class="carousel-inner" role="listbox">
                    @foreach($carousel as $j => $ca)
                        <!-- 一個輪播項目 -->
                        <div @if ($j == 0) class="item active" @else class="item" @endif>
                            @if (!empty($ca->imgReferUrl))
                                <a href="{{ $ca->imgReferUrl }}">
                            @endif
                            <img src="{{ asset('images\carousel\\') . $ca->imgUrl }}" class="carousel-img">
                            <div class="carousel-caption carousel-text">
                                {{ $ca->imgDescript }}
                            </div>
                            @if (!empty($ca->imgReferUrl))
                                </a>
                            @endif
                        </div>
                        <!-- /一個輪播項目 -->
                    @endforeach
                </div>
                <!-- 左右控制項 -->
                <a class="left carousel-control" href="#lsgames-index" role="button" data-slide="prev">
                    <div class="carousel-control-arrow"><i class="fas fa-chevron-left"></i></div>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#lsgames-index" role="button" data-slide="next">
                    <div class="carousel-control-arrow"><i class="fas fa-chevron-right"></i></div>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        @endif
    @endif
</div>