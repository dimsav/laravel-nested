<?php

use Dimsav\Nested\Test\Model\Category;

class TestNestedValidation extends TestsBase {

    /**
     * @test
     */
    public function seeded_records_are_valid()
    {
        (new Category())->validate();
    }

    /**
     * @expectedException Dimsav\Nested\Exceptions\DuplicateCoordinateException
     * @test
     */
    public function validation_caches_duplicated_left_right_values()
    {
        $category = Category::find(1);
        $category->rght = $category->rght - 1;
        $category->save();

        $category->validate();
    }

}