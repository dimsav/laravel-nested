<?php namespace Dimsav\Nested;

trait Nested {

    protected $positions = ['after', 'before', 'inside'];

    public function save(array $options = array())
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

}