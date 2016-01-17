<?php

namespace Alius\Query;

use Alius\Query\Traits\Where as WhereTrait;

class Select extends Statement
{
    use WhereTrait;

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $from = [];

    /**
     * @var array
     */
    protected $join = [];

    /**
     * @var array
     */
    protected $group_by = [];

    /**
     * @var array
     */
    protected $having = [];

    /**
     * @var array
     */
    protected $order_by = [];

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var int|null
     */
    protected $page;

    /**
     * @param mixed $expr
     * @param mixed $data
     */
    public function __construct($expr, $data = [])
    {
        $this->select($expr, $data);
    }

    /**
     * Add select
     *
     * @param mixed $expr
     * @param mixed $data
     *
     * @return this
     */
    public function select($expr, $data = [])
    {
        return $this->addExpression('select', $expr, $data);
    }

    /**
     * Add from
     *
     * @param mixed $expr
     * @param mixed $data
     *
     * @return this
     */
    public function from($expr, $data = [])
    {
        return $this->addExpression('from', $expr, $data);
    }

    /**
     * Add join
     *
     * @param string $type
     * @param string $expr
     * @param array  $data
     *
     * @return this
     */
    protected function addJoin($type, $expr, $data = [])
    {
        if (($expr = $this->processExpression($expr, $data)) !== '') {
            $this->join[] = $type . ' ' . $expr;
        }

        return $this;
    }

    /**
     * Add join
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function join($expr, $data = [])
    {
        return $this->addJoin('JOIN', $expr, $data);
    }

    /**
     * Add left join
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function leftJoin($expr, $data = [])
    {
        return $this->addJoin('LEFT JOIN', $expr, $data);
    }

    /**
     * Add right join
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function rightJoin($expr, $data = [])
    {
        return $this->addJoin('RIGHT JOIN', $expr, $data);
    }

    /**
     * Add cross join
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function crossJoin($expr, $data = [])
    {
        return $this->addJoin('CROSS JOIN', $expr, $data);
    }

    /**
     * Add group by
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function groupBy($expr, $data = [])
    {
        return $this->addExpression('group_by', $expr, $data);
    }

    /**
     * Add having
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function having($expr, $data = [])
    {
        return $this->addExpression('having', $expr, $data);
    }

    /**
     * Add order by
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function orderBy($expr, $data = [])
    {
        return $this->addExpression('order_by', $expr, $data);
    }

    /**
     * Set the limit
     *
     * @param int $limit
     *
     * @return this
     */
    public function limit($limit)
    {
        $this->limit = $limit < 1 ? null : (int) $limit;

        if ($this->page > 0) {
            $this->offset = $this->page * $this->limit;
        }

        return $this;
    }

    /**
     * Set the offset
     *
     * @param int $offset
     *
     * @return this
     */
    public function offset($offset)
    {
        $this->offset = $offset < 1 ? null : (int) $offset;
        $this->page = null;
        return $this;
    }

    /**
     * Helper method to set the offset to page * limit
     *
     * @param int $page
     *
     * @return this
     */
    public function page($page)
    {
        $this->page = $page < 1 ? null : (int) $page;

        if ($this->page > 0) {
            $this->offset = $this->page * $this->limit;
        }

        return $this;
    }

    /**
     * Build
     *
     * @return string
     */
    public function build()
    {
        $this->query = [];

        if (! empty($this->select)) {
            $this->query[] = 'SELECT ' . implode(', ', $this->select);
        }

        if (! empty($this->from)) {
            $this->query[] = 'FROM ' . implode(', ', $this->from);
        }

        if (! empty($this->join)) {
            $this->query[] = implode(' ', $this->join);
        }

        $this->buildWhere();

        if (! empty($this->group_by)) {
            $this->query[] = 'GROUP BY ' . implode(', ', $this->group_by);
        }

        if (! empty($this->having)) {
            $this->query[] = 'HAVING ' . implode(' ', $this->having);
        }

        if (! empty($this->order_by)) {
            $this->query[] = 'ORDER BY ' . implode(', ', $this->order_by);
        }

        if ($this->limit !== null) {
            $this->query[] = 'LIMIT ' . $this->limit;
        }

        if ($this->limit !== null && $this->offset !== null) {
            $this->query[] = 'OFFSET ' . $this->offset;
        }

        return implode(' ', $this->query);
    }
}
