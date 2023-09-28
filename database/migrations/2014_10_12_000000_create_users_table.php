<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('number')->default('');
            $table->date('dob')->nullable();
            $table->enum('status', ['0', '1', '2'])->comment('pending => 0, active => 1, inactive => 2')->default('0');
            $table->enum('online_status', ['0', '1' ,'2'])->default('1')->comment('offline => 0,online => 1, busy => 2');
            $table->string('otp')->default('');
            $table->datetime('otp_time')->nullable();
            $table->enum('role', ['1', '2', '3','4'])->comment('Admin => 1, user => 2, Expert => 3 guestuser => 4');
            $table->string('number_verified')->comment('verify=>1,not verified=0')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default('');
            $table->string('profile_image')->default('');
            $table->string('token')->default('');
            $table->text('bio')->nullable();
            $table->text('fcm_token')->nullable();
            $table->string('mobile_type')->nullable();
            $table->string('device_id')->nullable();
            $table->string('last_activity')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
