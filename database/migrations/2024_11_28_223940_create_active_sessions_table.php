<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('session_id')->unique();
            $table->ipAddress('ip_address');
            $table->string('user_agent');
            $table->timestamp('last_activity');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('active_sessions');
    }
}
