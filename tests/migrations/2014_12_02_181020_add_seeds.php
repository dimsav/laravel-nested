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
            ['id' => 1, 'parent_id' => null, 'lft' => 1, 'rght' => 12, 'name' => 'Sciences'],
                ['id' => 2, 'parent_id' => 1, 'lft' => 2, 'rght' => 7, 'name' => 'Math'],
                    ['id' => 3, 'parent_id' => 2, 'lft' => 3, 'rght' => 4, 'name' => 'Algebra'],
                    ['id' => 4, 'parent_id' => 2, 'lft' => 5, 'rght' => 6, 'name' => 'Node theory'],
                ['id' => 5, 'parent_id' => 1, 'lft' => 8, 'rght' => 9, 'name' => 'Medicine'],
                ['id' => 6, 'parent_id' => 1, 'lft' => 10, 'rght' => 11, 'name' => 'Geology'],
        ];

        $this->createCategories($categories);

	}

    private function createCategories($categories)
    {
        foreach ($categories as $data) {
            $country = new Category;
            $country->id = $data['id'];
            $country->name = $data['name'];
            $country->parent_id = $data['parent_id'];
            $country->lft = $data['lft'];
            $country->rght = $data['rght'];
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
