<?php

namespace nailfor\Redis\Query\Commands;

use nailfor\Redis\Factory\FilterFactory;
use nailfor\Redis\Query\QueryBuilder;
use Predis\Command\ScriptCommand;

abstract class AbstractCommand extends ScriptCommand implements CommandsInterface
{
    /**
     * Return where params
     * @return array
     */
    protected function getArgs(QueryBuilder $builder) : array
    {
        $from = $builder->from;
        $result = [
            "$from:*",      //KEYS
                            //ARGV
        ];

        if (!$builder->wheres) {
            return $result;
        }

        foreach($builder->wheres as $where) {
            $where['column'] = str_replace($from . '.', '', $where['column']);
            $keys = $this->getFilter($from, $where);
            $result = array_merge($result, $keys);
        }
        
        return $result;
    }

    protected function getFilter(string $from, array $conditions) : array
    {
        return FilterFactory::create($from, $conditions);
    }

    protected function getKeysCondition(): string
    {
        return <<<LUA
            keys = ARGV
        LUA;
    }

    protected function getObject(): string
    {
        return <<<LUA
            local obj = {}
        LUA;
    }

    protected function getWhereOperator(): string
    {
        return <<<LUA
            if (
                (ok == false and where.boolean == 'and')
                or (ok == true and where.boolean == 'or')
            ) then
            elseif (where.operator == '=') then
                ok = column == tostring(where.value)
            elseif (where.operator == '<') then
                ok = column < tostring(where.value)
            elseif (where.operator == '>') then
                ok = column > tostring(where.value)
            elseif (where.operator == '<=') then
                ok = column <= tostring(where.value)
            elseif (where.operator == '>=') then
                ok = column >= tostring(where.value)
            end
        LUA;
    }

    protected function getWhereInOperator(): string
    {
        return <<<LUA
            for _,value in ipairs(where.values) do
                if (column == tostring(value)) then
                    ok = true
                end
            end
        LUA;
    }
}
