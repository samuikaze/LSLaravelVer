<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFrontcarouselTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frontcarousel', function (Blueprint $table) {
            $table->increments('imgID')->unique()->comment('輪播流水號');
            $table->string('imgUrl', 100)->comment('輪播圖片');
            $table->string('imgDescript', 100)->nullable()->comment('輪播描述');
            $table->string('imgReferUrl', 150)->nullable()->comment('輪播指向位址');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('frontcarousel');
    }
}
