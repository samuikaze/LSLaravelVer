@extends('frontend.layouts.master')

@if(!empty($newsData->newsTitle))
    @section('title', "$newsData->newsTitle | 最新消息內容")
@else
    @section('title', "找不到該則消息 | 最新消息內容")
@endif

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10" style="float:unset; margin: 0 auto;">
                @if(empty($newsData->newsTitle))
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h3 class="panel-title">錯誤</h3>
                        </div>
                        <div class="panel-body text-center">
                            <h2 class="news-warn">找不到該則公告！<br /><br />
                                <div class="btn-group" role="group">
                                    <a href="javascript:history.back();" class="btn btn-success">返回消息列表</a>
                                </div>
                            </h2>
                        </div>
                    </div>
                @else
                    <div class="news-view">
                        <div class="news-time">{{ $newsData->postTime }}&nbsp;・&nbsp;@if ($newsData->newsType == '一般') <span class="badge badge-primary"> @else <span class="badge badge-success"> @endif {{ $newsData->newsType }}</span></div>
                        <h2 class="text-info news-title">{{$newsData->newsTitle}}
                    </div>
                    <hr />
                    <div class="news-content">{!! $newsData->newsContent !!}</div>
                    <div class="container-fluid text-center" style="margin: 3em 0 0 0;"><a href="javascript:history.back();" class="btn btn-lg btn-success">返回消息列表</a></div>
                @endif
            </div>
        </div>
    </div>
@endsection