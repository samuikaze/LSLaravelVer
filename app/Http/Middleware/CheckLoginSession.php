<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Cookie;

class CheckLoginSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 驗證有沒有被登出（會員資料管理頁面可以管理登入）
        if(Auth::check())
        {
            // 取得帳號 ID
            $uid = Auth::user()->uid;
            // 取得 Session 中儲存的 session_id
            $sessId = Cookie::get('loginSession');
            $sessions = User::find($uid)->sessions();
            // 如果 Cookie 中的 SessionID 在 sessions 資料表中找不到就登出使用者
            if($sessions->where('sessionID', $sessId)->count() == 0)
            {
                // 如果 Sessions 資料表中已經沒有這個帳號的資料就把登入資料整個登出清掉
                if($sessions->count() == 0){
                    // 用此方式 remember_token 會重新產生
                    Auth::logout();
                }else{
                    // 否則就只登出這個裝置（token 不重新產生）
                    $this->guard()->logoutCurrentDevice();
                }
                $request->session()->invalidate();
                return $next($request)->withCookie(cookie()->forget('loginSession'));
            }
            // 如果有找到就更新資訊
            else
            {
                // 取得 Sessions 資料
                $sessData = $sessions->where('sessionID', $sessId)->first();
                // 取得最後登入時間
                $lastLoginTime = $sessData->loginTime;
                // 取得現在時間
                $nowTime = Carbon::now();
                // 如果找不到 session 中的 sessionStillAlive 表示 session 已過期
                if(!$request->session()->has('sessionStillAlive')){
                    $sessions->where('sessionID', $sessId)->update([
                        'ipRmtAddr' => $request->ip(),  
                        'lastipRmtAddr' => $sessData->ipRmtAddr,
                        'loginTime' => $nowTime,
                    ]);
                    $request->session()->put('sessionStillAlive', 'true');
                }
                // 如果 session 沒有過期
                else
                {
                    $sessions->where('sessionID', $sessId)->update([
                        'ipRmtAddr' => $request->ip(),
                        'loginTime' => $nowTime,
                    ]);
                }
            }
        }
        return $next($request);
    }
}
