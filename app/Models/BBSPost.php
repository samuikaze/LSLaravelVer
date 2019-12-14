<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BBSPost extends Model
{
    protected $table = 'bbspost';
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'postTitle', 'postType', 'postContent', 'postUserID', 'lastUpdateUserID', 'lastUpdateTime', 'postBoard'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'postTime', 'lastUpdateTime', 'postEdittime'
    ];
}
