<?php

namespace App\Http\Controllers\Backend\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings;
use App\Models\News;
use App\Models\User;

class NewsController extends Controller
{
    /**
     * 顯示消息一覽與新增消息
     * @param Request $request Request 實例
     * @param string $action 目前顯示項目
     * @return view 視圖
     */
    public function newsindex(Request $request, $action)
    {
        // 把 action 放進 info 陣列內
        $info['action'] = $action;
        // 拿目前頁數
        if(!empty($request->query('p'))){
            $info['thisPage'] = $request->query('p');
        }else{
            $info['thisPage'] = 1;
        }
        // 從資料庫取得一頁要顯示的數量
        $info['listnums'] = (int)GlobalSettings::where('settingName', 'adminListNum')->value('settingValue');
        // 先處理總頁數
        $info['nums'] = News::count();
        $info['totalPage'] = ceil($info['nums'] / $info['listnums']);
        // 從資料庫取得輪播項目
        $info['data'] = News::skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('newsOrder', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '消息新增與一覽'],
        ];
        return view('backend.article.news.newsform', compact('bc', 'info'));
    }

    /**
     * 顯示編輯消息表單
     * @param Request $request Request 實例
     * @param int $newsid 消息 ID
     * @return view 視圖
     */
    public function editNews(Request $request, $newsid)
    {
        $nBuilder = News::where('newsOrder', $newsid);
        // 如果找不到該則消息
        if($nBuilder->count() == 0){
            return redirect(route('admin.article.news', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該則消息！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $ndata = $nBuilder->first();
        $bc = [
            ['url' => route('admin.article.news', ['action'=> 'list']), 'name' => '消息新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['newsid'=> $newsid]), 'name' => '編輯消息「' . $ndata->newsTitle . '」'],
        ];
        return view('backend.article.news.editnews', compact('bc', 'ndata'));
    }

    /**
     * 顯示消息刪除確認表單
     * @param Request $request Request 實例
     * @param int $newsid 消息 ID
     * @return view 視圖
     */
    public function delNewsConfirm(Request $request, $newsid)
    {
        $nBuilder = News::where('newsOrder', $newsid);
        // 如果找不到該則消息
        if($nBuilder->count() == 0){
            return redirect(route('admin.article.news', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該則消息！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $ndata = $nBuilder->first();
        $poster = User::where('uid', $ndata->postUser)->value('userName');
        $bc = [
            ['url' => route('admin.article.news', ['action'=> 'list']), 'name' => '消息新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['newsid'=> $newsid]), 'name' => '刪除消息確認'],
        ];
        return view('backend.article.news.delnewsconfirm', compact('bc', 'ndata', 'poster'));
    }

    /**
     * 執行編輯消息
     * @param Request $request Request 實例
     * @param int $newsid 消息 ID
     * @return redirect 重新導向實例
     */
    public function fireEditNews(Request $request, $newsid)
    {
        $nBuilder = News::where('newsOrder', $newsid);
        // 如果找不到該則消息
        if($nBuilder->count() == 0){
            return back()->withErrors([
                'msg'=> '找不到該則消息！',
                'type'=> 'error',
            ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'newsType' => ['required', 'string', Rule::in(['normal', 'info'])],
            'newsTitle' => ['required', 'string', 'max:50'],
            'newsContent' => ['required', 'string', 'max:300'],
        ]);
        // 若驗證失敗
        if ($validator->fails()){
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就開始更新資料庫
        switch($request->input('newsType')){
            case 'normal':
                $type = '一般';
                break;
            case 'info':
                $type = '資訊';
                break;
            default:
                $type = '一般';
        }
        $nBuilder->update([
            'newsType'=> $type,
            'newsTitle'=> $request->input('newsTitle'),
            'newsContent'=> $request->input('newsContent'),
        ]);
        return redirect(route('admin.article.news', ['action'=> 'list']))->withErrors([
            'msg'=> '編輯消息成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行張貼新消息
     * @param Request $request Request 實例
     * @return redirect 重新導向實例
     */
    public function addNews(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'newsType' => ['required', 'string', Rule::in(['normal', 'info'])],
            'newsTitle' => ['required', 'string', 'max:50'],
            'newsContent' => ['required', 'string', 'max:300'],
        ]);
        // 若驗證失敗
        if ($validator->fails()){
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return redirect(route('admin.article.news', ['action'=> 'add']))
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就開始更新資料庫
        switch($request->input('newsType')){
            case 'normal':
                $type = '一般';
                break;
            case 'info':
                $type = '資訊';
                break;
            default:
                $type = '一般';
        }
        News::create([
            'newsType' => $type,
            'newsTitle' => $request->input('newsTitle'),
            'newsContent' => $request->input('newsContent'),
            'postUser' => Auth::user()->uid,
        ]);
        return redirect(route('admin.article.news', ['action'=> 'list']))->withErrors([
            'msg'=> '消息新增成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行刪除消息
     * @param int $newsid 消息 ID
     * @return redirect 重新導向實例
     */
    public function fireDelNews($newsid)
    {
        $nBuilder = News::where('newsOrder', $newsid);
        // 如果找不到該則消息
        if($nBuilder->count() == 0){
            return back()->withErrors([
                'msg'=> '找不到該則消息！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就開始更新資料庫
        $nBuilder->delete();
        return redirect(route('admin.article.news', ['action'=> 'list']))->withErrors([
            'msg'=> '刪除消息成功！',
            'type'=> 'success',
        ]);
    }
}
