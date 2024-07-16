<?php

namespace nailfor\Redis\Query\Commands;

use nailfor\Redis\Query\QueryBuilder;

class KEYVALSelect extends AbstractCommand
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
                local _ = cmd('GET', key)
                if _ then
                    local rec = {}
                    rec[1] = key
                    rec[2] = _
                    results[#results + 1] = rec
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
            $key = $item[0];
            $data = explode(':', $key);
            $result[] = [
                'id' => $data[1] ?? $key,
                'value' => $item[1],
            ];
        }

        return $result;
    }
}
