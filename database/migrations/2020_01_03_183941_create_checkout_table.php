<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout', function (Blueprint $table) {
            $table->increments('itemID')->unique()->comment('項目編號');
            $table->string('pattern', 50)->comment('運送方式');
            $table->integer('fee')->comment('運費');
            $table->string('type', 10)->default('freight')->comment('結帳／運送方式');
            $table->string('cashType', 10)->nullable()->comment('付款方式');
            $table->string('isRAddr', 10)->nullable()->comment('送貨地址是否為住址');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkout');
    }
}
