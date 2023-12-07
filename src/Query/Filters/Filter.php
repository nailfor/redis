<?php

namespace nailfor\Redis\Query\Filters;
use nailfor\Redis\Exceptions\PrimaryKeyException;

class Filter extends AbstractFilter
{
    /**
     * Return current filter
     */
    public function getFilter(): array
    {
        if ($this->column != 'id') {
            throw new PrimaryKeyException($this->column);
        }

        return [
            $this->getKey($this->value),
        ];
    }
}
