<?php

namespace nailfor\Redis\Eloquent\Modules;

use nailfor\Redis\Eloquent\Builder;
use nailfor\Redis\Query\QueryBuilder;

abstract class Module implements ModuleInterface
{
    protected Builder $builder;

    abstract public function handle(array $args): mixed;

    public function __construct(Builder $builder)
    {
        $this->init($builder);
    }

    public function init(Builder $builder): void
    {
        $this->builder = $builder;
    }

    public function getQuery(): QueryBuilder
    {
        return $this->builder->getQuery();
    }
}
