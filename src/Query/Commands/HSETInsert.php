<?php

namespace nailfor\Redis\Query\Commands;

use nailfor\Redis\Query\QueryBuilder;

class HSETInsert extends AbstractCommand
{
    public function getKeysCount()
    {
        return 1;
    }

    public function getScript()
    {
        return <<<LUA
            local cmd, key, results = redis.call, KEYS[1], {}
            local values = cjson.decode(ARGV[1])

            for field,val in pairs(values) do
                cmd("HSET", key, field, val)
            end
            
            return KEYS[1]
        LUA;
    }

    public function before(QueryBuilder $builder, $values): array
    {
        $id = $values['id'];

        return [
            "{$builder->from}:$id",     //KEYS
            json_encode($values),       //ARGV
        ];
    }
}
