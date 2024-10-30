<?php

namespace nailfor\Redis\Query\Commands\Traits;

use nailfor\Redis\Query\QueryBuilder;
use nailfor\Redis\Exceptions\PrimaryKeyException;

trait HashTrait
{
    protected function getKeysCondition(): string
    {
        $keys = $this->getCondition();

        return <<<LUA
            local values = cjson.decode(ARGV[1])

            if (values.keys) then
                keys = values.keys
            else
                {$keys}
            end
        LUA;
    }

    protected function getCondition(): string
    {
        $obj = $this->getObject();
        $whereInOperator = $this->getWhereInOperator();
        $whereOperator = $this->getWhereOperator();

        return <<<LUA
            local allkeys = cmd("KEYS", KEYS[1])
            for _,key in ipairs(allkeys) do
                {$obj}
                local ok = nil

                for _,where in pairs(values.wheres) do
                    local column = obj[where.column]

                    if (where.type == 'In') then
                        {$whereInOperator}
                    else
                        {$whereOperator}
                    end
                end

                if (ok) then
                    keys[#keys + 1] = key
                end
            end
        LUA;
    }

    protected function getObject(): string
    {
        return <<<LUA
            local data, k = cmd('HGETALL', key), 0
            local obj = {}
            for idx,val in pairs(data) do
                if (idx % 2 == 0) then
                    obj[k] = val
                else
                    k = val
                end
            end
        LUA;
    }

    public function before(QueryBuilder $builder): array
    {
        $result = [
            "{$builder->from}:*",
        ];

        if ($builder->wheres) {
            $data = [
                'wheres' => $builder->wheres
            ];
            try {
                $keys = $this->getArgs($builder);
                array_shift($keys);
                $data['keys'] = $keys;
            }
            catch(PrimaryKeyException $e) {
            }

            $result[] = json_encode($data);
        }
        
        return $result;
    }
}
