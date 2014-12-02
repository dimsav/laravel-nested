<?php namespace Dimsav\Nested\Test\Model;

use Dimsav\Nested\Nested;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Category extends Eloquent {

    use Nested;

}