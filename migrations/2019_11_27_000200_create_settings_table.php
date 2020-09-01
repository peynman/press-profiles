<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('author_id', false, true);
            $table->bigInteger('user_id', false, true)->nullable();
            $table->string('type')->nullable();
            $table->string('key');
            $table->json('val')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at', 'user_id', 'type', 'key']);
            $table->unique(['deleted_at', 'type', 'key', 'user_id']);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('author_id')->references('id')->on('users');
        });

        Schema::create('setting_domain', function(Blueprint $table) {
            $table->bigInteger('setting_id', false, true);
            $table->bigInteger('domain_id', false, true);

            $table->foreign('setting_id')->references('id')->on('settings');
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
        Schema::dropIfExists('setting_domain');
        Schema::dropIfExists('settings');
    }
}
