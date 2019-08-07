<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRpteamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropColumn('rpteam_id');
        });

        Schema::table('permission_user', function (Blueprint $table) {
            $table->dropColumn('rpteam_id');
        });

        Schema::drop('rpteams');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rpteams', function (Blueprint $table) {
            //
        });
    }
}
