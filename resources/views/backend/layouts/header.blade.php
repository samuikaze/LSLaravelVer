<div class="row">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="?action=index">洛嬉遊戲後台管理系統</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    @if(Route::currentRouteName() == 'admin.index') <li class="active"> @else <li>@endif<a href="{{ route('admin.index') }}">首頁</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">文章管理 <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('admin.article.carousel', ['action'=> 'list']) }}">輪播管理</a></li>
                            <li><a href="{{ route('admin.article.news', ['action'=> 'list']) }}">最新消息</a></li>
                            <li><a href="{{ route('admin.article.product', ['action'=> 'list']) }}">作品管理</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('admin.bbs.bbs', ['action' => 'list']) }}">討論板管理</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">會員管理 <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">會員權限</a></li>
                            <li><a href="#">會員管理</a></li>
                            <li><a href="#">封鎖清單</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">商品管理 <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">商品管理</a></li>
                            <li><a href="#">訂單管理</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">系統設定 <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">主要系統設定</a></li>
                            <li><a href="#">資料庫管理</a></li>
                            <li><a href="{{ route('index') }}">離開後台</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>