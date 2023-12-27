<?php

namespace nailfor\Redis\Eloquent\Modules;

use nailfor\Redis\Query\QueryBuilder;

interface ModuleInterface
{
    public function getQuery(): QueryBuilder;
}
