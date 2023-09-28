<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateNotificationSend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_send', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('send_id')->comment('send user')->nullable();
            $table->string('message');
            $table->string('status')->comment('unseen => 0, seen => 1');
            $table->string('send_notification')->comment('poke notificaion send not send =>1, send =>2')->nullable();
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
        Schema::dropIfExists('notification_send');
    }
}
