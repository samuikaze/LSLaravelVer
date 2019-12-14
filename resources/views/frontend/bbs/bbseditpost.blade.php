@extends('frontend.layouts.master')

@section('title', "編輯文章 | 討論區")

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-push-1">
            <form action="{{ route('bbs.editpost', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id'], 'type'=> $postData['type'], 'targetpost'=>$postData['id']]) }}" method="POST">
                @csrf
                <div class="form-group">
                    @if($postData['type'] == 'post') <label for="posttitle">主貼文標題 @else <label for="replytitle">回文標題 @endif</label>
                    <input type="text" class="form-control" @if($postData['type'] == 'post') id="posttitle" name="posttitle" value="{{ $postData['data']->postTitle }}" placeholder="請輸入主貼文標題，此為必填項目" @else id="replytitle" name="replytitletitle" value="{{ $postData['data']->articleTitle }}" placeholder="請輸入回文標題，可不填" @endif>
                </div>
                @if($postData['type'] == 'post')
                    <div class="form-group">
                        <label for="posttype">主貼文類型</label>
                        <select class="form-control" name="posttype">
                            <option value="綜合討論" {{ ($postData['data']->postType == '綜合討論') ? " selected" : "" }}>綜合討論</option>
                            @if(Auth::user()->userPriviledge == 99)<option value="板務公告" {{ ($postData['data']->postType == '板務公告') ? " selected" : "" }}>板務公告</option>@endif
                            <option value="攻略心得" {{ ($postData['data']->postType == '攻略心得') ? " selected" : "" }}>攻略心得</option>
                            <option value="同人創作" {{ ($postData['data']->postType == '同人創作') ? " selected" : "" }}>同人創作</option>
                        </select>
                    </div>
                @endif
                <div class="form-group">
                    @if($postData['type'] == 'post') <label for="postcontent">主貼文內容 @else <label for="replycontent">回文內容 @endif</label>
                    <textarea id="editor1" class="form-control" rows="3" @if($postData['type'] == 'post') name="postcontent" placeholder="請輸入主貼文內容，此為必填項" @else name="replycontent" placeholder="請輸入回文內容，此為必填項" @endif>{!! ($postData['type'] == 'post') ? $postData['data']->postContent : $postData['data']->articleContent !!}</textarea>
                    <script>CKEDITOR.replace( 'editor1' );</script>
                </div>
                <input type="hidden" name="refer" value="{{ url()->previous() }}" />
                <div class="col-md-12 text-center">
                    <input type="submit" name="submit" class="btn btn-success" value="確認修改" />
                    <a href="{{ url()->previous() }}" class="btn btn-info">返回文章</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection