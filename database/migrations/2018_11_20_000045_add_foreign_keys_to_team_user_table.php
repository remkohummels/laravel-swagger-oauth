<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTeamUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->foreign('team_id', 'team_user_teams_fk')->references('id')->onDelete('cascade')->on('teams');
            $table->foreign('user_id', 'team_user_users_fk')->references('id')->onDelete('cascade')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropForeign('team_user_teams_fk');
            $table->dropForeign('team_user_users_fk');
        });
    }
}
