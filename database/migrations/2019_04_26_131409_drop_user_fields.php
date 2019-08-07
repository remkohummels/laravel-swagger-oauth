<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('state');
            $table->dropColumn('age');
            $table->dropColumn('phone');

            $table->dropColumn('midwife');
            $table->dropColumn('doctor_adult');
            $table->dropColumn('doctor_child');
            $table->dropColumn('pregnacy_weeks');
            $table->dropColumn('wic_clinic_name');

            $table->dropColumn('champ_role');
            $table->dropColumn('champ_first_name');
            $table->dropColumn('champ_last_name');
            $table->dropColumn('champ_phone');
            $table->dropColumn('champ_email');
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
        });
    }
}
