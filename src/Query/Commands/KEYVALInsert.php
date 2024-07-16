<?php

namespace nailfor\Redis\Query\Commands;

use nailfor\Redis\Query\QueryBuilder;

class KEYVALInsert extends AbstractCommand
{
    protected string $id;

    public function getKeysCount()
    {
        return 1;
    }

    public function getScript()
    {
        return <<<LUA
            local cmd, key, results = redis.call, KEYS[1], {}
            local value = ARGV[1]

            cmd("set", key, value)
            
            return KEYS[1]
        LUA;
    }

    public function before(QueryBuilder $builder, $values): array
    {
        $this->id = $values['id'];

        return [
            "{$builder->from}:{$this->id}",     //KEYS
            $values['value'] ?? '',             //ARGV
        ];
    }

    public function after(string $key): string
    {
        return $this->id;
    }
}
