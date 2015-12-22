<?php

namespace Alius\Query\Interfaces;

interface StatementUsingWhere
{
	/**
     * Set data
     *
     * @param mixed $values
     * @return array
     */
    public function setData($values);

    /**
     * Get data
     *
     * @return array
     */
    public function data();

    /**
     * Add where
     *
     * @param string|Closure $column
     * @param string|null    $operator
     * @return Alius\Query\Where
     */
    public function addWhere($column, $operator = null);

    /**
     * Build where
     *
     * @return string
     */
    public function buildWhere();
}
