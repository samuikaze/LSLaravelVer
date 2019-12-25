@extends('backend.layouts.master')

@section('title', '編輯消息「' . $ndata->newsTitle . '」 - 最新消息設定 | 後台管理首頁')

@section('content')
<form method="POST" action="{{ route('admin.article.doeditnews', ['newsid'=> $ndata->newsOrder]) }}" style="margin-top: 1em;">
    @csrf
    <div class="form-group">
        <label for="newsType">消息類型</label>
        <select name="newsType" class="form-control" id="newsType">
            <option value="normal" @if($ndata->newsType == '一般') selected @endif>一般</option>
            <option value="info" @if($ndata->newsType == '資訊') selected @endif>資訊</option>
        </select>
    </div>
    <div class="form-group">
        <label for="newsTitle">消息標題</label>
        <input type="text" name="newsTitle" class="form-control" id="newsTitle" value="{{ $ndata->newsTitle }}" />
    </div>
    <div class="form-group">
        <label for="newsContent">消息內容</label>
        <textarea id="editor1" name="newsContent" class="form-control noResize" rows="3">{!! $ndata->newsContent !!}</textarea>
        <script>CKEDITOR.replace( 'editor1' );</script>
    </div>
    <div class="form-group text-center">
        <input type="submit" name="submit" value="送出" class="btn btn-success" />
        <a href="{{ route('admin.article.news', ['action' => 'list']) }}" class="btn btn-info">取消</a>
    </div>
</form>
@endsection