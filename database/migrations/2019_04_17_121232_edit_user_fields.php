<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 128)->nullable()->change();
            $table->string('last_name', 128)->nullable()->change();
            $table->dropUnique('users_email_unique');
            $table->bigInteger('wp_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 128)->change();
            $table->string('last_name', 128)->change();
            $table->unique('email', 'users_email_unique');
//            $table->drop('wp_user_id');
        });
    }
}
