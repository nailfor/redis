<?php

namespace nailfor\Redis\Eloquent;

use nailfor\Redis\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use nailfor\Redis\Eloquent\Modules\ModuleInterface;
use nailfor\Redis\Helpers\ModuleTrait;

class Builder extends EloquentBuilder
{
    use ModuleTrait;

    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
        $this->init(ModuleInterface::class, $this);
    }

    public function update(array $values)
    {
        $query = $this->query;
        $att = $this->model->getAttributes();
        $key = $this->model->getKeyName();
        $values[$key] = $att[$key] ?? 0;
        
        return $query->insert($values);
    }
}
