<?php

namespace nailfor\Redis\Query\Filters;

class AbstractFilter
{
    protected string $from;

    protected string $column;

    protected mixed $value;

    protected string $operator;

    public function __construct(string $from, array $data)
    {
        $this->from     = $from;
        $this->column   = $data['column'] ?? '';
        $this->value    = $data['value'] ?? '';
        $this->operator = $data['operator'] ?? '=';
    }

    /**
     * Return current filter
     */
    public function getFilter(): array
    {
        return [
            "{$this->from}:{$this->value}"
        ];
    }

    protected function getKey(string $key): string
    {
        return "{$this->from}:$key";
    }
}
