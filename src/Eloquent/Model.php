<?php

namespace nailfor\Redis\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use nailfor\Redis\Query\QueryBuilder;

class Model extends BaseModel
{
    protected $connection = 'redis';

    protected const TYPE_HASH = 'HASH';

    protected const TYPE_SET = 'SET';

    protected const MODEL_TYPE = self::TYPE_HASH;

    public function newEloquentBuilder($query): Builder
    {
        /** @var QueryBuilder $query*/
        $query->type = static::MODEL_TYPE;

        return new Builder($query);
    }

    protected function newRelatedInstance($class)
    {
        return new $class;
    }

    protected function getDirtyForUpdate()
    {
        return $this->attributes;
    }
}
