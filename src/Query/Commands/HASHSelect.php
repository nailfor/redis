<?php

namespace nailfor\Redis\Query\Commands;

class HASHSelect extends AbstractCommand
{
    use Traits\HashTrait;

    public function getKeysCount()
    {
        return 1;
    }

    public function getScript()
    {
        $keys = $this->getKeysCondition();

        return <<<LUA
            local cmd, keys = redis.call, {}
            local result = {}

            if #ARGV > 0 then
                {$keys}
            else
                keys = cmd("KEYS", KEYS[1])
            end

            for idx,key in ipairs(keys) do
                local _ = cmd('HGETALL', key)
                if #_ > 0 then
                    result[#result + 1] = _
                end
            end
            
            return result
        LUA;
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
