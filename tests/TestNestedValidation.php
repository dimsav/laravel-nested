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

    /**
     * @expectedException Dimsav\Nested\Exceptions\InvalidDescendantsNumberException
     * @test
     */
    public function validation_detects_when_the_descendants_number_is_wrong()
    {
        // We will create two sibling nodes at the end of the tree with wrong coordinates.
        // To do that, we need the biggest right value.
        $biggestRight = Category::orderBy('rght', 'desc')->first()->rght;

        $cat1 = new Category();
        $cat1->lft = $biggestRight + 1;
        $cat1->rght = $biggestRight + 3;
        $cat1->name = 'cat_1';
        $cat1->save();

        $cat2 = new Category();
        $cat2->lft = $biggestRight + 2;
        $cat2->rght = $biggestRight + 4;
        $cat2->name = 'cat_2';
        $cat2->save();

        $cat1->validate();
    }

}