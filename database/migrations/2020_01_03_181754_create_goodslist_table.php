<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodslistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goodslist', function (Blueprint $table) {
            $table->increments('goodsOrder')->unique()->comment('商品識別碼');
            $table->string('goodsName', 50)->comment('商品名稱');
            $table->string('goodsImgUrl', 50)->default('default.jpg')->comment('商品圖片');
            $table->string('goodsDescript', 500)->comment('商品描述');
            $table->integer('goodsPrice')->comment('商品價格');
            $table->integer('goodsQty')->comment('商品在庫量');
            $table->string('goodsStatus', 5)->default('up')->comment('商品販售狀態');
            $table->timestamp('goodsPostDate')->useCurrent()->comment('商品上架日期');
            $table->string('goodsUp', 50)->comment('商品上架者');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goodslist');
    }
}
