<?php namespace Dimsav\Nested;

use DB;
use Dimsav\Nested\Exceptions\CoordinateOrderException;
use Dimsav\Nested\Exceptions\UpperBorderException;
use Dimsav\Nested\Exceptions\LowerBorderException;
use Dimsav\Nested\Exceptions\DuplicateCoordinateException;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait Nested {

    protected $positions = ['after', 'before', 'inside'];

    public function save(array $options = [])
    {
        if ( ! $this->exists)
        {
            if ($this->parent_id === null)
            {
                if ($last = $this->orderBy('rght', 'DESC')->first())
                {
                    $this->lft = $last->rght + 1;
                    $this->rght = $last->rght + 2;
                }
                else
                {
                    // todo
                }
            }
            else
            {
//                $parent = $this->find($this->parent_id);
//
//                if ($last = $this->orderBy('rght', 'DESC')->first())
//                {
//                    $this->lft = $last->rght + 1;
//                    $this->rght = $last->rght + 2;
//                }
            }
        }

        return parent::save($options);
    }

    // Todo: test with empty db.
    public function validate()
    {
        $this->validateDuplicateCoordinates();
        $this->validateWrongBorder();
        $this->validate_coordinate_order();
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
}