<?php

namespace nailfor\Redis\Eloquent\Modules;

class Expire extends Module
{
    public function handle(array $args): mixed
    {
        $model = $this->builder->getModel();
        if (!$model || !$model->id) {
            return $this;
        }

        $query = $this->getQuery();
        $query->expire($model->id, ...$args);

        return $this->builder;
    }
}
