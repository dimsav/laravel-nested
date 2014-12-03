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

    /**
     * @expectedException Dimsav\Nested\Exceptions\CoordinateOrderException
     * @test
     */
    public function validation_detects_when_left_is_greater_equal_than_right()
    {
        // We don't want to take a record with "border" coordinates to bypass
        // the other validation errors
        $category = Category::where('lft', '>', 1)->orderBy('lft', 'asc')->first();

        $left = $category->lft;
        $category->lft = $category->rght;
        $category->rght = $left;
        $category->save();

        $category->validate();
    }

}