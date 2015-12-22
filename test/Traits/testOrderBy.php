<?php

namespace Alius\Query\Traits;

trait testOrderBy
{
    public function testOrderBy()
    {
        // default
        $this->assertSame(null, $this->instance->buildOrderBy());

        // accept string
        $this->instance->orderBy('bar');
        $this->assertSame('ORDER BY bar', $this->instance->buildOrderBy());

        // accept array
        $this->instance->orderBy(['apple ASC', 'pear DESC']);
        $this->assertSame('ORDER BY bar, apple ASC, pear DESC', $this->instance->buildOrderBy());

        // skip duplicates
        $this->instance->orderBy(['pear DESC', 'apple ASC', '*', '*']);
        $this->assertSame('ORDER BY bar, apple ASC, pear DESC, *', $this->instance->buildOrderBy());

        // skip null, empty
        $this->instance->orderBy([null, '']);
        $this->assertSame('ORDER BY bar, apple ASC, pear DESC, *', $this->instance->buildOrderBy());

        // trim spaces
        $this->instance->orderBy(['  foo    ', '  bar   ', '   foo   bar  ']);
        $this->assertSame('ORDER BY bar, apple ASC, pear DESC, *, foo, foo bar', $this->instance->buildOrderBy());

        // replace
        $this->instance->replaceOrderBy('bar');
        $this->assertSame('ORDER BY bar', $this->instance->buildOrderBy());

        // chainable
        $this->assertSame($this->instance, $this->instance->orderBy('foo'));
    }
}
