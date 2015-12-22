<?php

namespace Alius\Query\Traits;

trait testWhere
{
    public function testWhereEq()
    {
        $return = $this->instance->where('bar')->eq(12);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE bar = :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12], $this->instance->data());
    }

    public function testWhereNe()
    {
        $return = $this->instance->where('bar')->ne(12);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE bar <> :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12], $this->instance->data());
    }

    public function testWhereLt()
    {
        $return = $this->instance->where('bar')->lt(12);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE bar < :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12], $this->instance->data());
    }

    public function testWhereLte()
    {
        $return = $this->instance->where('bar')->lte(12);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE bar <= :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12], $this->instance->data());
    }

    public function testWhereGt()
    {
        $return = $this->instance->where('bar')->gt(12);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE bar > :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12], $this->instance->data());
    }

    public function testWhereGte()
    {
        $return = $this->instance->where('bar')->gte(12);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE bar >= :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12], $this->instance->data());
    }

    public function testWhereIn()
    {
        $return = $this->instance->where('foo')->in(12)
            ->or('bar')->in([2, 4, 6]);

        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE foo IN (:data0) OR bar IN (:data1, :data2, :data3)', $this->instance->buildWhere());
        $this->assertSame([':data0' => 12, ':data1' => 2, ':data2' => 4, ':data3' => 6], $this->instance->data());
    }

    public function testWhereBetween()
    {
        $return = $this->instance->where('foo')->between(10, 20);
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE foo BETWEEN :data0 AND :data1', $this->instance->buildWhere());
        $this->assertSame([':data0' => 10, ':data1' => 20], $this->instance->data());
    }

    public function testWhereLike()
    {
        $return = $this->instance->where('foo')->like('%bar%');
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE foo LIKE :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => '%bar%'], $this->instance->data());
    }

    public function testWhereNotLike()
    {
        $return = $this->instance->where('foo')->notLike('%bar%');
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE foo NOT LIKE :data0', $this->instance->buildWhere());
        $this->assertSame([':data0' => '%bar%'], $this->instance->data());
    }

    public function testWhereIsNull()
    {
        $return = $this->instance->where('foo')->isNull();
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE foo IS NULL', $this->instance->buildWhere());
        $this->assertSame([], $this->instance->data());
    }

    public function testWhereIsNotNull()
    {
        $return = $this->instance->where('foo')->isNotNull();
        $this->assertSame($this->instance, $return);
        $this->assertSame('WHERE foo IS NOT NULL', $this->instance->buildWhere());
        $this->assertSame([], $this->instance->data());
    }
}
