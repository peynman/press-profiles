<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id', false, true)->nullable();
            $table->bigInteger('domain_id', false, true)->nullable();
            $table->bigInteger('country_code')->nullable();
            $table->bigInteger('province_code')->nullable();
            $table->bigInteger('city_code')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address');
            $table->integer('flags', false, true)->default(0);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['deleted_at', 'domain_id', 'email']);
            $table->index(
                [
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'domain_id',
                    'country_code',
                    'province_code',
                    'city_code',
                    'postal_code',
                    'flags'
                ],
                'email_addresses_full_index'
            );

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('domain_id')->references('id')->on('domains');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_addresses');
    }
}
