<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function(Blueprint $table)
        {
			$table->increments('id');
			$table->string('name');
			$table->integer('parent_id')->unsigned()->nullable();
			$table->integer('lft')->unsigned();
			$table->integer('rght')->unsigned();
            $table->timestamps();
            $table->softDeletes();
		});

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

        Schema::dropIfExists('categories');
	}

}
