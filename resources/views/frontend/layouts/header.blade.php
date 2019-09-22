<div id="home" class="banner banner-load inner-banner">
    <header style="padding: 15px;">
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
                        <li><a class="colorTran" href="news.php">最新消息</a></li>
                        <li><a @if(Route::getCurrentRoute() == 'product') class="active" @else class="colorTran" @endif href="{{ route('product') }}">作品一覽</a></li>
                        <li><a @if(Route::getCurrentRoute() == 'goods') class="active" @else class="colorTran" @endif href="{{ route('goods')}}">周邊產品</a></li>
                        <li><a class="colorTran" href="bbs.php">討論專區</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle colorTran" data-toggle="dropdown">其他連結<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('about') }}">關於團隊</a></li>
                                <li><a href="recruit.php">招募新血</a></li>
                                <li><a href="{{ route('faq') }}">常見問題</a></li>
                                <li><a href="{{ route('contact') }}">連絡我們</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="clearfix"></div>
    </header>
</div>