<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUrlsTable.
 */
class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('urls', function(Blueprint $table) {
            $table->increments('id');
            $table->string('url_category')->default('category')->nullable();
            $table->string('url_title')->nullable();
            $table->string('uri');
            $table->timestamps();

            $table->index('uri');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop('urls');
    }
}
