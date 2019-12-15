<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table)
        {
	        $table->bigIncrements( 'id' );
	        $table->bigInteger( 'user_id', false, true )->nullable();
	        $table->bigInteger( 'domain_id', false, true )->nullable();
	        $table->string( 'number' );
	        $table->integer( 'flags', false, true )->default( 0 );
            $table->json('data')->nullable();
	        $table->timestamps();
	        $table->softDeletes();

	        $table->unique(['deleted_at', 'domain_id', 'number']);

	        $table->foreign( 'user_id' )->references( 'id' )->on( 'users' );
	        $table->foreign( 'domain_id' )->references( 'id' )->on( 'domains' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_numbers');
    }
}
