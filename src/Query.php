<?php

namespace Alius\Query;

abstract class Query
{
    /**
     * Create a delete statement
     *
     * @param string $table
     *
     * @return Alius\Query\Delete
     */
    public static function delete($table)
    {
        return new Delete($table);
    }

    /**
     * Create an insert statement
     *
     * @param string $table
     * @param array  $values
     *
     * @return Alius\Query\Insert
     */
    public static function insert($table, array $values = [])
    {
        return new Insert($table, $values);
    }

    /**
     * Create a select statement
     *
     * @param mixed $expr
     * @param mixed $data
     *
     * @return Alius\Query\Select
     */
    public static function select($expr, $data = [])
    {
        return new Select($expr, $data);
    }

    /**
     * Create an update statement
     *
     * @param string $table
     * @param array  $values
     *
     * @return Alius\Query\Update
     */
    public static function update($table, array $values = [])
    {
        return new Update($table, $values);
    }

    /**
     * Create a where statement
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return Alius\Query\Where
     */
    public static function where($expr, $data = [])
    {
        return new Where($expr, $data);
    }
}
