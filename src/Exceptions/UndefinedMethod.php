<?php

namespace Alius\Query\Exceptions;

class UndefinedMethod extends QueryException
{
    /**
     * Throw exception
     *
     * @param string $class
     * @param string $name
     * @return this
     */
    public static function create($class, $name)
    {
        return new static('Call to undefined method ' . $class . '::' . $name . '()');
    }
}
