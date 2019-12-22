<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    // 變更預設的 ID 欄位名稱
    protected $primaryKey = 'notifyID';
    // 變更讀取的表格名稱
    protected $table = 'notifications';
    // 取消 create_at 和 update_at 的自動填入
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'userName', 'notifyTarget');
    }

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'notifyContent', 'notifyTitle', 'notifySource', 'notifyTarget', 'notifyURL', 'notifyStatus'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'notifyTime',
    ];
}
