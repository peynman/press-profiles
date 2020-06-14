<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('author_id', false, true)->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->integer('flags', false, true)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('author_id')->references('id')->on('users');
        });

        Schema::create('form_domain', function(Blueprint $table) {
            $table->bigInteger('form_id', false, true);
            $table->bigInteger('domain_id', false, true);

            $table->foreign('domain_id')->references('id')->on('domains');
            $table->foreign('form_id')->references('id')->on('forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_domain');
        Schema::dropIfExists('forms');
    }
}
