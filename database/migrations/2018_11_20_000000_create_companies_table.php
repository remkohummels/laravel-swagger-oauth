<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary('id');

            $table->string('name', 128)->unique();
            $table->string('address', 256);
            $table->string('branch_location', 128)->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('domain', 64)->nullable();
            $table->boolean('is_leader')->default(false);

            $table->uuid('user_id')->index('companies_user_id');

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
        Schema::dropIfExists('companies');
    }
}
