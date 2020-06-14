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
            $table->bigInteger('author_id', false, true);
            $table->string('type');
            $table->string('name');
            $table->integer('zorder', false, true)->default(0);
            $table->integer('flags', false, true)->default(0);
            $table->json('data')->nullable();
            $table->json('translations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['deleted_at', 'type', 'name']);
            $table->index(['type', 'name']);
            $table->foreign('author_id')->references('id')->on('users');
        });
        Schema::create('filter_domain', function(Blueprint $table) {
            $table->bigInteger('filter_id', false, true);
            $table->bigInteger('domain_id', false, true);

            $table->foreign('domain_id')->references('id')->on('domains');
            $table->foreign('filter_id')->references('id')->on('filters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filter_domain');
        Schema::dropIfExists('filters');
    }
}
