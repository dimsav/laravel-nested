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

    /**
     * @expectedException Dimsav\Nested\Exceptions\UpperBorderException
     * @test
     */
    public function validation_detects_false_right_border()
    {
        $category = Category::orderBy('rght', 'desc')->first();
        $category->rght++;
        $category->save();

        $category->validate();
    }


    /**
     * @expectedException Dimsav\Nested\Exceptions\LowerBorderException
     * @test
     */
    public function validation_detects_false_left_border()
    {
        $category = Category::orderBy('lft', 'asc')->first();
        $category->lft--;
        $category->save();

        $category->validate();
    }

}