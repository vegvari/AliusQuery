<?php

namespace Alius\Query;

use Alius\Query\Traits\Where as WhereTrait;
use Alius\Query\Interfaces\StatementUsingWhere;

use Alius\Query\Exceptions\MissingArgument;
use Alius\Query\Exceptions\UndefinedMethod;

class Select implements StatementUsingWhere
{
    use WhereTrait;

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $join = [];

    /**
     * @var array
     */
    protected $where = [];

    /**
     * @var array
     */
    protected $group_by = [];

    /**
     * @var null|Having
     */
    protected $having;

    /**
     * @var array
     */
    protected $order_by = [];

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @param mixed $expressions
     */
    public function __construct($expressions = null)
    {
        $this->select($expressions);
    }

    /**
     * Keywords as methods
     *
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public function __call($name, array $args = [])
    {
        if (($name === 'and' || $name === 'or') && ! array_key_exists(0, $args)) {
            throw MissingArgument::create(get_class($this), $name);
        }

        if ($name === 'and') {
            return $this->addWhere($args[0], 'AND');
        }

        if ($name === 'or') {
            return $this->addWhere($args[0], 'OR');
        }

        throw UndefinedMethod::create(get_class($this), $name);
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * Add select
     *
     * @param mixed $expressions
     * @return this
     */
    public function select($expressions)
    {
        foreach ($this->processVar($expressions) as $expression) {
            $this->select[] = $expression;
        }

        if (empty($this->select) || array_search('*', $this->select) !== false) {
            $this->select = ['*'];
        } else {
            $this->select = array_unique($this->select);
        }

        return $this;
    }

    /**
     * Replace select
     *
     * @param mixed $expressions
     * @return this
     */
    public function replaceSelect($expressions)
    {
        $this->select = [];
        return $this->select($expressions);
    }

    /**
     * Build select
     *
     * @return string
     */
    public function buildSelect()
    {
        return $this->query[] = 'SELECT ' . implode(', ', $this->select);
    }

    /**
     * From table
     *
     * @param string $table
     * @return this
     */
    public function from($table)
    {
        if ($table !== null && $table !== '') {
            $this->from = (string) $table;
        }
        return $this;
    }

    /**
     * Build from
     *
     * @return string
     */
    public function buildFrom()
    {
        if ($this->from !== null) {
            return $this->query[] = 'FROM ' . $this->from;
        }
    }

    /**
     * Add group by
     *
     * @param string $columns
     * @return this
     */
    public function groupBy($columns)
    {
        foreach ($this->processVar($columns) as $column) {
            $this->group_by[] = $column;
        }

        $this->group_by = array_unique($this->group_by);
        return $this;
    }

    /**
     * Replace group by
     *
     * @param string $expression
     * @return this
     */
    public function replaceGroupBy($columns)
    {
        $this->group_by = [];
        return $this->groupBy($columns);
    }

    /**
     * Build group by
     */
    public function buildGroupBy()
    {
        if (! empty($this->group_by)) {
            return $this->query[] = 'GROUP BY ' . implode(', ', $this->group_by);
        }
    }

    /**
     * Add order by
     *
     * @param string $columns
     * @return this
     */
    public function orderBy($columns)
    {
        foreach ($this->processVar($columns) as $column) {
            $this->order_by[] = $column;
        }

        $this->order_by = array_unique($this->order_by);
        return $this;
    }

    /**
     * Replace order by
     *
     * @param string $columns
     * @return this
     */
    public function replaceOrderBy($columns)
    {
        $this->order_by = [];
        return $this->orderBy($columns);
    }

    /**
     * Build order by
     *
     * @return string
     */
    public function buildOrderBy()
    {
        if (! empty($this->order_by)) {
            return $this->query[] = 'ORDER BY ' . implode(', ', $this->order_by);
        }
    }

    /**
     * Set the offset
     *
     * @param int  $offset
     * @return this
     */
    public function offset($offset)
    {
        if ($offset >= 0) {
            $this->offset = (int) $offset;
        }

        return $this;
    }

    /**
     * Set the limit
     *
     * @param int $limit
     * @return this
     */
    public function limit($limit)
    {
        if ($limit === null) {
            $this->limit = null;
        } elseif ($limit >= 0) {
            $this->limit = (int) $limit;
        }

        return $this;
    }

    /**
     * Set the offset to page * limit
     *
     * @param int  $page
     * @return this
     */
    public function page($page)
    {
        if ($page >= 0) {
            $this->offset = (int) $page * $this->limit;
        }

        return $this;
    }

    /**
     * Build limit
     *
     * @return string
     */
    public function buildLimit()
    {
        if ($this->limit !== null) {
            if (! isset($this->offset) || $this->offset === 0) {
                return $this->query[] = 'LIMIT ' . $this->limit;
            } else {
                return $this->query[] = 'LIMIT ' . $this->offset . ',' . $this->limit;
            }
        }
    }

    /**
     * Where
     *
     * @param string $expression
     * @return Where
     */
    public function where($expression)
    {
        $this->last_join = null;
        return $this->addWhere($expression);
    }

    /**
     * Build
     *
     * @return string
     */
    public function build()
    {
        $this->query = [];

        $this->buildSelect();
        $this->buildFrom();
        $this->buildWhere();
        $this->buildGroupBy();
        $this->buildOrderBy();
        $this->buildLimit();

        return implode(' ', $this->query);
    }

    /**
     * Create an array with trimmed values
     *
     * @param mixed $values
     * @return array
     */
    protected function processVar($values)
    {
        $result = [];

        $values = is_array($values) ? $values : [$values];
        foreach ($values as $key => $value) {
            $value = preg_replace('/\s+/', ' ', ltrim(rtrim($value, ' '), ' '));
            if ($value !== null && $value !== '') {
                $result[] = (string) $value;
            }
        }

        return $result;
    }
}
