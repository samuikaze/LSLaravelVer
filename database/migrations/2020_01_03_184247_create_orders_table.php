<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('orderID')->unique()->comment('訂單系統編號');
            $table->string('orderSerial', 30)->comment('訂單序號');
            $table->string('orderMember', 50)->comment('訂購會員');
            $table->string('orderRealName', 50)->comment('訂購者姓名');
            $table->string('orderPhone', 20)->comment('訂購者電話');
            $table->string('orderAddress', 100)->comment('訂單送貨地址');
            $table->integer('orderPrice')->comment('應付金額');
            $table->timestamp('orderDate')->useCurrent()->comment('下訂日期');
            $table->string('orderCasher', 50)->nullable()->comment('付款方式');
            $table->string('orderPattern', 50)->comment('取貨方式');
            $table->integer('orderFreight')->comment('運費');
            $table->string('orderStauts', 50)->comment('訂單狀態');
            $table->integer('removeApplied')->default(0)->comment('是否提出取消申請');
            $table->string('orderApplyStatus', 50)->nullable()->comment('訂單移除前狀態');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
