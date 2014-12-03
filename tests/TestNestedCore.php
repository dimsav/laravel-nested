<?php

use Dimsav\Nested\Test\Model\Category;

class TestNestedCore extends TestsBase {

    /**
     * @test
     */
    public function new_instances_without_parent_id_are_saved_at_end_of_tree()
    {
        $greaterRight = Category::orderBy('rght', 'DESC')->first()->rght;

        $category = new Category();
        $category->name = 'test';
        $category->save();
        $this->assertSame($greaterRight + 1, $category->lft);
        $this->assertSame($greaterRight + 2, $category->rght);
    }
    
//    /**
//     * @test
//     */
//    public function new_instances_with_parent_id_are_saved_as_last_children()
//    {
//        $parent = Category::find(1);
//        $parentRightBefore = $parent->rght;
//        $category = new Category();
//        $category->name = 'test';
//        $category->parent_id = 1;
//        $category->save();
//
//        $this->assertSame($parentRightBefore, $category->lft);
//        $this->assertSame($parentRightBefore + 1, $category->rght);
//
//        $parent = Category::find($parent->id);
//        $this->assertSame($parentRightBefore + 2, $parent->rght);
//
//    }
}