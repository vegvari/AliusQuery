<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;
use Alius\Query\Traits\testLimit;
use Alius\Query\Traits\testWhere;
use Alius\Query\Traits\testOrderBy;

use Alius\Query\Exceptions\MissingArgument;
use Alius\Query\Exceptions\UndefinedMethod;

class SelectTest extends PHPUnit_Framework_TestCase
{
    use testOrderBy, testLimit, testWhere;

    protected $instance;

    public function setUp()
    {
        $this->instance = new Select();
    }

    public function testSelect()
    {
        // default
        $this->assertSame('SELECT *', $this->instance->buildSelect());

        // * wins
        $this->instance->select('foo');
        $this->assertSame('SELECT *', $this->instance->buildSelect());

        // replace with string
        $this->instance->replaceSelect('foo');
        $this->assertSame('SELECT foo', $this->instance->buildSelect());

        // accept array
        $this->instance->replaceSelect(['foo', 'bar']);
        $this->assertSame('SELECT foo, bar', $this->instance->buildSelect());

        // skip duplicates
        $this->instance->select(['foo', 'bar', 'foobar']);
        $this->assertSame('SELECT foo, bar, foobar', $this->instance->buildSelect());

        // skip null, empty
        $this->instance->select([null, '']);
        $this->assertSame('SELECT foo, bar, foobar', $this->instance->buildSelect());

        // * wins again
        $this->instance->select(['apple', 'pear', '*']);
        $this->assertSame('SELECT *', $this->instance->buildSelect());

        // trim spaces
        $this->instance->replaceSelect(['  foo    ', '  bar   ', '   foo   bar  ']);
        $this->assertSame('SELECT foo, bar, foo bar', $this->instance->buildSelect());

        // chainable
        $this->assertSame($this->instance, $this->instance->select('*'));
        $this->assertSame($this->instance, $this->instance->replaceSelect('*'));
    }

    public function testFrom()
    {
        // default
        $this->assertSame(null, $this->instance->buildFrom());

        // accept string
        $this->instance->from('foo');
        $this->assertSame('FROM foo', $this->instance->buildFrom());

        // replace
        $this->instance->from('bar');
        $this->assertSame('FROM bar', $this->instance->buildFrom());

        // null, empty
        $this->instance->from(null)->from('');
        $this->assertSame('FROM bar', $this->instance->buildFrom());

        // chainable
        $this->assertSame($this->instance, $this->instance->from('foobar'));
    }

    public function testGroupBy()
    {
        // default
        $this->assertSame(null, $this->instance->buildGroupBy());

        // accept string
        $this->instance->groupBy('bar');
        $this->assertSame('GROUP BY bar', $this->instance->buildGroupBy());

        // accept array
        $this->instance->groupBy(['apple', 'pear']);
        $this->assertSame('GROUP BY bar, apple, pear', $this->instance->buildGroupBy());

        // skip duplicates (* isn't special)
        $this->instance->groupBy(['pear', 'apple', '*', '*']);
        $this->assertSame('GROUP BY bar, apple, pear, *', $this->instance->buildGroupBy());

        // null, empty
        $this->instance->groupBy([null, '']);
        $this->assertSame('GROUP BY bar, apple, pear, *', $this->instance->buildGroupBy());

        // trim spaces
        $this->instance->groupBy(['  foo    ', '  bar   ', '   foo   bar  ']);
        $this->assertSame('GROUP BY bar, apple, pear, *, foo, foo bar', $this->instance->buildGroupBy());

        // replace
        $this->instance->replaceGroupBy('bar');
        $this->assertSame('GROUP BY bar', $this->instance->buildGroupBy());

        // chainable
        $this->assertSame($this->instance, $this->instance->groupBy('foo'));
    }

    public function testOffset()
    {
        // default without limit
        $this->assertSame(null, $this->instance->buildLimit());

        // valid without limit
        $this->instance->offset(100);
        $this->assertSame(null, $this->instance->buildLimit());

        $this->instance->offset(0);
        $this->assertSame(null, $this->instance->buildLimit());

        // invalid without limit
        $this->instance->offset(-1);
        $this->assertSame(null, $this->instance->buildLimit());

        // valid with limit, set offset first
        $this->instance->offset(200)->limit(100);
        $this->assertSame('LIMIT 200,100', $this->instance->buildLimit());

        $this->instance->offset(0)->limit(100);
        $this->assertSame('LIMIT 100', $this->instance->buildLimit());

        // invalid with limit
        $this->instance->offset(-1);
        $this->assertSame('LIMIT 100', $this->instance->buildLimit());

        // chainable
        $this->assertSame($this->instance, $this->instance->offset(0));
    }

    public function testPage()
    {
        // valid
        $this->instance->limit(100)->page(3);
        $this->assertSame('LIMIT 300,100', $this->instance->buildLimit());

        $this->instance->page(0);
        $this->assertSame('LIMIT 100', $this->instance->buildLimit());

        // invalid
        $this->instance->page(-1);
        $this->assertSame('LIMIT 100', $this->instance->buildLimit());

        // chainable
        $this->assertSame($this->instance, $this->instance->page(0));
    }

    public function testBuild()
    {
        $this->instance
            ->select('*')
            ->from('foo')
            ->where('bar')->isNull()
            ->and('foobar')->isNull()
            ->or('barfoo')->isNull()
            ->and(function ($select) {
                return $select->where('fizz')->isNull()->or('buzz')->isNotNull();
            })
            ->groupBy('foo')
            ->orderBy('bar')
            ->limit(100)
            ->offset(200);

        $this->assertSame('SELECT * FROM foo WHERE bar IS NULL AND foobar IS NULL OR barfoo IS NULL AND (fizz IS NULL OR buzz IS NOT NULL) GROUP BY foo ORDER BY bar LIMIT 200,100', $this->instance->build());
        $this->assertSame('SELECT * FROM foo WHERE bar IS NULL AND foobar IS NULL OR barfoo IS NULL AND (fizz IS NULL OR buzz IS NOT NULL) GROUP BY foo ORDER BY bar LIMIT 200,100', (string) $this->instance);
    }

    public function testCallMissingArgument()
    {
        $this->setExpectedException(MissingArgument::class);
        $this->instance->and();
    }

    public function testCallUndefinedMethod()
    {
        $this->setExpectedException(UndefinedMethod::class);
        $this->instance->test();
    }
}
