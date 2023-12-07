<?php

namespace nailfor\Redis\Query\Commands;

use nailfor\Redis\Query\QueryBuilder;

class HSETSelect extends AbstractCommand
{
    public function getKeysCount()
    {
        return 1;
    }

    public function getScript()
    {
        return <<<LUA
            local cmd, keys, results = redis.call, {}, {}

            if #ARGV > 0 then
                keys = ARGV
            else
                keys = cmd("KEYS", KEYS[1])
            end

            for idx,key in ipairs(keys) do
                local _ = cmd('HGETALL', key)
                if #_ > 0 then
                    results[#results + 1] = _
                end
            end
            
            return results
        LUA;
    }

    public function before(QueryBuilder $builder): array
    {
        return $this->getArgs($builder);
    }

    public function after(array $data): array
    {
        $result = [];
        foreach ($data as $item) {
            $object = [];
            $name = '';
            foreach ($item as $key => $val) {
                if ($key % 2 == 0) {
                    $name = $val;
                    continue;
                }

                $object[$name] = $val;
            }
            $result[] = $object;
        }

        return $result;
    }
}
