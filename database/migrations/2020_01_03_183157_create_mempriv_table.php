<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemprivTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mempriv', function (Blueprint $table) {
            $table->integer('privNum')->unique()->comment('權限編號');
            $table->string('privName', 10)->comment('權限名稱');
            $table->tinyInteger('privPreset')->default(0)->comment('是否為內建權限');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mempriv');
    }
}
