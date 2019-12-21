<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoveOrders extends Model
{
    protected $table = 'removeorder';
    // 變更預設的 ID 欄位名稱
    protected $primaryKey = 'removeID';
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'targetOrder', 'removeReason', 'removeDate', 'removeStatus'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'removeDate'
    ];
}
