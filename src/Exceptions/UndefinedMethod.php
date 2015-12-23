<?php

namespace Alius\Query\Exceptions;

class UndefinedMethod extends QueryException
{
    /**
     * @param string $class
     * @param string $name
     */
    public function __construct($class, $name)
    {
        parent::__construct('Call to undefined method ' . $class . '::' . $name . '()');
    }
}
