<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetricsCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metrics_counters', function (Blueprint $table) {
            $table->bigIncrements('id');
	        $table->bigInteger('domain_id', false, true)->nullable();
	        $table->string('key');
	        $table->decimal('value', 12, 2);
	        $table->timestamp('created_at');

	        $table->index(['created_at', 'key']);
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
        Schema::dropIfExists('metrics_counters');
    }
}
