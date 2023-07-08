<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailModificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_modification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('token_old')->nullable();
            $table->text('token_new')->nullable();
            $table->text('email_old');
            $table->text('email_new');
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
        Schema::drop('email_modification');
    }
}
