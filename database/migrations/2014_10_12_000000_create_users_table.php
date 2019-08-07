<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->uuid('id')->primary('id');

            $table->string('name', 128);
            $table->string('first_name', 128);
            $table->string('last_name', 128);
            $table->string('email', 64)->unique();
            $table->string('avatar', 128)->nullable();
            $table->string('state', 128)->nullable();
            $table->string('age', 6)->nullable();
            $table->string('phone', 64)->nullable();

            $table->string('midwife', 128)->nullable();
            $table->string('doctor_adult', 128)->nullable();
            $table->string('doctor_child', 128)->nullable();
            $table->string('pregnacy_weeks', 128)->nullable();
            $table->string('wic_clinic_name', 128)->nullable();

            $table->string('champ_role', 128)->nullable();
            $table->string('champ_first_name', 128)->nullable();
            $table->string('champ_last_name', 128)->nullable();
            $table->string('champ_phone', 128)->nullable();
            $table->string('champ_email', 128)->nullable();

            $table->string('user_litmos_id', 128)->nullable();
            $table->string('old_user_id', 128)->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

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
