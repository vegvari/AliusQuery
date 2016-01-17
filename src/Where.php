<?php

namespace Alius\Query;

use Alius\Query\Traits\Where as WhereTrait;

class Where extends Statement
{
    use WhereTrait;

    /**
     * @param string $expr
     * @param mixed  $data
     */
    public function __construct($expr, $data = [])
    {
        $this->where($expr, $data);
    }

    /**
     * Build
     *
     * @return string
     */
    public function build()
    {
        return $this->query[] = implode(' ', $this->where);
    }
}
