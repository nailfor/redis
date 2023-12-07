<?php

namespace nailfor\Redis\Query\Modules;

use nailfor\Redis\Eloquent\Builder;
use nailfor\Redis\Query\QueryBuilder;

interface ModuleInterface
{
    public function newEloquentQuery(): Builder;

    public function newBuilder(): QueryBuilder;
}
