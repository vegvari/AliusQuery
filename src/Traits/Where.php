<?php

namespace Alius\Query\Traits;

use Closure;

trait Where
{
    /**
     * @var array
     */
    protected $where = [];

    /**
     * Add where condition
     *
     * @param string      $expr
     * @param mixed       $data
     * @param string|null $operator
     *
     * return this
     */
    protected function addWhere($expr, $data = [], $operator = null)
    {
        if ($expr instanceof Closure) {
            return $this->addClosure($expr, $operator);
        }

        $data = is_array($data) ? $data : [$data];

        if (preg_match('/\s+IN$/ui', (string) $expr) === 1) {
            $tokens = str_repeat('?, ', count($data));
            $tokens = substr($tokens, 0, strlen($tokens) - 2);
            $expr .= ' (' . $tokens . ')';
        } elseif (preg_match('/\s+BETWEEN$/ui', (string) $expr) === 1) {
            $expr .= ' ? AND ?';
        }

        if (($expr = $this->processExpression($expr, $data)) !== '') {
            $this->where[] = $operator === null ? $expr : $operator . ' ' . $expr;
        }

        return $this;
    }

    protected function addClosure(Closure $expr, $operator = null)
    {
        $before = count($this->where);
        $expr($this);
        $after = count($this->where);

        if (isset($this->where[$before])) {
            $this->where[$before] = $operator === null ? '(' . $this->where[$before] : $operator . ' (' . $this->where[$before];
            $this->where[$after - 1] .= ')';
        }

        return $this;
    }

    /**
     * Add AND where condition
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function where($expr, $data = [])
    {
        return $this->addWhere($expr, $data);
    }

    /**
     * Add AND where condition
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function andWhere($expr, $data = [])
    {
        return $this->addWhere($expr, $data, 'AND');
    }

    /**
     * Add OR where condition
     *
     * @param string $expr
     * @param mixed  $data
     *
     * @return this
     */
    public function orWhere($expr, $data = [])
    {
        return $this->addWhere($expr, $data, 'OR');
    }

    /**
     * Build
     *
     * @return string|null
     */
    public function buildWhere()
    {
        if (! empty($this->where)) {
            return $this->query[] = 'WHERE ' . implode(' ', $this->where);
        }
    }
}
