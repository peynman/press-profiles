<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFormEntriesTableAddTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_entries', function (Blueprint $table) {
            // $table->string('tags')->nullable();
            // $table->index(['deleted_at', 'form_id', 'domain_id', 'user_id', 'tags'], 'form_entries_query_index_columns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_entries', function (Blueprint $table) {
//            $table->dropIndex('form_entries_query_index_columns');
  //          $table->dropColumn('tags');
        });
    }
}
