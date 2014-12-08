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
        $left = $this->getLeftName();
        $right = $this->getRightName();
        $table = $this->getTable();

        $result = DB::select(
            DB::raw("SELECT  count(*) as count
                FROM $table AS C
                JOIN $table AS D
                WHERE C.id != D.id
                AND (
                  C.$left = D.$left
                  OR C.$left = D.$right
                  OR C.$right = D.$right
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
        $left = $this->getLeftName();
        $right = $this->getRightName();

        $result = $this->select(DB::raw("MAX($right) as max, MIN($left) as min, Count(*) as count"))->first();

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
        $left = $this->getLeftName();
        $right = $this->getRightName();

        $badRecord = $this->where($left, '>=', DB::raw($right))->first();
        if ($badRecord)
        {
            throw new CoordinateOrderException;
        }
    }

    private function validate_number_of_descendants()
    {
        $left = $this->getLeftName();
        $right = $this->getRightName();
        $table = $this->getTable();

        $result = DB::select(
            DB::raw("SELECT (C.$right-C.$left-1)/2 AS descendants_count_1, COUNT(D.id) AS descendants_count_2
                FROM $table AS C
                LEFT JOIN $table AS D
                ON D.$left > C.$left AND D.$right < C.$right
                GROUP BY C.id
                HAVING descendants_count_1 != descendants_count_2"
            )
        );

        if (count($result))
        {
            throw new InvalidDescendantsNumberException;
        }
    }

    protected function getLeftName()
    {
        return 'lft';
    }

    protected function getRightName()
    {
        return 'rght';
    }
}