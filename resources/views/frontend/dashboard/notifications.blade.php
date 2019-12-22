@extends('frontend.layouts.master')

@section('title', "通知一覽")

@section('content')
<div class="row">
    <div class="col-md-10 col-md-push-1">
        {{-- 若沒有通知可顯示 --}}
        @if ($notifydata['totalNums'] == 0)
            <div class="panel panel-info" style="margin-top: 1em;">
                <div class="panel-heading">
                    <h3 class="panel-title">資訊</h3>
                </div>
                <div class="panel-body">
                    <h2 class="info-warn">目前沒有通知！<br /><br /></h2>
                </div>
            </div>
        {{-- 如果有通知 --}}
        @else
            <div id="forMsg" style="display: none;"></div>
            <div id="notification">
                <div class="pull-right" style="margin-bottom: 5px;">
                    @if($notifydata['unreadNums'] == 0)
                        <a class="btn btn-success" disabled="disabled" title="目前沒有未讀通知">已讀所有通知</a>
                    @else
                        <a id="readallnotifications" class="btn btn-success" style="cursor: pointer;" title="已讀所有未讀的通知">已讀所有通知</a>
                    @endif
                    <a id="removeallnotifications" class="btn btn-danger" style="cursor: pointer;">刪除所有通知</a>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr class="info">
                            <th colspan="3">通知一覽</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifydata['data'] as $data)
                        <!-- 一則通知 -->
                        <tr id="{{ "notify" . $data->notifyID }}">
                            <td id="{{ "content" . $data->notifyID }}" @if($data->notifyStatus == 'u') class="forrall" @else colspan="2"@endif>
                                <a id="{{ "nlink" . $data->notifyID }}" @if(!empty($data->notifyURL)) href="{{ $data->notifyURL }}" @endif data-notifyid="{{ $data->notifyID }}" data-isgoto="true" @if($data->notifyStatus == 'u') class="notify-link notify-unread" @else class="notify-link notify-read" @endif>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <span class="pull-left">{{ $data->notifySource }}・{{ $data->notifyTime }}</span>
                                                
                                                <div class="clearfix"></div>
                                                <div class="notify-content">
                                                    @if(empty($data->notifyTitle)) {{ $data->notifyTitle }} @else <h4>{{ $data->notifyTitle }}</h4>@endif
                                                    <span>{{ $data->notifyContent }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            @if($data->notifyStatus == 'u') <td id="{{ "readOperate" . $data->notifyID }}" class="forreadall" valign="middle" style="width: 8%;"><span class="pull-right btn btn-success notify-unread" style="cursor: pointer;" data-notifyid="{{ $data->notifyID }}" data-isgoto="false">標示為已讀</span></td> @endif
                            <td class="clearnotify" data-notifyid="{{ $data->notifyID }}" style="width: 3%;"><span title="刪除此通知" class="lead text-danger" style="cursor: pointer;"><i class="fas fa-trash"></i></span></td>
                        </tr>
                        <!-- /一則通知 -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection