<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BBSBoard extends Model
{
    protected $primaryKey = 'boardID';
    protected $table = 'bbsboard';
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'boardName', 'boardImage', 'boardDescript', 'boardCreator', 'boardHide'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'boardCTime'
    ];

    /**
     * 主貼文資料表關聯
     */
    public function posts()
    {
        return $this->hasMany(BBSPost::class, 'postBoard', 'boardID');
    }
}
