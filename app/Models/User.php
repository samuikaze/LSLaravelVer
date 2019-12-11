<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // 變更預設的 ID 欄位名稱
    protected $primaryKey = 'uid';
    // 變更讀取的表格名稱
    protected $table = 'member';
    // 取消 create_at 和 update_at 的自動填入
    public $timestamps = false;

    use Notifiable;

    /**
     * 變更 laraval 查詢密碼的欄位名稱
     */
    public function getAuthPassword()
    {
        return $this->userPW;
    }

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'userName', 'userEmail', 'userPW', 'userNickname', 'userAvator', 'userRealName', 'userPhone', 'userAddress'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'userPW', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /* protected $casts = [
        'email_verified_at' => 'datetime',
    ]; */

    /**
     * Sessions 資料表關聯
     */
    public function sessions()
    {
        return $this->hasMany(Sessions::class, 'userName', 'userName');
    }
}
