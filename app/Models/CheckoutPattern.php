<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutPattern extends Model
{
    // 變更讀取的表格名稱
    protected $table = 'checkout';
    // 變更預設的 ID 欄位名稱
    protected $primaryKey = 'itemID';
    // 取消 create_at 和 update_at 的自動填入
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'pattern', 'fee', 'type', 'cashType', 'isRAddr'
    ];
}
