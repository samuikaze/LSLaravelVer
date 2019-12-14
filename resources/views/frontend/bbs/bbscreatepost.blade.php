@extends('frontend.layouts.master')

@section('title', "建立貼文 - " . $boardinfo['name'] . " | 討論區")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-push-1">
            <form method="POST" action="{{ route('bbs.createpost', ['bid' => $boardinfo['id']]) }}">
                @csrf
                <div class="form-group">
                    <label for="posttitle">文章標題</label>
                    <input type="text" name="posttitle" class="form-control" id="posttitle" placeholder="請輸入文章標題，此為必填項" />
                </div>
                <div class="form-group">
                        <label for="posttype">文章分類</label>
                        <select name="posttype" class="form-control" id="posttype">
                            <option value="" selected>請選擇分類</option>
                            <option value="綜合討論">綜合討論</option>
                            @if(Auth::user()->userPriviledge  == 99)<option value="板務公告">板務公告</option>@endif
                            <option value="攻略心得">攻略心得</option>
                            <option value="同人創作">同人創作</option>
                        </select>
                    </div>
                <div class="form-group">
                    <label for="postcontent">文章內容</label>
                    <textarea id="editor1" name="postcontent" class="form-control noResize" rows="3" placeholder="請輸入文章內容，此為必填項"></textarea>
                    <script>CKEDITOR.replace( 'editor1' );</script>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                    <a href="{{ route('showboard', ['bid' => $boardinfo['id']])}}" class="btn btn-info">取消</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection