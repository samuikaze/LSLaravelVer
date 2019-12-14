@extends('frontend.layouts.master')

@section('title', "回覆文章 - " . $postinfo['title'] . " | " . $boardinfo['name'] . " | 討論區")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-push-1">
            @if(in_array($postinfo['status'], [2, 3]))
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">錯誤</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="news-warn">該文章已被鎖定，不可以新增回文！<br /><br />
                            <div class="btn-group" role="group">
                                <a class="btn btn-lg btn-info" onClick="javascript:history.back();">返回上一頁</a>
                                <?php echo (empty($_GET['refbid'])) ? "<a href=\"bbs.php?action=viewboard\" class=\"btn btn-lg btn-success\">返回討論板列表</a>" : "<a href=\"bbs.php?action=viewpostcontent&postid=" . $_GET['refbid'] . "&refbid=" . $_GET['refbid'] . "\" class=\"btn btn-lg btn-success\">返回討論文章</a>"; ?>
                            </div>
                        </h2>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('bbs.replypost', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id']]) }}">
                    @csrf
                    <div class="form-group">
                        <label for="replytitle">回文標題</label>
                        <input type="text" name="replytitle" class="form-control" id="replytitle" placeholder="請輸入回文標題，可以不填" />
                    </div>
                    <div class="form-group">
                        <label for="replycontent">回文內容</label>
                        <textarea id="editor1" name="replycontent" class="form-control noResize" rows="3" placeholder="請輸入回文內容，此為必填項"></textarea>
                        <script>CKEDITOR.replace( 'editor1' );</script>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="submit" value="送出" class="btn btn-success" />
                        <a href="{{ route('viewdiscussion', ['bid' => $boardinfo['id'], 'postid' => $postinfo['id']]) }}" class="btn btn-info">取消</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection