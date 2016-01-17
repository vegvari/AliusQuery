<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;
use Alius\Query\Exceptions\InvalidColumn;

class InsertTest extends PHPUnit_Framework_TestCase
{
    protected $instance;

    public function setUp()
    {
        $this->instance = new Insert('foo');
    }

    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $name
     */
    public function testConstructor($query, $name)
    {
        $instance = new Insert($name);
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
        $instance = Query::insert($name);
        $this->assertSame($query, $instance->build());
        $this->assertSame([], $instance->data());
    }

    public function constructorDataProvider()
    {
        return [
            ['', null],
            ['', ''],
            ['INSERT INTO foo', 'foo'],
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
        $instance = new Insert('foo', $values);
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
        $instance = Query::insert('foo', $values);
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
            ['INSERT INTO foo',
                [],
                [],
            ],
            ['INSERT INTO foo',
                [null => null],
                [],
            ],
            ['INSERT INTO foo',
                ['' => null],
                [],
            ],
            ['INSERT INTO foo (foo) VALUES (?)',
                ['foo' => null],
                [null],
            ],
            ['INSERT INTO foo (foo) VALUES (?)',
                ['foo' => ''],
                [''],
            ],
            ['INSERT INTO foo (foo) VALUES (?)',
                ['foo' => 'bar'],
                ['bar'],
            ],
            ['INSERT INTO foo (foo) VALUES (?), (?), (?)',
                [['foo' => 'bar'], ['foo' => 'bar'], ['foo' => 'bar']],
                ['bar', 'bar', 'bar'],
            ],
            ['INSERT INTO foo (foo, bar) VALUES (?, ?)',
                ['foo' => 'bar', 'bar' => 'foo'],
                ['bar', 'foo'],
            ],
            ['INSERT INTO foo (foo, bar) VALUES (?, ?), (?, ?), (?, ?)',
                [['foo' => 'bar', 'bar' => 'foo'], ['foo' => 'bar', 'bar' => 'foo'], ['foo' => 'bar', 'bar' => 'foo']],
                ['bar', 'foo', 'bar', 'foo', 'bar', 'foo'],
            ],
        ];
    }

    public function testValuesWithInitialData()
    {
        $instance = new Insert('foo', ['foo' => 'bar']);
        $instance->values(['foo' => 'bar']);
        $this->assertSame('INSERT INTO foo (foo) VALUES (?), (?)', $instance->build());
        $this->assertSame(['bar', 'bar'], $instance->data());
    }

    public function testValuesMoreColumn()
    {
        $this->setExpectedException(InvalidColumn::class);
        $instance = new Insert('foo');
        $instance->values([['foo' => 'bar'], ['foo' => 'bar', 'bar' => 'foo']]);
    }

    public function testValuesLessColumn()
    {
        $this->setExpectedException(InvalidColumn::class);
        $instance = new Insert('foo');
        $instance->values([['foo' => 'bar', 'bar' => 'foo'], ['foo' => 'bar']]);
    }
}
