<?php

namespace Alius\Query;

use Alius\Query\Interfaces\StatementUsingWhere;

class Where
{
    /**
     * @var StatementUsingWhere
     */
    protected $statement;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param StatementUsingWhere $statement
     * @param string              $column
     */
    public function __construct(StatementUsingWhere $statement, $column)
    {
        $this->statement = $statement;
        $this->column = $column;
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
     * Build
     *
     * @return string
     */
    public function build()
    {
        $q = $this->column . ' ' . $this->operator;

        if ($this->operator === 'IN') {
            $q .= ' (' . implode(', ', array_keys($this->data)) . ')';
        } elseif ($this->operator === 'BETWEEN') {
            $q .= ' ' . implode(' AND ', array_keys($this->data));
        } elseif ($this->operator !== 'IS NULL' && $this->operator !== 'IS NOT NULL') {
            $q .= ' ' . implode('', array_keys($this->data));
        }

        return $q;
    }

    /**
     * Equal
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function eq($value)
    {
        $this->operator = '=';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Not equal
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function ne($value)
    {
        $this->operator = '<>';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Less than
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function lt($value)
    {
        $this->operator = '<';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Less than or equal
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function lte($value)
    {
        $this->operator = '<=';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Greater than
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function gt($value)
    {
        $this->operator = '>';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Greater than or equal
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function gte($value)
    {
        $this->operator = '>=';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * In
     *
     * @param mixed $value
     * @return this
     */
    public function in($value)
    {
        $this->operator = 'IN';
        $this->data = $this->statement->setData(is_array($value) ? $value : [$value]);
        return $this->statement;
    }

    /**
     * Between
     *
     * @param mixed $value1
     * @param mixed $value2
     * @return Alius\Query\StatementUsingWhere
     */
    public function between($value1, $value2)
    {
        $this->operator = 'BETWEEN';
        $this->data = $this->statement->setData([$value1, $value2]);
        return $this->statement;
    }

    /**
     * Like
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function like($value)
    {
        $this->operator = 'LIKE';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Not like
     *
     * @param mixed $value
     * @return Alius\Query\StatementUsingWhere
     */
    public function notLike($value)
    {
        $this->operator = 'NOT LIKE';
        $this->data = $this->statement->setData($value);
        return $this->statement;
    }

    /**
     * Is null
     * @return Alius\Query\StatementUsingWhere
     */
    public function isNull()
    {
        $this->operator = 'IS NULL';
        return $this->statement;
    }

    /**
     * Is not null
     * @return Alius\Query\StatementUsingWhere
     */
    public function isNotNull()
    {
        $this->operator = 'IS NOT NULL';
        return $this->statement;
    }
}
