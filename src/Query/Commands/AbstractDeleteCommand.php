<?php

namespace nailfor\Redis\Query\Commands;

use nailfor\Redis\Query\QueryBuilder;

abstract class AbstractDeleteCommand extends AbstractCommand
{
    public function getKeysCount()
    {
        return 1;
    }

    public function getScript()
    {
        $keys = $this->getKeysCondition();

        return <<<LUA
            local cmd, keys = redis.call, {}
            local result = 0

            if #ARGV > 0 then
                {$keys}
            else
                keys = cmd("KEYS", KEYS[1])
            end

            for idx,key in ipairs(keys) do
                local _ = cmd('DEL', key)
                result = result + _
            end

            return result
        LUA;
    }

    public function before(QueryBuilder $builder): array
    {
        return $this->getArgs($builder);
    }

    public function after(int $data): int
    {
        return (int) $data;
    }
}
