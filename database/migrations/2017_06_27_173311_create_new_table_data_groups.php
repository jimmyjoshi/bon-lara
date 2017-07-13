<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTableDataGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_groups', function(Blueprint $table)
        {
            $table->bigInteger('id', true);
            $table->integer('campus_id');
            $table->integer('user_id');
            $table->string('name');
            $table->text('description');
            $table->string('image')->default('default.png');
            $table->tinyInteger('is_private')->default(0);
            $table->integer('group_type')->default(0);
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
        //
    }
}
