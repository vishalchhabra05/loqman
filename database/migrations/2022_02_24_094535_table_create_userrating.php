<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateUserrating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userrating', function (Blueprint $table) {
            $table->id();
            $table->string('usercalling_id');
            $table->string('rating');
            $table->string('bages');
            $table->string('sender_id');
            $table->string('recive_id');
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
        Schema::dropIfExists('userrating');
    }
}
