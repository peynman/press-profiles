<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_domain', function (Blueprint $table) {
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('domain_id', false, true);
            $table->integer('flags', false, true)->default(0);
            $table->json('data')->nullable();

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
        Schema::dropIfExists('user_domain');
    }
}
