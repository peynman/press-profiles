<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivateCodesHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activate_codes_history', function (Blueprint $table) {
            $table->bigIncrements('id');
	        $table->bigInteger('activate_code_id', false, true);
	        $table->string('session_id');
	        $table->string('user_agent')->nullable();
	        $table->string('ip')->nullable();
	        $table->timestamps();
	        $table->softDeletes();

	        $table->foreign('activate_code_id')->references('id')->on('activate_codes');
	        $table->index(['ip', 'user_agent']);
	        $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activate_codes_history');
    }
}
