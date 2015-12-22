<?php

namespace Alius\Query\Traits;

trait testLimit
{
    public function testLimit()
    {
        // default
        $this->assertSame(null, $this->instance->buildLimit());

        // valid
        $this->instance->limit(300);
        $this->assertSame('LIMIT 300', $this->instance->buildLimit());

        $this->instance->limit(null);
        $this->assertSame(null, $this->instance->buildLimit());

        $this->instance->limit(0);
        $this->assertSame('LIMIT 0', $this->instance->buildLimit());

        // invalid
        $this->instance->limit(-1);
        $this->assertSame('LIMIT 0', $this->instance->buildLimit());

        // chainable
        $this->assertSame($this->instance, $this->instance->limit(0));
    }
}
