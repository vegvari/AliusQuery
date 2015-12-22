<?php

namespace Alius\Query\Exceptions;

class MissingArgument extends QueryException
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
        return new static('Missing argument 1 for ' . $class . '::' . $name . '()');
    }
}
