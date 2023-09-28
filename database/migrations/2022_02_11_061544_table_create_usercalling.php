<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCreateUsercalling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usercalling', function (Blueprint $table) {
            $table->id();
            $table->string('send_id')->nullable();
            $table->string('recive_id')->nullable();
            $table->string('status')->comment('call accpet=>1, call cancel=>2,pokecall=> 3');
            $table->string('start_date')->nullable()->comment('calling date');
            $table->string('start_time')->nullable()->comment('Calling starting time');
            $table->string('end_time')->nullable()->comment('calling end time');
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
        Schema::dropIfExists('usercalling');
    }
}
