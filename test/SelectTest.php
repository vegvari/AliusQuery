<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;

class SelectTest extends PHPUnit_Framework_TestCase
{
    protected $instance;

    public function setUp()
    {
        $this->instance = new Select(null);
    }

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
        $instance = new Select($expr, $data);
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
        $instance = Query::select($expr, $data);
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
            ['SELECT foo',
                'foo',
                [],
                [],
            ],
            ['SELECT foo = ?',
                'foo = ?',
                'bar',
                ['bar'],
            ],
        ];
    }

    /**
     * @dataProvider selectDataProvider
     *
     * @param string $query
     * @param mixed  $expr
     * @param array  $data
     */
    public function testSelect($query, $expr, array $data)
    {
        $this->assertSame($this->instance->select($expr), $this->instance); // chainable
        $this->assertSame($query, $this->instance->build());
        $this->assertSame($data, $this->instance->data());
    }

    public function selectDataProvider()
    {
        return [
            ['', null, []],
            ['', '', []],
            ['SELECT foo', 'foo', []],
        ];
    }

    public function testFrom()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->from(null));
        $this->instance->from('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->from('foo');
        $this->instance->from('bar');
        $this->assertSame('FROM foo, bar', $this->instance->build());

        // set string with data
        $this->instance->from('? = ?', [1, 2]);
        $this->assertSame('FROM foo, bar, ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testJoin()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->join(null));
        $this->instance->join('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->join('foo');
        $this->assertSame('JOIN foo', $this->instance->build());

        // set string with data
        $this->instance->join('? = ?', [1, 2]);
        $this->assertSame('JOIN foo JOIN ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testLeftJoin()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->leftJoin(null));
        $this->instance->leftJoin('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->leftJoin('foo');
        $this->assertSame('LEFT JOIN foo', $this->instance->build());

        // set string with data
        $this->instance->leftJoin('? = ?', [1, 2]);
        $this->assertSame('LEFT JOIN foo LEFT JOIN ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testRightJoin()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->rightJoin(null));
        $this->instance->rightJoin('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->rightJoin('foo');
        $this->assertSame('RIGHT JOIN foo', $this->instance->build());

        // set string with data
        $this->instance->rightJoin('? = ?', [1, 2]);
        $this->assertSame('RIGHT JOIN foo RIGHT JOIN ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testCrossJoin()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->crossJoin(null));
        $this->instance->crossJoin('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->crossJoin('foo');
        $this->assertSame('CROSS JOIN foo', $this->instance->build());

        // set string with data
        $this->instance->crossJoin('? = ?', [1, 2]);
        $this->assertSame('CROSS JOIN foo CROSS JOIN ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testGroupBy()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->groupBy(null));
        $this->instance->groupBy('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->groupBy('foo');
        $this->assertSame('GROUP BY foo', $this->instance->build());

        // set string with data
        $this->instance->groupBy('? = ?', [1, 2]);
        $this->assertSame('GROUP BY foo, ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testHaving()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->having(null));
        $this->instance->having('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->having('foo');
        $this->assertSame('HAVING foo', $this->instance->build());

        // set string with data
        $this->instance->having('? = ?', [1, 2]);
        $this->assertSame('HAVING foo ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testOrderBy()
    {
        // set null, empty, test chainable
        $this->assertSame($this->instance, $this->instance->orderBy(null));
        $this->instance->orderBy('');
        $this->assertSame('', $this->instance->build());

        // set string
        $this->instance->orderBy('foo');
        $this->assertSame('ORDER BY foo', $this->instance->build());

        // set string with data
        $this->instance->orderBy('? = ?', [1, 2]);
        $this->assertSame('ORDER BY foo, ? = ?', $this->instance->build());
        $this->assertSame([1, 2], $this->instance->data());
    }

    public function testLimit()
    {
        // set null, empty, zero, test chainable
        $this->assertSame($this->instance, $this->instance->limit(null));
        $this->instance->limit('');
        $this->instance->limit(0);
        $this->assertSame('', $this->instance->build());

        // set int
        $this->instance->limit(10);
        $this->assertSame('LIMIT 10', $this->instance->build());
    }

    public function testOffset()
    {
        // set null, empty, zero, int, test chainable
        $this->assertSame($this->instance, $this->instance->offset(null));
        $this->instance->offset('');
        $this->instance->offset(0);
        $this->assertSame('', $this->instance->build());

        // set limit
        $this->instance->offset(10)->limit(10);
        $this->assertSame('LIMIT 10 OFFSET 10', $this->instance->build());
    }

    public function testPage()
    {
        // set null, empty, zero, int, test chainable
        $this->assertSame($this->instance, $this->instance->page(null));
        $this->instance->page('');
        $this->instance->page(0);
        $this->assertSame('', $this->instance->build());

        // set int with limit
        $this->instance->page(10)->limit(10);
        $this->assertSame('LIMIT 10 OFFSET 100', $this->instance->build());
    }
}
