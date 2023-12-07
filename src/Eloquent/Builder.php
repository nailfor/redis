<?php

namespace nailfor\Redis\Eloquent;

use nailfor\Redis\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }
    
    public function update(array $values)
    {
        $query = $this->query;
        $att = $this->model->getAttributes();
        $key = $this->model->getKeyName();
        $values[$key] = $att[$key] ?? 0;
        
        return $query->update($values);
    }
}
