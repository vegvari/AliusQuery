<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;
use Alius\Query\Exceptions\InvalidParameterCount;

class WhereTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $expr
     * @param mixed  $data
     * @param array  $expected_data
     */
    public function testConstructor($query, $expr, $data, array $expected_data)
    {
        $instance = new Where($expr, $data);
        $this->assertSame($query, $instance->build());
        $this->assertSame($expected_data, $instance->data());
    }

    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $query
     * @param string $expr
     * @param mixed  $data
     * @param array  $expected_data
     */
    public function testFactory($query, $expr, $data, array $expected_data)
    {
        $instance = Query::where($expr, $data);
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
            ['foo',
                'foo',
                [],
                [],
            ],
            ['foo = ?',
                'foo = ?',
                'bar',
                ['bar'],
            ],
        ];
    }

    public function whereDataProvider()
    {
        return [
            [new Delete(null)],
            [new Select(null)],
            [new Update(null)],
            [new Where(null)],
        ];
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testNullEmpty(Statement $instance)
    {
        $this->assertSame($instance->where(null), $instance);
        $instance->where('');
        $this->assertSame(null, $instance->buildWhere());

        $this->assertSame($instance->andWhere(null), $instance);
        $instance->andWhere('');
        $this->assertSame(null, $instance->buildWhere());

        $this->assertSame($instance->orWhere(null), $instance);
        $instance->orWhere('');
        $this->assertSame(null, $instance->buildWhere());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testClosure(Statement $instance)
    {
        // set empty Closure (to make sure no content generated + check return)
        $this->assertSame($instance->where(function () {
        }), $instance);
        $this->assertSame(null, $instance->buildWhere());

        $this->assertSame($instance->andWhere(function () {
        }), $instance);
        $this->assertSame(null, $instance->buildWhere());

        $this->assertSame($instance->orWhere(function () {
        }), $instance);
        $this->assertSame(null, $instance->buildWhere());

        // set Closure
        $instance->where(function ($q) {
            $q->where('foo = ?', 'closure1')->andWhere('bar = ?', 'closure2');
        });
        $this->assertSame('WHERE (foo = ? AND bar = ?)', $instance->buildWhere());

        $instance->andWhere(function ($q) {
            $q->where('foo = ?', 'closure1')->andWhere('bar = ?', 'closure2');
        });
        $this->assertSame('WHERE (foo = ? AND bar = ?) AND (foo = ? AND bar = ?)', $instance->buildWhere());

        $instance->orWhere(function ($q) {
            $q->where('foo = ?', 'closure1')->andWhere('bar = ?', 'closure2');
        });
        $this->assertSame('WHERE (foo = ? AND bar = ?) AND (foo = ? AND bar = ?) OR (foo = ? AND bar = ?)', $instance->buildWhere());

        // check data
        $this->assertSame(['closure1', 'closure2', 'closure1', 'closure2', 'closure1', 'closure2'], $instance->data());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testWhere(Statement $instance)
    {
        $instance->where('foo = ?', 'bar');
        $this->assertSame('WHERE foo = ?', $instance->buildWhere());

        $instance->andWhere('bar = ?', 'foo');
        $this->assertSame('WHERE foo = ? AND bar = ?', $instance->buildWhere());

        $instance->orWhere('foobar = ?', 'barfoo');
        $this->assertSame('WHERE foo = ? AND bar = ? OR foobar = ?', $instance->buildWhere());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testAndWhere(Statement $instance)
    {
        $instance->andWhere('foo = ?', 'bar');
        $this->assertSame('WHERE foo = ?', $instance->buildWhere());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testAndWhereArray(Statement $instance)
    {
        $instance->andWhereArray(['foo' => 'bar', 'bar' => 'foo']);
        $this->assertSame('WHERE foo = ? AND bar = ?', $instance->buildWhere());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testOrWhere(Statement $instance)
    {
        $instance->orWhere('foo = ?', 'bar');
        $this->assertSame('WHERE foo = ?', $instance->buildWhere());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testBetween(Statement $instance)
    {
        $instance->where('foobar BETWEEN', ['foo', 'bar']);
        $this->assertSame('WHERE foobar BETWEEN ? AND ?', $instance->buildWhere());
        $this->assertSame(['foo', 'bar'], $instance->data());
    }

    /**
     * @dataProvider whereDataProvider
     *
     * @param Alius\Query\Statement $instance
     */
    public function testIn(Statement $instance)
    {
        // set scalar
        $instance->where('foobar IN', 'foo');
        $this->assertSame('WHERE foobar IN (?)', $instance->buildWhere());

        // set array
        $instance->where('foobar IN', ['bar', 'foo']);
        $this->assertSame('WHERE foobar IN (?) foobar IN (?, ?)', $instance->buildWhere());
        $this->assertSame(['foo', 'bar', 'foo'], $instance->data());
    }
}
