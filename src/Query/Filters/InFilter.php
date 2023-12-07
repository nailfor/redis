<?php

namespace nailfor\Redis\Query\Filters;
use nailfor\Redis\Exceptions\PrimaryKeyException;

class InFilter extends AbstractFilter
{
    protected array $values;

    public function __construct(string $from, array $data)
    {
        parent::__construct($from, $data);
        $this->values = $data['values'] ?? [];
    }

    /**
     * Return current filter
     */
    public function getFilter(): array
    {
        if ($this->column != 'id') {
            throw new PrimaryKeyException();
        }

        $result = [];
        foreach ($this->values as $value) {
            $result[] = $this->getKey($value);
        }

        return $result;
    }
}
