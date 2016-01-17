<?php

namespace Alius\Query\Exceptions;

class InvalidParameterCount extends QueryException
{
    /**
     * More data than token
     *
     * @param string $expr
     *
     * @return this
     */
    public static function moreTokenThanData($expr)
    {
        return new static('More token than data: "' . $expr . '"');
    }

    /**
     * More token than data
     *
     * @param string $expr
     *
     * @return this
     */
    public static function moreDataThanToken($expr)
    {
        return new static('More data than token: "' . $expr . '"');
    }
}
