<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->bigIncrements('id');
	        $table->bigInteger('domain_id', false, true)->nullable();
	        $table->string('type');
	        $table->string('name');
	        $table->integer('zorder', false, true)->default(0);
	        $table->integer('flags', false, true)->default(0);
	        $table->json('data')->nullable();
	        $table->timestamps();
	        $table->softDeletes();

	        $table->unique(['deleted_at', 'domain_id', 'type', 'name']);
	        $table->index(['domain_id', 'type', 'name']);

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
        Schema::dropIfExists('filters');
    }
}
