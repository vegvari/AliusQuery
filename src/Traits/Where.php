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
     *
     * @return array
     */
    public function setData($values)
    {
        $values = is_array($values) ? $values : [$values];

        $data = [];

        $data_id = count($this->data);
        foreach ($values as $value) {
            $data[':data' . $data_id] = $value;
            $data_id++;
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
     *
     * @return Alius\Query\Where
     */
    public function addWhere($column, $operator = null)
    {
        if ($operator !== null) {
            $this->where[] = $operator;
        }

        if ($column instanceof Closure) {
            $this->where[] = '(';
            $return        = $column($this);
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
        $query = [];
        foreach ($this->where as $where) {
            $query[] = (string) $where;
        }

        if (! empty($query)) {
            $query = implode(' ', $query);
            $query = str_replace('( ', '(', $query);
            $query = str_replace(' )', ')', $query);
            return $this->query[] = 'WHERE ' . $query;
        }
    }
}
