<?php

namespace Alius\Query\Exceptions;

class MissingArgument extends QueryException
{
    /**
     * @param string $class
     * @param string $name
     */
    public function __construct($class, $name)
    {
        parent::__construct('Missing argument 1 for ' . $class . '::' . $name . '()');
    }
}
