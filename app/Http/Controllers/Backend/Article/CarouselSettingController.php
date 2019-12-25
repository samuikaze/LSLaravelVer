<?php

namespace App\Http\Controllers\Backend\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Carousel;
use App\Models\GlobalSettings;

class CarouselSettingController extends Controller
{
    /**
     * 顯示輪播管理畫面
     * @param Request $request Request 實例
     * @param string $action 要顯示的頁面
     */
    public function carouselindex(Request $request, $action)
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
        $listnums = (int)GlobalSettings::where('settingName', 'adminListNum')->value('settingValue');
        // 先處理總頁數
        $info['nums'] = Carousel::count();
        $info['totalPage'] = ceil($info['nums'] / $listnums);
        // 從資料庫取得輪播項目
        $info['data'] = Carousel::skip(($info['thisPage'] - 1) * $listnums)->take($listnums)->orderBy('imgID', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '輪播新增與一覽'],
        ];
        return view('backend.article.carousel.carouselform', compact('bc', 'info'));
    }

    /**
     * 顯示編輯輪播表單
     * @param Request $request Request 實例
     * @param int $cid 輪播 ID
     * @return view 視圖
     */
    public function editCarousel(Request $request, $cid)
    {
        $cBuilder = Carousel::where('imgID', $cid);
        // 如果找不到
        if($cBuilder->count() == 0){
            return redirect(route('admin.article.carousel', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該輪播',
                'type'=> 'error',
            ]);
        }
        // 沒問題就開始取資料
        $cdata = $cBuilder->first();
        $bc = [
            ['url' => route('admin.article.carousel', ['action'=> 'list']), 'name' => '輪播新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['cid'=> $cid]), 'name' => '編輯輪播'],
        ];
        return view('backend.article.carousel.editform', compact('bc', 'cdata'));
    }

    /**
     * 顯示刪除確認表單
     * @param Request $request Request 實例
     * @param int $cid 輪播 ID
     * @return view 視圖
     */
    public function confirmDelCarousel(Request $request, $cid)
    {
        $cBuilder = Carousel::where('imgID', $cid);
        // 如果找不到
        if($cBuilder->count() == 0){
            return redirect(route('admin.article.carousel', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該輪播',
                'type'=> 'error',
            ]);
        }
        // 沒問題就開始取資料
        $cdata = $cBuilder->first();
        $bc = [
            ['url' => route('admin.article.carousel', ['action'=> 'list']), 'name' => '輪播新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['cid'=> $cid]), 'name' => '輪播刪除確認'],
        ];
        return view('backend.article.carousel.delconfirm', compact('bc', 'cdata'));
    }

    /**
     * 執行新增輪播
     * @param Request $request Request 實例
     * @return redirect Redirect 實例
     */
    public function addCarousel(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'carouselDescript' => ['nullable', 'string', 'max:100'],
            'carouselTarget' => ['nullable', 'url', 'max:150'],
            'carouselImg' => ['required', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒錯就決定新檔案名稱和副檔名
        $filename = 'carousel-' . hexdec(uniqid()) . '.' . $request->file('carouselImg')->extension();
        // 移動檔案
        $request->file('carouselImg')->storeAs('images/carousel/', $filename, 'htdocs');
        // 寫入資料庫
        Carousel::create([
            'imgUrl' => $filename,
            'imgDescript' => (empty($request->input('carouselDescript'))) ? null : $request->input('carouselDescript'),
            'imgReferUrl' => (empty($request->input('carouselTarget'))) ? null : $request->input('carouselTarget'),
        ]);
        return redirect(route('admin.article.carousel', ['action'=> 'list']))->withErrors([
            'msg'=> '新增輪播圖片成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行編輯輪播
     * @param Request $request Request 實例
     * @param int $cid 輪播 ID
     * @return redirect Redirect 實例
     */
    public function doEditCarousel(Request $request, $cid)
    {
        $cBuilder = Carousel::where('imgID', $cid);
        // 如果找不到
        if($cBuilder->count() == 0){
            return back()->withErrors([
                'msg'=> '找不到該輪播',
                'type'=> 'error',
            ]);
        }
        // 驗證表單
        $validator = Validator::make($request->all(), [
            'carouselDescript' => ['nullable', 'string', 'max:100'],
            'carouselTarget' => ['nullable', 'url', 'max:150'],
            'carouselImg' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 如果有上傳新檔案
        if($request->hasFile('carouselImg')){
            // 決定新檔名
            $filename = 'carousel-' . hexdec(uniqid()) . '.' . $request->file('carouselImg')->extension();
            // 移動檔案
            $request->file('carouselImg')->storeAs('images/carousel/', $filename, 'htdocs');
        }
        // 否則檔名為空
        else{
            $filename = $cBuilder->value('imgUrl');
        }
        // 更新資料庫
        $cBuilder->update([
            'imgUrl'=> $filename,
            'imgDescript'=> (empty($request->input('carouselDescript'))) ? null : $request->input('carouselDescript'),
            'imgReferUrl'=> (empty($request->input('carouselTarget'))) ? null : $request->input('carouselTarget'),
        ]);
        return redirect(route('admin.article.carousel', ['action'=> 'list']))->withErrors([
            'msg'=> '更新輪播資料成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行刪除輪播
     * @param Request $request Request 實例
     * @param int $cid 輪播 ID
     * @return redirect 重新導向實例
     */
    public function fireDelCarousel(Request $request, $cid)
    {
        $cBuilder = Carousel::where('imgID', $cid);
        // 如果找不到
        if($cBuilder->count() == 0){
            return back()->withErrors([
                'msg'=> '找不到該輪播',
                'type'=> 'error',
            ]);
        }
        // 沒問題就先刪檔案
        $filename = 'images/carousel/' . $cBuilder->value('imgUrl');
        Storage::disk('htdocs')->delete($filename);
        // 刪除資料庫記錄
        $cBuilder->delete();
        return redirect(route('admin.article.carousel', ['action'=> 'list']))->withErrors([
            'msg'=> '刪除輪播成功！',
            'type'=> 'success',
        ]);
    }
}
