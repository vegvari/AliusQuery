<?php

namespace Alius\Query;

use Alius\Query\Exceptions\InvalidParameterCount;

abstract class Statement
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    protected $query = [];

    /**
     * Build
     *
     * @return string
     */
    abstract public function build();

    /**
     * Cast to string
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->build();
    }

    /**
     * Get the data array
     *
     * @return array
     */
    final public function data()
    {
        return $this->data;
    }

    /**
     * Get the query array
     *
     * @return array
     */
    final public function query()
    {
        return $this->query;
    }

    /**
     * Set data
     *
     * @param string $expr
     * @param array  $data
     *
     * @return string
     */
    final protected function processExpression($expr, array $data)
    {
        if ($expr instanceof self) {
            $this->data = array_merge($this->data, $expr->data());
            return '(' . $expr . ')';
        }

        $expr = (string) $expr;

        $token_count = preg_match_all('/\?/', $expr);
        $data_count = count($data);

        if ($data_count === 0 && $token_count === 0) {
            return $expr;
        }

        if ($data_count > $token_count) {
            throw InvalidParameterCount::moreDataThanToken($expr);
        }

        if ($data_count < $token_count) {
            throw InvalidParameterCount::moreTokenThanData($expr);
        }

        $this->data = array_merge($this->data, $data);
        return $expr;
    }

    /**
     * Helper method to add expression
     *
     * @param string $target
     * @param mixed  $expr
     * @param mixed  $data
     *
     * @return this
     */
    final public function addExpression($target, $expr, $data = [])
    {
        $data = is_array($data) ? $data : [$data];
        if (($expr = $this->processExpression($expr, $data)) !== '') {
            array_push($this->$target, $expr);
        }

        return $this;
    }
}
