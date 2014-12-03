<?php namespace Dimsav\Nested;

use DB;
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

    public function validate()
    {
        $this->validateDuplicateCoordinates();
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
}