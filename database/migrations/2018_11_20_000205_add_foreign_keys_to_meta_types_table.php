<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMetaTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meta_types', function (Blueprint $table) {
            $table->foreign('user_id', 'meta_types_users_fk')->references('id')->on('users');
            $table->foreign('client_id', 'meta_types_clients_fk')->references('id')->on('oauth_clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meta_types', function (Blueprint $table) {
            $table->dropForeign('meta_types_users_fk');
            $table->dropForeign('meta_types_clients_fk');
        });
    }
}
