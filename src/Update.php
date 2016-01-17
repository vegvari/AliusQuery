<?php

namespace Alius\Query;

use Alius\Query\Traits\Where as WhereTrait;

class Update extends Statement
{
    use WhereTrait;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var int|null
     */
    protected $limit;

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
        if (! empty($values)) {
            $columns = [];
            $data = [];

            foreach ($values as $key => $value) {
                if ($key !== '') {
                    $columns[] = $key . ' = ?';
                    $data[] = $value;
                }
            }

            $this->addExpression('values', implode(', ', $columns), $data);
        }

        return $this;
    }

    /**
     * Helper to add one value
     *
     * @param mixed $column
     * @param mixed $data
     *
     * @return this
     */
    public function set($column, $data)
    {
        return $this->values([$column => $data]);
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
            $this->query[] = 'UPDATE ' . $this->table;
        }

        if (! empty($this->values)) {
            $this->query[] = 'SET ' . implode(', ', $this->values);
        }

        $this->buildWhere();

        return implode(' ', $this->query);
    }
}
