<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $table = 'sessions';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'userName', 'userName');
    }

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'userName', 'sessionID', 'useBrowser', 'ipRmtAddr'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'loginTime'
    ];
}
