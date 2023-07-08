<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gifts', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('account_id')->nullable();
            $table->integer('item_id');
            $table->text('description');
            $table->boolean('delivred')->default(false);
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
        Schema::drop('gifts');
    }
}
