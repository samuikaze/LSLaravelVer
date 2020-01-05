<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductnameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productname', function (Blueprint $table) {
            $table->increments('prodOrder')->unique()->comment('作品編號');
            $table->string('prodTitle', 50)->comment('作品名稱');
            $table->string('prodImgUrl', 150)->default('nowprint.jpg')->comment('作品視覺圖');
            $table->string('prodDescript', 100)->comment('作品簡介');
            $table->string('prodPageUrl', 150)->comment('作品頁面');
            $table->string('prodType', 30)->comment('作品類型');
            $table->string('prodPlatform', 50)->comment('遊戲平台');
            $table->timestamp('prodRelDate')->nullable()->comment('作品上架日期');
            $table->timestamp('prodAddDate')->useCurrent()->comment('資料新增日期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productname');
    }
}
