<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferts', function ($table) {
            $table->increments('id');
            $table->string('server');
            $table->integer('account_id')->unsigned();
            $table->integer('state')->unsigned(); // IN_PROGRESS 0, OK_API 1, OK_SQL 2, FAIL 3
            $table->integer('amount')->unsigned();
            $table->string('type');
            $table->timestamps();
        });
    }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
    public function down()
    {
        Schema::drop('transferts');
    }
}
