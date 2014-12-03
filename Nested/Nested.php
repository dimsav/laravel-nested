<?php namespace Dimsav\Nested;

use DB;
use Dimsav\Nested\Exceptions\CoordinateOrderException;
use Dimsav\Nested\Exceptions\InvalidDescendantsNumberException;
use Dimsav\Nested\Exceptions\UpperBorderException;
use Dimsav\Nested\Exceptions\LowerBorderException;
use Dimsav\Nested\Exceptions\DuplicateCoordinateException;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait Nested {

    // Todo: test with empty db.
    public function validate()
    {
        $this->validateDuplicateCoordinates();
        $this->validateWrongBorder();
        $this->validate_coordinate_order();
        $this->validate_number_of_descendants();
    }

    /**
     * Throws exception if the same left/right coordinate is found
     * in more than one table record.
     *
     * @throws DuplicateCoordinateException
     */
    private function validateDuplicateCoordinates()
    {
        $table  = $this->getTable();
        $result = DB::select(
            DB::raw("SELECT  count(*) as count
                FROM $table AS C
                JOIN $table AS D
                WHERE C.id != D.id
                AND (
                  C.lft = D.lft
                  OR C.lft = D.rght
                  OR C.rght = D.rght
                )"
            )
        );

        if ($result[0]->count > 0)
        {
            throw new DuplicateCoordinateException;
        }
    }

    private function validateWrongBorder()
    {
        $result = $this->select(DB::raw('MAX(rght) as max, MIN(lft) as min, Count(*) as count'))->first();

        if (($result->count * 2) != $result->max)
        {
            throw new UpperBorderException;
        }
        if ($result->min < 1)
        {
            throw new LowerBorderException;
        }
    }

    private function validate_coordinate_order()
    {
        $badRecord = $this->where('lft', '>=', DB::raw('rght'))->first();
        if ($badRecord)
        {
            throw new CoordinateOrderException;
        }
    }

    private function validate_number_of_descendants()
    {
        $table  = $this->getTable();
        $result = DB::select(
            DB::raw("SELECT (C.rght-C.lft-1)/2 AS descendants_count_1, COUNT(D.id) AS descendants_count_2
                FROM $table AS C
                LEFT JOIN $table AS D
                ON D.lft > C.lft AND D.rght < C.rght
                GROUP BY C.id
                HAVING descendants_count_1 != descendants_count_2"
            )
        );
        if (count($result))
        {
            throw new InvalidDescendantsNumberException;
        }
    }
}