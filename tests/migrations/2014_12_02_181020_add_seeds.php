<?php

use Illuminate\Database\Migrations\Migration;
use Dimsav\Nested\Test\Model\Category;

class AddSeeds extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $categories = [
            ['id' => 1, 'name' => 'Sciences'],
        ];

        $this->createCategories($categories);

	}

    private function createCategories($categories)
    {
        foreach ($categories as $data) {
            $country = new Category;
            $country->id = $data['id'];
            $country->name = $data['name'];
            $country->save();
        }
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {}

}
