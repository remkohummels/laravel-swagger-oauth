<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_types', function (Blueprint $table) {
            $table->uuid('id')->primary('id');

            $table->string('name', 128);
            $table->unsignedTinyInteger('type')->nullable();
            $table->boolean('is_custom')->nullable();
            $table->boolean('is_required')->nullable();
            $table->string('validation')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('weight')->default(100);

            $table->uuid('user_id')->index('meta_types_user_id');
            $table->unsignedInteger('client_id')->nullable()->index('meta_types_client_id');

            $table->softDeletes();
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
        Schema::dropIfExists('meta_types');
    }
}
