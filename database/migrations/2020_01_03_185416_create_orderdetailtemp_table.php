<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderdetailtempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderdetailtemp', function (Blueprint $table) {
            $table->string('orderSerial', 30)->comment('訂單序號');
            $table->integer('goodID')->comment('商品編號');
            $table->integer('goodQty')->comment('訂購數量');
            $table->integer('goodPrice')->comment('下訂時的商品價格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderdetailtemp');
    }
}
