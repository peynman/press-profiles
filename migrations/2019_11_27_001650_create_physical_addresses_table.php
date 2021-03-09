<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhysicalAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('domain_id', false, true);
            $table->string('type')->nullable();
            $table->integer('country_code')->nullable();
            $table->integer('city_code')->nullable();
            $table->integer('province_code')->nullable();
            $table->string('address');
            $table->string('postal_code')->nullable();
            $table->string('desc')->nullable();
            $table->json('data')->nullable();
            $table->integer('flags', false, true)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'user_id',
                    'domain_id',
                    'country_code',
                    'province_code',
                    'city_code'
                ],
                'physical_addresses_full_index'
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
        Schema::dropIfExists('physical_addresses');
    }
}
