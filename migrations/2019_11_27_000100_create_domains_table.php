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
            $table->string('domain');
            $table->string('ips')->nullable();
            $table->string('nameservers')->nullable();
            $table->integer('flags')->default(0);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['deleted_at', 'domain']);
            $table->index(
                [
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'domain',
                    'flags'
                ],
                'domains_full_index'
            );

            $table->foreign('author_id')->references('id')->on('users');
        });
        Schema::create('domains_subs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('domain_id', false, true);
            $table->string('sub_domain');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('deleted_at', 'sub_domain');

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
        Schema::dropIfExists('domains_subs');
        Schema::dropIfExists('domains');
    }
}
