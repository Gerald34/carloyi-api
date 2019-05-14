<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AppNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('userid', 255);
            $table->string('endpoint', 255);
            $table->string('p256dh', 255);
            $table->string('auth', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_notifications');
    }
}
