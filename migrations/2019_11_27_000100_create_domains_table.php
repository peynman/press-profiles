<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->bigIncrements('id');
	        $table->bigInteger('author_id', false, true)->nullable();
	        $table->string('name')->unique();
	        $table->string('ips')->nullable();
	        $table->string('nameservers')->nullable();
	        $table->integer('flags')->default(0);
	        $table->json('data')->nullable();
	        $table->timestamps();
	        $table->softDeletes();

	        $table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
