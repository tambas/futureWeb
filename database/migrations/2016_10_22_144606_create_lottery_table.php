<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->unsigned();
            $table->integer('percentage')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->unsigned();
            $table->integer('user_id');
            $table->text('description');
            $table->boolean('used')->default(false);
            $table->integer('item_id')->nullable();
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
        Schema::drop('lottery_items');
        Schema::drop('lottery_tickets');
    }
}
