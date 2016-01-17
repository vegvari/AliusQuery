<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;

class DeleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $name
     */
    public function testConstructor($query, $name)
    {
        $instance = new Delete($name);
        $this->assertSame($query, $instance->build());
        $this->assertSame([], $instance->data());
    }

    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $name
     */
    public function testFactory($query, $name)
    {
        $instance = Query::delete($name);
        $this->assertSame($query, $instance->build());
        $this->assertSame([], $instance->data());
    }

    public function constructorDataProvider()
    {
        return [
            ['', null],
            ['', ''],
            ['DELETE FROM foo', 'foo'],
        ];
    }
}
