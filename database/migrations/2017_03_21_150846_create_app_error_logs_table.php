<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_error_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('url');
            $table->string('method');
            $table->string('message');
            $table->string('error_code');
            $table->string('file');
            $table->string('line');
            $table->string('user_id');
            $table->text('session');
            $table->text('request');
            $table->text('trace');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_error_logs');
    }
}
