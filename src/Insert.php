<?php

namespace Alius\Query;

use Alius\Query\Exceptions\InvalidColumn;

class Insert extends Statement
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $tokens;

    /**
     * @param string $table
     * @param array  $values
     */
    public function __construct($table, array $values = [])
    {
        $this->table = (string) $table;
        $this->values($values);
    }

    /**
     * Add values
     *
     * @param array $values
     *
     * @return this
     */
    public function values(array $values)
    {
        if (! is_array(reset($values))) {
            $values = [$values];
        }

        $expr = [];
        $data = [];

        foreach ($values as $value) {
            if (empty($this->columns)) {
                $columns = array_keys($value);
                if (! empty($columns) && array_search(null, $columns) === false) {
                    $this->columns = $columns;
                }
            } elseif ($this->columns !== array_keys($value)) {
                throw new InvalidColumn(implode(', ', array_keys($value)));
            }

            if (! empty($this->columns)) {
                $tokens = str_repeat('?, ', count($value));
                $expr[] = '(' . substr($tokens, 0, strlen($tokens) - 2) . ')';
                $data = array_merge($data, array_values($value));
            }
        }

        if (! empty($this->columns)) {
            if (($expr = $this->processExpression(implode(', ', $expr), $data)) !== '()') {
                $this->tokens[] = $expr;
            }
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

        if ($this->table !== '') {
            $this->query[] = 'INSERT INTO ' . $this->table;
        }

        if (! empty($this->columns)) {
            $this->query[] = '(' . implode(', ', $this->columns) . ') VALUES ' . implode(', ', $this->tokens);
        }

        return implode(' ', $this->query);
    }
}
