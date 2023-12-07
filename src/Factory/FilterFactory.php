<?php

namespace nailfor\Redis\Factory;

use nailfor\Redis\Query\Filters\Filter;

class FilterFactory
{
    public static function create(string $key, array $params)
    {
        $type = $params['type'];
        $class = "nailfor\\Redis\\Query\\Filters\\{$type}Filter";

        if (!class_exists($class)) {
            $class = Filter::class;
        }
        $filter = new $class($key, $params);
        
        return $filter->getFilter();
    }
}
