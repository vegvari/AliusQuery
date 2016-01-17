<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;

class UpdateTest extends PHPUnit_Framework_TestCase
{
    protected $instance;

    public function setUp()
    {
        $this->instance = new Update('foo');
    }

    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $name
     * @param string $values
     * @param string $expected_data
     */
    public function testConstructor($query, $name, array $values, array $expected_data)
    {
        $instance = new Update($name, $values);
        $this->assertSame($query, $instance->build());
        $this->assertSame($expected_data, $instance->data());
    }

    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $name
     * @param string $values
     * @param string $expected_data
     */
    public function testFactory($query, $name, array $values, array $expected_data)
    {
        $instance = Query::update($name, $values);
        $this->assertSame($query, $instance->build());
        $this->assertSame($expected_data, $instance->data());
    }

    public function constructorDataProvider()
    {
        return [
            ['',
                null,
                [],
                [],
            ],
            ['',
                '',
                [],
                [],
            ],
            ['UPDATE foo',
                'foo',
                [],
                [],
            ],
            ['UPDATE foo SET bar = ?',
                'foo',
                ['bar' => 'foo'],
                ['foo'],
            ],
        ];
    }

    /**
     * @dataProvider valuesProvider
     *
     * @param string $query
     * @param array  $values
     * @param array  $data
     */
    public function testConstructorWithData($query, array $values, array $data)
    {
        $instance = new Update('foo', $values);
        $this->assertSame($query, $instance->build());
        $this->assertSame($data, $instance->data());
    }

    /**
     * @dataProvider valuesProvider
     *
     * @param string $query
     * @param array  $values
     * @param array  $data
     */
    public function testFactoryWithData($query, array $values, array $data)
    {
        $instance = Query::update('foo', $values);
        $this->assertSame($query, $instance->build());
        $this->assertSame($data, $instance->data());
    }

    /**
     * @dataProvider valuesProvider
     *
     * @param string $query
     * @param array  $values
     * @param array  $data
     */
    public function testValues($query, array $values, array $data)
    {
        $this->assertSame($this->instance, $this->instance->values($values)); // chainable
        $this->assertSame($query, $this->instance->build());
        $this->assertSame($data, $this->instance->data());
    }

    public function valuesProvider()
    {
        return [
            ['UPDATE foo',
                [],
                [],
            ],
            ['UPDATE foo',
                [null => null],
                [],
            ],
            ['UPDATE foo',
                ['' => null],
                [],
            ],
            ['UPDATE foo SET foo = ?',
                ['foo' => null],
                [null],
            ],
            ['UPDATE foo SET foo = ?',
                ['foo' => ''],
                [''],
            ],
            ['UPDATE foo SET foo = ?',
                ['foo' => 'bar'],
                ['bar'],
            ],
            ['UPDATE foo SET foo = ?, bar = ?',
                ['foo' => 'bar', 'bar' => 'foo'],
                ['bar', 'foo'],
            ],
        ];
    }

    public function testValuesDuplicate()
    {
        $this->instance->values(['foo' => 'bar']);
        $this->instance->values(['foo' => 'bar']);
        $this->assertSame('UPDATE foo SET foo = ?, foo = ?', $this->instance->build());
        $this->assertSame(['bar', 'bar'], $this->instance->data());
    }

    public function testSetChainable()
    {
        $this->assertSame($this->instance->set('foo', null), $this->instance);
    }

    public function testSetWithNull()
    {
        $this->instance->set('foo', null);
        $this->assertSame('UPDATE foo SET foo = ?', $this->instance->build());
        $this->assertSame([null], $this->instance->data());
    }

    public function testSetWithEmpty()
    {
        $this->instance->set('foo', '');
        $this->assertSame('UPDATE foo SET foo = ?', $this->instance->build());
        $this->assertSame([''], $this->instance->data());
    }

    public function testSetDuplicate()
    {
        $this->instance->set('foo', 'bar');
        $this->instance->set('foo', 'bar');
        $this->assertSame('UPDATE foo SET foo = ?, foo = ?', $this->instance->build());
        $this->assertSame(['bar', 'bar'], $this->instance->data());
    }

    public function testSetWithInitialData()
    {
        $instance = new Update('foo', ['foo' => 'bar']);
        $instance->set('foo', 'bar');
        $this->assertSame('UPDATE foo SET foo = ?, foo = ?', $instance->build());
        $this->assertSame(['bar', 'bar'], $instance->data());
    }
}
