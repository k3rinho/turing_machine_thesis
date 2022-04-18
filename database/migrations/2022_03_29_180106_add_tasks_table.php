<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id');
            $table->string('name');
            $table->string('description');
            $table->json('IO')->nullable();
            $table->timestamps();
        });

        // machines table, add task id
        Schema::table('machines', function (Blueprint $table) {
            $table->bigInteger('task_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
        // drop task_id
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('task_id');
        });
    }
}
