<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('author_id', false, true);
            $table->string('name')->unique();
            $table->json('data')->nullable();
            $table->integer('score')->default(0);
            $table->integer('flags')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'deleted_at',
                'created_at',
                'updated_at',
                'name',
                'flags',
                'score',
            ], 'segmetns_full_index');

            $table->foreign('author_id')->references('id')->on('users');
        });
        Schema::create('user_segment', function (Blueprint $table) {
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('segment_id', false, true);
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('segment_id')->references('id')->on('segments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_segment');
        Schema::dropIfExists('segments');
    }
}
