<?php

namespace nailfor\Redis\Query\Modules;

class TTL extends Module
{
    protected string $command = 'ttl';

    protected function getType(): string
    {
        return '';
    }

    protected function getParams(array $data): mixed
    {
        $from = $this->builder->from;
        $data[0] = "{$from}:{$data[0]}";

        return $data;
    }
}
