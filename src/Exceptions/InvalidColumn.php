<?php

namespace Alius\Query\Exceptions;

class InvalidColumn extends QueryException
{
    public function __construct($columns)
    {
        parent::__construct('This array contains different columns than the other arrays: "' . $columns  . '"');
    }
}
