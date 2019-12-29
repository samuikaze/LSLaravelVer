<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    // 變更預設的 ID 欄位名稱
    protected $primaryKey = 'goodsOrder';
    // 變更讀取的表格名稱
    protected $table = 'goodslist';
    // 取消 create_at 和 update_at 的自動填入
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'goodsName', 'goodsImgUrl', 'goodsDescript', 'goodsPrice', 'goodsQty', 'goodsUp'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'goodsPostDate'
    ];
}
