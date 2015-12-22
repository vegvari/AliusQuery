<?php

namespace Alius\Query\Traits;

use Closure;
use Alius\Query\Where as WhereBuilder;

trait Where
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Set data
     *
     * @param mixed $values
     * @return array
     */
    public function setData($values)
    {
        $values = is_array($values) ? $values : [$values];

        $data = [];

        $id = count($this->data);
        foreach ($values as $value) {
            $data[':data' . $id] = $value;
            $id++;
        }

        $this->data = array_merge($this->data, $data);
        return $data;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Add where
     *
     * @param string      $column
     * @param string|null $operator
     * @return Alius\Query\Where
     */
    public function addWhere($column, $operator = null)
    {
        if ($operator !== null) {
            $this->where[] = $operator;
        }

        if ($column instanceof Closure) {
            $this->where[] = '(';
            $return = $column($this);
            $this->where[] = ')';

            return $return;
        }

        return $this->where[] = new WhereBuilder($this, $column);
    }

    /**
     * Build where
     *
     * @return string
     */
    public function buildWhere()
    {
        $q = [];
        foreach ($this->where as $where) {
            $q[] = (string) $where;
        }

        if (! empty($q)) {
            return $this->query[] = 'WHERE ' . str_replace('( ', '(', str_replace(' )', ')', implode(' ', $q)));
        }
    }
}
