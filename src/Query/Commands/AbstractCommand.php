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
}
