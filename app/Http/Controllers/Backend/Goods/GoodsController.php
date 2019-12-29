<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings;
use App\Models\Goods;
use App\Models\User;

class GoodsController extends Controller
{
    /**
     * 商品一覽與新增商品
     * @param Request $request Request 實例
     * @param string $action 目前顯示頁面
     * @return view 視圖
     */
    public function goodindex(Request $request, $action)
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
        $info['nums'] = Goods::count();
        $info['totalPage'] = ceil($info['nums'] / $info['listnums']);
        // 從資料庫取得輪播項目
        $info['data'] = Goods::skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('goodsOrder', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '商品新增與一覽'],
        ];
        return view('backend.goods.good.goodform', compact('bc', 'info'));
    }

    /**
     * 編輯商品表單
     * @param Request $request Request 實例
     * @param int $gid 商品編號
     * @return view 視圖
     */
    public function editGood(Request $request, $gid)
    {
        $gBuilder = Goods::where('goodsOrder', $gid);
        // 如果找不到該會員權限
        if($gBuilder->count() == 0){
            return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該商品！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $gooddata = $gBuilder->first();
        $uptime = date('Y-m-d', strtotime($gooddata->goodsPostDate));
        $upuser = User::where('userName', $gooddata->goodsUp)->value('userNickname');
        $bc = [
            ['url' => route('admin.goods.good', ['action'=> 'list']), 'name' => '商品新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['gid'=> $gid]), 'name' => '編輯' . $gooddata->goodsName],
        ];
        return view('backend.goods.good.editgood', compact('bc', 'gooddata', 'uptime', 'upuser'));
    }

    /**
     * 確認移除商品表單
     * @param Request $request Request 實例
     * @param int $gid 商品編號
     * @return view 視圖
     */
    public function delGoodConfirm(Request $request, $gid)
    {
        $gBuilder = Goods::where('goodsOrder', $gid);
        // 如果找不到該會員權限
        if($gBuilder->count() == 0){
            return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該商品！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $gooddata = $gBuilder->first();
        $uptime = date('Y-m-d', strtotime($gooddata->goodsPostDate));
        $upuser = User::where('userName', $gooddata->goodsUp)->value('userNickname');
        $bc = [
            ['url' => route('admin.goods.good', ['action'=> 'list']), 'name' => '商品新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['gid'=> $gid]), 'name' => '確認移除' . $gooddata->goodsName],
        ];
        return view('backend.goods.good.delgoodconfirm', compact('bc', 'gooddata', 'uptime', 'upuser'));
    }

    /**
     * 執行上架商品
     * @param Request $request Request 實例
     * @return Redirect 重新導向實例
     */
    public function addGood(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'goodname' => ['required', 'string', 'max:50'],
            'goodprice' => ['required', 'numeric'],
            'goodquantity' => ['required', 'numeric'],
            'gooddescript' => ['required', 'string', 'max:500'],
            'goodstatus' => ['required', 'string', Rule::in(['up', 'down'])],
            'goodimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
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
        if($request->hasFile('goodimage')){
            $filename = 'goods-' . hexdec(uniqid()) . '.' . $request->file('goodimage')->extension();
            $request->file('goodimage')->storeAs('images/goods/', $filename, 'htdocs');
        }
        // 沒有上傳檔案就設定為預設圖片名稱
        else{
            $filename = 'default.jpg';
        }
        // 寫入資料庫
        Goods::create([
            'goodsName'=> $request->input('goodname'),
            'goodsImgUrl'=> $filename,
            'goodsDescript'=> $request->input('gooddescript'),
            'goodsPrice'=> $request->input('goodprice'),
            'goodsQty'=> $request->input('goodquantity'),
            'goodsStatus'=> $request->input('goodstatus'),
            'goodsUp'=> Auth::user()->userName,
        ]);
        return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
            'msg'=> '上架新商品成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行編輯商品
     * @param Request $request Request 實例
     * @param int $gid 商品編號
     * @return Redirect 重新導向實例
     */
    public function fireEditGood(Request $request, $gid)
    {
        // 先檢查是不是有上傳檔案又把刪除檔案打勾
        if($request->hasFile('goodimage') && $request->input('delgoodimage') == 'true'){
            return back()
                   ->withErrors([
                       'msg'=> '上傳與刪除商品圖片不能同時執行！',
                       'type'=> 'error',
                   ]);
        }
        $gBuilder = Goods::where('goodsOrder', $gid);
        // 如果找不到該商品
        if($gBuilder->count() == 0){
            return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該商品！',
                'type'=> 'error',
            ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'goodname' => ['required', 'string', 'max:50'],
            'goodstatus' => ['required', 'string', Rule::in(['up', 'down'])],
            'goodprice' => ['required', 'numeric'],
            'goodquantity' => ['required', 'numeric'],
            'gooddescript' => ['required', 'string', 'max:500'],
            'goodimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
            'delgoodimage' => ['nullable', 'string', Rule::in(['true'])],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        if($request->hasFile('goodimage')){
            // 如果本來就不是預設的討論板圖則需要先把檔案刪除
            if($gBuilder->value('goodsImgUrl') != 'default.jpg'){
                $oldFilename = 'images/goods/'. $gBuilder->value('goodsImgUrl');
                Storage::disk('htdocs')->delete($oldFilename);
            }
            // 決定新檔案名稱和副檔名
            $filename = 'good-' . hexdec(uniqid()) . '.' . $request->file('goodimage')->extension();
            // 移動檔案
            $request->file('goodimage')->storeAs('images/goods/', $filename, 'htdocs');
        }
        // 如果沒傳檔案但是要刪除檔案
        elseif($request->has('delgoodimage') && $request->input('delgoodimage') == 'true'){
            $oldFilename = 'images/goods/'. $gBuilder->value('goodsImgUrl');
            Storage::disk('htdocs')->delete($oldFilename);
            $filename = 'default.jpg';
        }
        // 不做任何動作
        else{
            // 沒有傳檔案還是要從資料庫裡取出檔案名稱，方便資料庫更新
            $filename = $gBuilder->value('goodsImgUrl');
        }
        // 更新資料庫
        $gBuilder->update([
            'goodsName'=> $request->input('goodname'),
            'goodsImgUrl'=> $filename,
            'goodsDescript'=> $request->input('gooddescript'),
            'goodsPrice'=> $request->input('goodprice'),
            'goodsQty'=> $request->input('goodquantity'),
            'goodsStatus'=> $request->input('goodstatus'),
        ]);
        return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
            'msg'=> '更新商品成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行移除商品
     * @param Request $request Request 實例
     * @param int $gid 商品編號
     * @return Redirect 重新導向實例
     */
    public function fireDelGood(Request $request, $gid)
    {
        $gBuilder = Goods::where('goodsOrder', $gid);
        // 如果找不到該商品
        if($gBuilder->count() == 0){
            return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該商品！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就先刪檔案
        if($gBuilder->value('goodsImgUrl') != 'default.jpg'){
            $filename = 'images/goods/' . $gBuilder->value('goodsImgUrl');
            Storage::disk('htdocs')->delete($filename);
        }
        // 然後刪商品資料
        $gBuilder->delete();
        return redirect(route('admin.goods.good', ['action'=> 'list']))->withErrors([
            'msg'=> '移除商品成功！',
            'type'=> 'success',
        ]);
    }
}
