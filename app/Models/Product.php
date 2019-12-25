<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'productname';
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'prodTitle', 'prodImgUrl', 'prodDescript', 'prodPageUrl', 'prodType', 'prodPlatform', 'prodRelDate'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'prodRelDate', 'prodAddDate'
    ];
}
