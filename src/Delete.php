<?php

namespace Alius\Query;

use Alius\Query\Traits\Where as WhereTrait;

class Delete extends Statement
{
    use WhereTrait;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = (string) $table;
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
            $this->query[] = 'DELETE FROM ' . $this->table;
        }

        $this->buildWhere();

        return implode(' ', $this->query);
    }
}
