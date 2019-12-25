<?php

namespace App\Http\Controllers\Backend\BBS;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\BBSBoard;
use App\Models\BBSPost;
use App\Models\GlobalSettings;
use App\Models\User;

class BBSController extends Controller
{
    /**
     * 討論板管理
     * @param Request $request Request 實例
     * @param string $action 目前顯示頁面
     * @return view 視圖
     */
    public function bbsindex(Request $request, $action)
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
        $info['nums'] = BBSBoard::count();
        $info['totalPage'] = ceil($info['nums'] / $info['listnums']);
        // 從資料庫取得輪播項目
        $info['data'] = BBSBoard::skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('boardID', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '討論板新增與一覽'],
        ];
        return view('backend.article.bbs.bbsform', compact('bc', 'info'));
    }

    /**
     * 編輯討論板表單
     * @param Request $request Request 實例
     * @param string $bid 討論板 ID
     * @return view 視圖
     */
    public function editBoard(Request $request, $bid)
    {
        $bBuilder = BBSBoard::where('boardID', $bid);
        // 如果找不到該作品
        if($bBuilder->count() == 0){
            return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該討論板！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $bdata = $bBuilder->first();
        $bc = [
            ['url' => route('admin.bbs.bbs', ['action'=> 'list']), 'name' => '討論板新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['bid'=> $bid]), 'name' => '編輯「' . $bdata->boardName . '」'],
        ];
        return view('backend.article.bbs.editboard', compact('bc', 'bdata'));
    }

    /**
     * 刪除確認表單
     * @param Request $request Request 實例
     * @param string $bid 討論板 ID
     * @return view 視圖
     */
    public function delBoardConfirm(Request $request, $bid)
    {
        $bBuilder = BBSBoard::where('boardID', $bid);
        // 如果找不到該作品
        if($bBuilder->count() == 0){
            return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該討論板！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $bdata = $bBuilder->first();
        $cuser = User::where('uid', $bdata->boardCreator)->value('userNickname');
        $bc = [
            ['url' => route('admin.bbs.bbs', ['action'=> 'list']), 'name' => '討論板新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['bid'=> $bid]), 'name' => '確認刪除「' . $bdata->boardName . '」'],
        ];
        return view('backend.article.bbs.delboardconfirm', compact('bc', 'bdata', 'cuser'));
    }

    /**
     * 執行建立討論板
     * @param Request $request Request 實例
     * @return redirect 重新導向實例
     */
    public function createBoard(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'boardname' => ['required', 'string', 'max:50'],
            'boarddescript' => ['required', 'string', 'max:150'],
            'hideboard' => ['nullable', 'string', Rule::in(['true'])],
            'boardimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 如果有上傳檔案
        if($request->hasFile('boardimage')){
            $filename = 'board-' . hexdec(uniqid()) . '.' . $request->file('boardimage')->extension();
            $request->file('boardimage')->storeAs('images/bbs/board/', $filename, 'htdocs');
        }
        // 沒有上傳檔案就設定為預設圖片名稱
        else{
            $filename = 'default.jpg';
        }
        // 寫入資料庫
        BBSBoard::create([
            'boardName'=> $request->input('boardname'),
            'boardImage'=> $filename,
            'boardDescript'=> $request->input('boarddescript'),
            'boardCreator'=> Auth::user()->uid,
            'boardHide'=> empty($request->input('hideboard')) ? 0 : 1,
        ]);
        return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
            'msg'=> '建立討論板成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行編輯討論板
     * @param Request $request Request 實例
     * @param int $bid 討論板 ID
     * @return redirect 重新導向實例
     */
    public function fireEditBoard(Request $request, $bid)
    {
        // 先檢查是不是有上傳檔案又把刪除檔案打勾
        if($request->hasFile('boardimage') && $request->input('delboardimage') == 'true'){
            return back()
                   ->withErrors([
                       'msg'=> '上傳與刪除作品視覺圖不能同時執行！',
                       'type'=> 'error',
                   ]);
        }
        $bBuilder = BBSBoard::where('boardID', $bid);
        // 如果找不到該作品
        if($bBuilder->count() == 0){
            return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該討論板！',
                'type'=> 'error',
            ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'boardname' => ['required', 'string', 'max:50'],
            'boarddescript' => ['required', 'string', 'max:150'],
            'hideboard' => ['nullable', 'string', Rule::in(['true'])],
            'boardimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
            'delboardimage' => ['nullable', 'string', Rule::in(['true'])],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        if($request->hasFile('boardimage')){
            // 如果本來就不是預設的討論板圖則需要先把檔案刪除
            if($bBuilder->value('boardImage') != 'default.jpg'){
                $oldFilename = 'images/bbs/board/'. $bBuilder->value('boardImage');
                Storage::disk('htdocs')->delete($oldFilename);
            }
            // 決定新檔案名稱和副檔名
            $filename = 'baord-' . hexdec(uniqid()) . '.' . $request->file('boardimage')->extension();
            // 移動檔案
            $request->file('boardimage')->storeAs('images/bbs/board/', $filename, 'htdocs');
        }
        // 如果沒傳檔案但是要刪除檔案
        elseif($request->has('delboardimage') && $request->input('delboardimage') == 'true'){
            $oldFilename = 'images/bbs/board/'. $bBuilder->value('boardImage');
            Storage::disk('htdocs')->delete($oldFilename);
            $filename = 'default.jpg';
        }
        // 不做任何動作
        else{
            // 沒有傳檔案還是要從資料庫裡取出檔案名稱，方便資料庫更新
            $filename = $bBuilder->value('boardImage');
        }
        // 更新資料庫
        $bBuilder->update([
            'boardName'=> $request->input('boardname'),
            'boardImage'=> $filename,
            'boardDescript'=> $request->input('boarddescript'),
            'boardHide'=> empty($request->input('hideboard')) ? 0 : 1,
        ]);
        return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
            'msg'=> '更新討論板成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行刪除討論板
     * @param Request $request Request 實例
     * @param int $bid 討論板 ID
     * @return redirect 重新導向實例
     */
    public function fireDelBoard(Request $request, $bid)
    {
        $bBuilder = BBSBoard::where('boardID', $bid);
        // 如果找不到該作品
        if($bBuilder->count() == 0){
            return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該討論板！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就先刪檔案
        if($bBuilder->value('boardImage') != 'default.jpg'){
            $filename = 'images/bbs/board/' . $bBuilder->value('boardImage');
            Storage::disk('htdocs')->delete($filename);
        }
        // 刪除資料庫記錄，先從貼文和回文開始刪
        $posts = BBSBoard::find($bid)->posts()->where('postBoard', $bid)->get();
        // 先刪回文
        foreach($posts as $post){
            BBSPost::find($post->postID)->replies()->where('ArticlePost', $post->postID)->delete();
        }
        // 然後刪貼文
        $posts = BBSBoard::find($bid)->posts()->where('postBoard', $bid)->delete();
        // 最後刪討論板
        $bBuilder->delete();
        return redirect(route('admin.bbs.bbs', ['action'=> 'list']))->withErrors([
            'msg'=> '刪除討論板及其下所有貼文成功！',
            'type'=> 'success',
        ]);
    }
}
