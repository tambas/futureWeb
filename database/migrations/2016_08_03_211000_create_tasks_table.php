<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('task', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('priority', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->integer('status_order')->default('0');
            $table->string('color', 200)->default('#000');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tasks');
    }
}
