<?php

namespace Alius\Query;

use PHPUnit_Framework_TestCase;
use Alius\Query\Exceptions\InvalidParameterCount;

class StatementTest extends PHPUnit_Framework_TestCase
{
    protected $instances = [];

    public function setUp()
    {
        $this->instances[] = new Delete(null);
        $this->instances[] = new Insert(null);
        $this->instances[] = new Select(null);
        $this->instances[] = new Update(null);
        $this->instances[] = new Where(null);

        return $this->instances;
    }

    public function testAddExpression()
    {
        foreach ($this->instances as $instance) {
            $instance->addExpression('query', null);
            $this->assertSame([], $instance->getQuery());
            $this->assertSame([], $instance->getData());

            $instance->addExpression('query', '');
            $this->assertSame([], $instance->getQuery());
            $this->assertSame([], $instance->getData());

            $instance->addExpression('query', 'foo');
            $this->assertSame(['foo'], $instance->getQuery());
            $this->assertSame([], $instance->getData());

            $instance->addExpression('query', 'foo = ?', null);
            $this->assertSame(['foo', 'foo = ?'], $instance->getQuery());
            $this->assertSame([null], $instance->getData());

            $instance->addExpression('query', 'foo = ?', '');
            $this->assertSame(['foo', 'foo = ?', 'foo = ?'], $instance->getQuery());
            $this->assertSame([null, ''], $instance->getData());
        }
    }

    public function testAddInsert()
    {
        foreach ($this->instances as $instance) {
            $instance->addExpression('query', (new Insert('foobar', ['foo' => 'bar'])));
            $this->assertSame(['(INSERT INTO foobar (foo) VALUES (?))'], $instance->getQuery());
            $this->assertSame(['bar'], $instance->getData());
        }
    }

    public function testAddDelete()
    {
        foreach ($this->instances as $instance) {
            $instance->addExpression('query', (new Delete('foobar'))->where('foo = ?', 'bar'));
            $this->assertSame(['(DELETE FROM foobar WHERE foo = ?)'], $instance->getQuery());
            $this->assertSame(['bar'], $instance->getData());
        }
    }

    public function testAddSelect()
    {
        foreach ($this->instances as $instance) {
            $instance->addExpression('query', (new Select('foobar'))->where('foo = ?', 'bar'));
            $this->assertSame(['(SELECT foobar WHERE foo = ?)'], $instance->getQuery());
            $this->assertSame(['bar'], $instance->getData());
        }
    }

    public function testAddUpdate()
    {
        foreach ($this->instances as $instance) {
            $instance->addExpression('query', (new Update('foobar'))->where('foo = ?', 'bar'));
            $this->assertSame(['(UPDATE foobar WHERE foo = ?)'], $instance->getQuery());
            $this->assertSame(['bar'], $instance->getData());
        }
    }

    public function testAddWhere()
    {
        foreach ($this->instances as $instance) {
            $instance->addExpression('query', (new Where('foo = ?', 'bar')));
            $this->assertSame(['(foo = ?)'], $instance->getQuery());
            $this->assertSame(['bar'], $instance->getData());
        }
    }

    /**
     * @dataProvider invalidExpressionProvider
     *
     * @param Alius\Query\Statement $instance
     * @param mixed                 $expr
     * @param mixed                 $data
     */
    public function testInvalidExpression(Statement $instance, $expr, $data)
    {
        $this->setExpectedException(InvalidParameterCount::class);
        $instance->addExpression('query', $expr, $data);
    }

    public function invalidExpressionProvider()
    {
        foreach ($this->setUp() as $instance) {
            $array[] = [$instance, null, null];
            $array[] = [$instance, null, ''];

            $array[] = [$instance, '', null];
            $array[] = [$instance, '', ''];

            $array[] = [$instance, '?', []];
        }

        return $array;
    }
}
