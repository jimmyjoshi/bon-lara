<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTableFeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_feeds', function(Blueprint $table)
        {
            $table->bigInteger('id', true);
            $table->integer('user_id');
            $table->integer('campus_id');
            $table->integer('channel_id');
            $table->integer('group_id');
            $table->text('description');
            $table->string('attachment', 255)->nullable();
            $table->tinyInteger('is_attachment')->default(0);
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
