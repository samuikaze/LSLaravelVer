@extends('frontend.layouts.master')

@section('title', '連絡我們')

@section('content')
<h5 class="main-w3l-title">讓我們知道你所遇到的問題！</h5>
<div class="form-bg">
    <form action="#" method="post">
        <div class="col-md-6 contact-fields">
            <input type="text" name="Name" placeholder="稱呼" required="">
        </div>
        <div class="col-md-6 contact-fields">
            <input type="email" name="Email" placeholder="電子郵件" required="">
        </div>
        <div class="contact-fields">
            <input type="text" name="Subject" placeholder="主題" required="">
        </div>
        <div class="contact-fields">
            <textarea name="Message" placeholder="內容" required=""></textarea>
        </div>
        <input type="submit" value="送出">
    </form>
</div>
<div class="contact-maps">
    <h5 class="main-w3l-title">直接臨櫃詢問</h5>
    <div class="col-md-5 add-left">
        <p class="paragraph-agileinfo"><span>地址 : </span>臺南市官田區官田工業區工業路40號</p>
        <p class="paragraph-agileinfo"><span>電話 : </span>(06)698-5945~50</p>
        <p class="paragraph-agileinfo"><span>Email : </span><a href="mailto:example@gmail.com">example@gmail.com</a></p>
    </div>
    <div class="col-md-7 add-right">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3666.795133746354!2d120.3202832507034!3d23.21413551489199!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x346e87f572beb163%3A0x191f52e398135526!2z5Yq05YuV6YOo5Yq05YuV5Yqb55m65bGV572y6Zuy5ZiJ5Y2X5YiG572y!5e0!3m2!1sja!2stw!4v1560992757455!5m2!1sja!2stw"></iframe>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
</div>
@endsection