<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PasswordResetTokenString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            DB::statement("ALTER TABLE password_resets ALTER COLUMN token TYPE varchar(64) USING rtrim(token)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            DB::statement("ALTER TABLE password_resets ALTER COLUMN token TYPE char(64)");
        });
    }
}
