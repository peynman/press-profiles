<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id', false, true)->nullable();
            $table->bigInteger('domain_id', false, true)->nullable();
            $table->integer('type', false, true);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(
                [
                    'created_at',
                    'updated_at',
                    'domain_id',
                    'type',
                    'user_id',
                    'subject'
                ],
                'activity_logs_full_index'
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
        Schema::dropIfExists('activity_logs');
    }
}
