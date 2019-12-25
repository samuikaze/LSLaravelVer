<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\GlobalSettings;

class CheckPriviledge
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
        // 如果不是 AJAX
        if (! $request->expectsJson()) {
            // 先取得資料庫中可存取後台頁面權限的人有誰
            $priv = GlobalSettings::where('settingName', 'backendPriv')->value('settingValue');
            // 如果權限不足就踢走
            if(Auth::user()->userPriviledge < $priv){
                return redirect(route('index'))->withErrors([
                    'msg'=> '您無權進行此操作！',
                    'type'=> 'error',
                ]);
            }
            // 否則就給進
            return $next($request);
        // 如果是 AJAX
        }else{
            // 先取得資料庫中可存取後台頁面權限的人有誰
            $priv = GlobalSettings::where('settingName', 'backendPriv')->value('settingValue');
            // 如果權限不足就踢走
            if(Auth::user()->userPriviledge < $priv){
                return response()->json(['error'=> '存取被拒！'], 403);
            }
            // 否則就給進
            return $next($request);
        }
    }
}
