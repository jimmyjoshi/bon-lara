<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTableAllCampus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_campus', function(Blueprint $table)
        {
            $table->bigInteger('id', true);
            $table->string('name');
            $table->string('campus_code');
            $table->string('valid_domain');
            $table->string('contact_person_name');
            $table->string('contact_number');
            $table->string('email_id');
            $table->boolean('status')->default(1);
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
        Schema::drop('all_campus');
    }
}
