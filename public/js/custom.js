/*
 * LoadingAnimation
 * Require PreloadJS 1.0.0
**/

var manifest = new Array();
var preload;
var percent;
var filePercent;
var loadText = ["頁面載入中，F5 連打不是好文明喔！",
                "最新消息可以掌握團隊想告知的訊息！",
                "覺得滿腔熱血想分享攻略？討論區是你的好選擇！",
                "對於製作遊戲有興趣嗎？現在立刻加入我們！"];
var temp = Math.random() * loadText.length;

function loadProgress(){
    // 初始化
    //initCheck();
    $('.loadHint').html(loadText[Math.floor(temp)]);
    
    //關閉其他 preload 線程
    if (preload != null){
        preload.close();
    }
     
    //資料收集 img → script → css
    //遍歷所有 img 標籤
    $('img').each(function(){
        manifest.push($(this).attr('src'));
    });
    if ($('.banner').css('backgroundImage') != null){           // 頭部 BANNER 圖片
        var bannerBGImg = $('.banner').css('backgroundImage');
        bannerBGImg = bannerBGImg.replace('url(','').replace(')','').replace(/\"/gi, "").replace("/css", "");
        manifest.push(bannerBGImg);
    }

    //遍歷所有 css 和 icon 檔案
    $('link').each(function(){
        if ($(this).attr('rel') == 'stylesheet' || $(this).attr('rel') == 'shortcut icon' && $(this).attr('href') != null){
            manifest.push($(this).attr('href'));
        }
    });
    
    // 正式開始載入
    // LoadQueue() 中的 true 表示優先使用 XHR 方法載入
    preload = new createjs.LoadQueue(true);
    
    loadEvent();
    
    preload.on("progress", progressEventListener, this);
    preload.on("complete", completeLoadingProcess, this);
    preload.setMaxConnections(5);
}

// 載入事件
function loadEvent(){
    preload.loadManifest(manifest);
}

// 整體進度事件
function progressEventListener(event){
    // 文字淡入淡出效果
    //setTimeout($('.loadHint').fadeTo(500, 0.6).fadeTo(500, 1.0) ,500);
    // 「event.progress」值為 0 ~ 1。
    percent = event.progress * 100 + '%';
    ariaValue = event.progress * 100;
    $('div.progress-bar').attr('aria-valuenow', ariaValue);
    $('div.progress-bar').css('width', percent);
    $('span.progressPercent').html(Math.round(ariaValue, 1) + '%');
    //adjustVerticalAlignMiddle();
}

// 載入完成
function completeLoadingProcess(event){
    $('.loadscr').delay(300).fadeOut('slow');
    $('.pageWrap').delay(300).fadeIn('slow', function(){
        $("#hidden_link").fancybox().trigger('click');
    });
    //console.log("載入完成！");
}


$(document).scroll(function(){
    //取得 #home 的元素高度後減 200 像素
    var pageHeight = $('#home').height() - 100;
    if($(this).scrollTop() > pageHeight){   
        $('header').css({
           "background":"rgba(196, 134, 0, 0.75)",
           "borderBottom":"2px solid rgba(255, 255, 255, 0.75)"
        });
        $('.colorTran').css({
            "color":"white"
        });
        $('.main-w3ls-logo').find('a').css({
            "color":"white"
        });
    } else {
        $('header').css({
           "background":"rgba(0, 0, 0, 0.35)",
           "borderBottom":"transparent"
        });
        /*$('.colorTran').css({
            "color":"black"
        });
        $('.main-w3ls-logo').find('a').css({
            "color":"black"
        });*/
    }
});

//登入／註冊 DIV 氣泡
$(document).ready(function(){
    var loginActionID = 0;
    var regActionID = 0;
    $('#loginForm').on("click", function(){
        if(loginActionID == 0){
            $('div.hp_login').fadeIn(200);
            loginActionID = 1;
            return false;
        }
    });

    $('#register').on("click", function(){
        if(regActionID == 0){
            $('div.hp_register').fadeIn(200);
            regActionID = 1;
            return false;
        }
    });
    
    $('body').on("click", function(e2) {
        if ($(e2.target).parents("#login").length == 0 && e2.target.id != "login" && loginActionID != 0 || regActionID != 0) {
            if(loginActionID != 0 && regActionID == 0){
                $('.hp_login').fadeOut(200);
                loginActionID = 0   
            }else if(loginActionID != 0 && regActionID != 0 && $(e2.target).parents("#reg").length == 0 && e2.target.id != "reg"){
                $('.hp_register').fadeOut(200);
                regActionID = 0;
            }
            if(e2.target.id != "reg-submit"){
                return false;
            }
        }
    });
});



//禁止 class=active 的連結有反應
$(document).ready(function(){
    $('a.active').on("click", function(){
        return false;
    });
});

//修改高度用
/*$(document).scroll(function(){
    var homeHeight = $('#headerForCalc').outerHeight();
    var _this = $('div.top-main-banner-item').find('img').height();
    $('div.top-main-banner-wrapper').css("paddingTop", ($('div#banner').height() + 10) + homeHeight);
    $('div.banner').css("height", homeHeight + _this);
});*/

jQuery(document).ready(function ($) {
    $(".scroll").click(function (event) {
        event.preventDefault();
        $('html,body').animate({
            scrollTop: $(this.hash).offset().top
        }, 1000);
    });
});

// 圖片輪播 slick.js
/* $(document).ready(function(){
    $('.top-main-banner').slick({
        draggable: true,
        infinite: true,
        speed: 600,
        autoplaySpeed: 2000,
        autoplay: true,
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        centerMode: false,
        variableWidth: false,
        prevArrow: $(".navi-allow.prev"),
        nextArrow: $(".navi-allow.next"),
    });
});
 */
//捲到最上層
$(document).ready(function(){
    $('.toTop').on('click', function(){
        $('html,body').animate({ scrollTop: 0 }, 300);
    });
    $(window).on('scroll', function(){
        if ($(this).scrollTop() > 50){
            $('img.toTop').fadeIn(200);
        } else {
            $('img.toTop').stop().fadeOut(200);
        }
    });
});

// URL 變更
/*$(document).ready(function(){
    $('a.urlPush').on('click', function(){
        // 先判斷有沒有GET值，沒有就加問號，然後取得 URL
        // $(location).attr('search').substr(0, 1) != '?'
        var url = $(this).attr('href').replace('#', "?type=");

        // 推送 URL 至瀏覽器
        window.history.pushState(null, null, url);
    });
});*/

// 購物車 AJAX
$(document).ready(function(){
    // 加入購物車
    $('a.joinCart').on('click', function(){
        var sendgid = $(this).data("gid");
        if($(this).data("clicked") != "true"){
            $.ajax({
                url: 'ajax.php?action=joincart',
                type: "POST",
                cache: false,
                data: 'goodid=' + sendgid,
                success: function(data) {
                    // AJAX 成功
                    //console.log(data);
                    var ajaxSuccessSelector = "a#goodsjCart" + sendgid;
                    if(data == "errornogid" || data == "errorgid"){
                        $('span.simpleCart_total').html("錯誤！");
                    }else if(data == "errorincheck"){
                        $('span.simpleCart_total').html("結帳中！");
                    }else{
                        $('span.simpleCart_total').html("NT$" + data);
                    }
                    $(ajaxSuccessSelector).html("加入購物車成功！").removeClass('btn-info').addClass('btn-success').attr("disabled", "disabled").data("clicked", "true");
                },
                error: function(errData) {
                    // AJAX 失敗
                }
            });
        }
        return false;
    });

    // 清除購物車
    $('a.simpleCart_empty').on('click', function(){
        $.ajax({
            url: 'ajax.php?action=clearcart',
            type: "POST",
            cache: false,
            data: 'clearcart=true',
            success: function(data) {
                // AJAX 成功
                //console.log(data);
                $('span.simpleCart_total').html("NT$" + data);
            },
            error: function(errData) {
                // AJAX 失敗
            }
        });
        return false;
    });

    // 變更購物車商品數量
    $('input#goodsQty').change(function(){
        var gid = $(this).data('gid');
        var selector = "span#gPrice" + gid;
        $.ajax({
            url: 'ajax.php?action=changeGQty',
            type: 'POST',
            cache: false,
            data: 'qty=' + $(this).val() + '&gid=' + gid + '&gPrice=' + $(selector).html(),
            success: function(data) {
                // AJAX 成功
                /* 將 JSON 資料處理為物件
                 * 取資料方式為「processedData.<varName>」
                **/
                //console.log(data);
                //console.log($(selector).html());
                var processedData = JSON.parse(data);
                // 當商品編號為空
                if(processedData.msg == 'errornogid'){
                    if(processedData.erred == 'false'){
                        var alertContent = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"margin-top: 1em;\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><h4><strong>無法識別商品編號，請依正常程序修改訂購數量！</strong></h4></div>";
                        $('div#ajaxmsg').prepend(alertContent);
                    }
                }
                // 若商品數量為空
                if(processedData.msg == 'errorqty'){
                    var alertContent = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"margin-top: 1em;\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><h4><strong>商品數量不可為空或是負數！</strong></h4></div>";
                    $('div#ajaxmsg').html(alertContent);
                }else{
                    $('div#ajaxmsg').html("");
                }
                // 若取得的訊息是 success
                if(processedData.msg == 'success'){
                    var gid = processedData.gid;
                    var totalUpdateSelector = "span#gTot" + gid;
                    $(totalUpdateSelector).html(processedData.nTotal);
                    $('span#ajaxTotal').html(processedData.ajaxTotal);
                }

                //console.log(processedData.msg);
            },
            error: function(data) {
                // AJAX 失敗
                console.log(data);
            }
        })
    });

    // 移除購物車商品
    $('a#removeitem').on('click', function(){
        var gid = $(this).data('gid');
        $.ajax({
            url: 'ajax.php?action=removeitem',
            type: 'POST',
            cache: false,
            data: 'gid=' + gid,
            success: function(data){
                // AJAX 成功
                console.log(data);
                var processedData = JSON.parse(data);
                var gid = processedData.gid;
                var removeSelector = 'div#anCartItem' + gid;
                // 移除該項目
                $(removeSelector).remove();
                // 更新右邊總額或小計
                $('span#ajaxTotal').html(processedData.cartTotal);
                // 更新購物車商品數量
                $('span#itemqty').html(processedData.itemsqty);
                if(processedData.itemsqty == 0){
                    $('div.cart-items').html("<div class=\"panel panel-info\"><div class=\"panel-heading\"><h3 class=\"panel-title\">訊息</h3></div><div class=\"panel-body\"><h2 class=\"info-warn\">您的購物車為空。<br /><br /><a href=\"goods.php\" class=\"btn btn-lg btn-success\">立即前往選購</a></div></div>");
                    $('a#submitorder').attr("href", "goods.php").text('立即選購');
                }
            },
            error: function(data){
                // AJAX 失敗
                console.log(data);
            }
        });
    });

    // 通知已讀
    $('a.notify-unread, span.notify-unread').on('click', function(){
        var notifyid = $(this).data('notifyid');
        /* var isgoto = $(this).data("isgoto"); */
        $.ajax({
            url: 'ajax.php?action=readnotify',
            type: 'POST',
            cache: false,
            dataType: "HTML",
            data: 'notifyid=' + $(this).data('notifyid') + '&isgoto=' + $(this).data("isgoto"),
            success: function(data){
                // AJAX 成功
                var processedData = JSON.parse(data);
                if(processedData.msg == 'updatesuccess'){
                    return true;
                }else{
                    var updateHide = 'td#readOperate' + notifyid;
                    var updateTClass = 'a#nlink' + notifyid;
                    var updatecoltarget = 'td#content' + notifyid;
                    // 移除已讀的表格欄位
                    $(updateHide).fadeOut(300, function(){
                        $(updatecoltarget).attr("colspan", "2");
                    });
                    // 讓表格正常顯示
                    $(updateTClass).removeClass("notify-unread").addClass("notify-read");
                    // 更新通知數量
                    $('span#notifyFQty, span#notifyQty').html(processedData.nqty);
                    return false;
                }
            },
            error: function(jqXHR, exception){
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = '無法連線，請檢查網路是否通暢。';
                } else if (jqXHR.status == 404) {
                    msg = '找不到要求的頁面。 [404]';
                } else if (jqXHR.status == 500) {
                    msg = '內部伺服器錯誤。 [500].';
                } else if (exception === 'parsererror') {
                    msg = '要求之 JSON 傳值 失敗.';
                } else if (exception === 'timeout') {
                    msg = '連線逾時。';
                } else if (exception === 'abort') {
                    msg = 'AJAX 要求被中止。';
                } else {
                    msg = '未知的錯誤：' + jqXHR.responseText;
                }
                console.log(msg);
                return false;
            }
        });
    });

    //刪除通知
    $('td.clearnotify').on('click', function(){
        var targetid = $(this).data('notifyid');
        $.ajax({
            url: 'ajax.php?action=removenotify',
            type: 'POST',
            cache: false,
            data: 'nid=' + targetid,
            success: function(data){
                resultData = JSON.parse(data);
                if(resultData.msg == 'errornonotifyid'){
                    console.log('AJAX 失敗，無法取得通知 ID。');
                }else{
                    // 若刪完這則通知還有剩
                    if(resultData.notifyqty != 0){
                        // 移除整列已經刪除的通知
                        var removeTarget = "tr#notify" + targetid;
                        $(removeTarget).fadeOut(300);
                    // 刪完就沒通知了
                    }else{
                        // 移除整列已經刪除的通知
                        var removeTarget = "div#notification";
                        var toggleContent = "<div class=\"panel panel-info\" style=\"margin-top: 1em;\"><div class=\"panel-heading\"><h3 class=\"panel-title\">資訊</h3></div><div class=\"panel-body\"><h2 class=\"info-warn\">目前沒有通知！<br /><br /></h2></div></div>";
                        $(removeTarget).fadeOut(300, function(){
                            $('div#forMsg').html(toggleContent).fadeIn(300);
                        });
                    }
                    // 更新通知數量
                    $('span#notifyFQty, span#notifyQty').html(resultData.notifynrqty);
                }
            },
            error: function(jqXHR, exception){
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = '無法連線，請檢查網路是否通暢。';
                } else if (jqXHR.status == 404) {
                    msg = '找不到要求的頁面。 [404]';
                } else if (jqXHR.status == 500) {
                    msg = '內部伺服器錯誤。 [500].';
                } else if (exception === 'parsererror') {
                    msg = '要求之 JSON 傳值 失敗.';
                } else if (exception === 'timeout') {
                    msg = '連線逾時。';
                } else if (exception === 'abort') {
                    msg = 'AJAX 要求被中止。';
                } else {
                    msg = '未知的錯誤：' + jqXHR.responseText;
                }
                console.log(msg);
            }
        });
    });

    // 已讀所有通知
    $('a#readallnotifications').on('click', function(){
        $.ajax({
            url: 'ajax.php?action=readallnotify',
            type: 'POST',
            cache: false,
            data: 'readallnotify=true',
            success: function(data){
                var readresultData = JSON.parse(data);
                // 若回傳 forbidden
                if(readresultData.msg == 'forbidden'){
                    var alertContent = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"margin-top: 1em;\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><h4><strong>請依正常程序已讀通知！</strong></h4></div>";
                    $('div#forMsg').html(alertContent).fadeIn(300);
                }else{
                    // 清除錯誤訊息
                    $('div#forMsg').html("").fadeOut(300);
                    // 移除已讀按鈕
                    $('td.forreadall').fadeOut(300, function(){
                        $('td.forrall').attr("colspan", "2");
                    });
                    // 更新通知數量
                    $('span#notifyFQty, span#notifyQty').html(readresultData.nqty);
                    // 讓已讀所有通知按鈕不能按
                    $('a#readallnotifications').removeAttr("href").removeAttr("id").attr("title", "目前沒有未讀通知").attr("disabled", "disabled");
                }
            },
            error: function(jqXHR, exception){
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = '無法連線，請檢查網路是否通暢。';
                } else if (jqXHR.status == 404) {
                    msg = '找不到要求的頁面。 [404]';
                } else if (jqXHR.status == 500) {
                    msg = '內部伺服器錯誤。 [500].';
                } else if (exception === 'parsererror') {
                    msg = '要求之 JSON 傳值 失敗.';
                } else if (exception === 'timeout') {
                    msg = '連線逾時。';
                } else if (exception === 'abort') {
                    msg = 'AJAX 要求被中止。';
                } else {
                    msg = '未知的錯誤：' + jqXHR.responseText;
                }
                console.log(msg);
            }
        });
    });

    // 刪除所有通知
    $('a#removeallnotifications').on('click', function(){
        $.ajax({
            url: 'ajax.php?action=removeallnotify',
            type: 'POST',
            cache: false,
            data: 'removeallnotify=true',
            success: function(data){
                var removeresultData = JSON.parse(data);
                if(removeresultData.msg == 'forbidden'){
                    var alertContent = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"margin-top: 1em;\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><h4><strong>請依正常程序已讀通知！</strong></h4></div>";
                    $('div#forMsg').html(alertContent).fadeIn(300);
                }else{
                    // 更新通知數量
                    $('span#notifyFQty, span#notifyQty').html(removeresultData.nqty);
                    // 移除整列已經刪除的通知
                    var removeTarget = "div#notification";
                    var toggleContent = "<div class=\"panel panel-info\" style=\"margin-top: 1em;\"><div class=\"panel-heading\"><h3 class=\"panel-title\">資訊</h3></div><div class=\"panel-body\"><h2 class=\"info-warn\">目前沒有通知！<br /><br /></h2></div></div>";
                    $(removeTarget).fadeOut(300, function(){
                        $('div#forMsg').html(toggleContent).fadeIn(300);
                    });
                }
            },
            error: function(jqXHR, exception){
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = '無法連線，請檢查網路是否通暢。';
                } else if (jqXHR.status == 404) {
                    msg = '找不到要求的頁面。 [404]';
                } else if (jqXHR.status == 500) {
                    msg = '內部伺服器錯誤。 [500].';
                } else if (exception === 'parsererror') {
                    msg = '要求之 JSON 傳值 失敗.';
                } else if (exception === 'timeout') {
                    msg = '連線逾時。';
                } else if (exception === 'abort') {
                    msg = 'AJAX 要求被中止。';
                } else {
                    msg = '未知的錯誤：' + jqXHR.responseText;
                }
                console.log(msg);
            }
        });
    });
});

$(document).ready(function(){
    // 預覽圖
    $('#avatorimage').change(function(){
        if (this.files && this.files[0]) {
            // 宣告新物件
            var reader = new FileReader();
            // 當檔案被選擇後
            reader.onload = function (e) {
                if($("#imgPreview").length){
                    $('#imgPreview').attr('src', e.target.result);
                }else{
                    $('#nowimage').after("&nbsp;&nbsp;<span style=\"font-size: 1.5em;\"><strong><i class=\"fas fa-angle-double-right\"></i></strong></span>&nbsp;&nbsp;<img id=\"imgPreview\" src=\"" + e.target.result + "\" style=\"width: 15%;\" />");
                }
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});