@extends('frontend.layouts.master')

@section('title', '關於團隊')

@section('content')
<h5 class="main-w3l-title">關於 L.S. Games</h5>
<div class="about-top">
    <h3 class="subheading-wthree">團隊起源</h3>
    <p class="paragraph-agileinfo">我們是一支新創的遊戲團隊，成員都對製作遊戲富含熱情，我們的目標是製作出「有創意」、「玩自由」及「能嬉笑」的遊戲。
    </p>
</div>
<div class="about-main">
    <div class="about-w3-left">
        <div class="about-img" style="background: url({{ asset('images/about.jpg') }}) !important;">
        </div>
        <div class="about-bottom">
            <p class="paragraph-agileinfo white-clr">儘管程式碼有上萬行，只要玩家盡興就好</p>
        </div>
    </div>

    <div class="about-w3ls-right">
        <h3 class="subheading-wthree">遊戲製作宗旨</h3>
        <p class="paragraph-agileinfo">做為新創的遊戲團隊，我們為了向各位玩家呈上最有趣的內容，我們在製作上堅持：</p>
        <ul>
            <li><i class="far fa-check-square"></i> 劇情完整及可讀性</li>
            <li><i class="far fa-check-square"></i> 美術衝擊性</li>
            <li><i class="far fa-check-square"></i> 遊戲可玩性</li>
            <li><i class="far fa-check-square"></i> 執行順暢性</li>
            <li><i class="far fa-check-square"></i> 隱藏要素</li>
        </ul>
    </div>
    <div class="clearfix"> </div>
</div>
</div>
</div>
<div class="about-mid">
    <div class="mid-info">
        <h3 class="subheading-wthree white-clr">熱情四溢</h3>
        <p class="paragraph-agileinfo white-clr">我們是由一群對玩及製作遊戲很有熱情的人所組成的團隊，為的就是製作出能讓玩家驚奇且享受其中的遊戲。</p>
        <ul>
            <li><i class="fas fa-asterisk"></i> 熱情</li>
            <li><i class="fas fa-asterisk"></i> 盡興</li>
            <li><i class="fas fa-asterisk"></i> 滿足</li>
        </ul>
    </div>
    <div class="mid-info">
        <h3 class="subheading-wthree white-clr">公平遊戲</h3>
        <p class="paragraph-agileinfo white-clr">玩家在玩遊戲時最怕的就是遇到作弊玩家，所以我們要盡我們全力避免作弊的發生，並時常修補可能的漏洞。</p>
        <ul>
            <li><i class="fas fa-asterisk"></i> 檢討公平性</li>
            <li><i class="fas fa-asterisk"></i> 封鎖作弊玩家</li>
            <li><i class="fas fa-asterisk"></i> 修補作弊漏洞</li>
        </ul>
    </div>

    <div class="mid-info">
        <h3 class="subheading-wthree white-clr">競爭向上</h3>
        <p class="paragraph-agileinfo white-clr">儘管已經正在製作遊戲了，我們仍督促自己繼續努力，讓團隊成員各方面的能力都可以追上潮流。</p>
        <ul>
            <li><i class="fas fa-asterisk"></i> 程式撰寫</li>
            <li><i class="fas fa-asterisk"></i> 美術設計</li>
            <li><i class="fas fa-asterisk"></i> 劇本改寫</li>
        </ul>
    </div>
</div>
<div class="team-section" id="team">
    <div class="container">
        <h5 class="main-w3l-title">團隊成員</h5>
        <div class="col-md-6 team-left">
            <p class="paragraph-agileinfo">負責遊戲故事及整體架構的撰寫，完成一個完整的故事架構並將其寫成完整的故事呈現給玩家。<br />劇本編寫－－</p>
            <p class="paragraph-agileinfo">將遊戲的相關機制與條件編寫成一系列模組、設計攝影機路徑等，並將其套用到相對應的遊戲上。<br />－－程式設計</p>
            <p class="paragraph-agileinfo">將世界觀及角色設定轉為平面美術，提供 3D 建模相關的基礎設計。<br />平面美術設計－－</p>
            <p class="paragraph-agileinfo">為平面美術建立 3D 模型，並套用於遊戲中。<br />－－立體建模設計</p>
        </div>
        <div class="col-md-6 team-right">
            <div class="col-md-6 col-sm-6 col-xs-6 team-grid">
                <img class="team-img img-responsive" src="{{ asset('images/writer.jpg') }}" alt="">
                <h6>劇本編寫</h6>
                <div class="clearfix"> </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 team-grid t2">
                <img class="team-img img-responsive" src="{{ asset('images/programmer.jpg') }}" alt="">
                <h6>程式設計</h6>
                <div class="clearfix"> </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 team-grid">
                <img class="team-img img-responsive" src="{{ asset('images/art.jpg') }}" alt="">
                <h6>平面美術設計</h6>
                <div class="clearfix"> </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 team-grid">
                <img class="team-img img-responsive" src="{{ asset('images/modeling.jpg') }}" alt="">
                <h6>立體建模設計</h6>
                <div class="clearfix"> </div>
            </div>
        </div>
    </div>
@endsection