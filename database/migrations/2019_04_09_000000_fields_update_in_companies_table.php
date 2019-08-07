<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FieldsUpdateInCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['branch_location', 'is_leader', 'domain']);
            $table->string('zip', 8)->nullable();
            $table->string('fax', 64)->nullable();
            $table->string('website', 128)->nullable();
            $table->string('language', 64)->nullable();
            $table->string('location', 64)->nullable();
            $table->string('description', 1024)->nullable();
            $table->string('short_description', 512)->nullable();
            $table->boolean('opened_24_hours')->nullable();
            $table->string('payment_method', 128)->nullable();
            $table->string('facebook_url', 128)->nullable();
            $table->string('key_person_name', 128)->nullable();
            $table->string('key_person_title', 128)->nullable();
            $table->string('key_person_phone', 128)->nullable();
            $table->string('key_person_email', 128)->nullable();
            $table->string('eligibility_requirement', 512)->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('branch_location', 128)->nullable();
            $table->boolean('is_leader')->default(false);
            $table->string('domain', 64)->nullable();

            $table->dropColumn(['zip', 'fax', 'website', 'language', 'location', 'description', 'short_description',
                'opened_24_hours', 'payment_method', 'facebook_url', 'key_person_name', 'key_person_title', 'key_person_phone',
                'key_person_email', 'eligibility_requirement', 'deleted_at']);
        });
    }
}
