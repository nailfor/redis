<?php

namespace nailfor\Redis\Eloquent\Modules;

class TTL extends Module
{
    public function handle(array $args): mixed
    {
        $model = $this->builder->getModel();
        if (!$model || !$model->id) {
            return -2;
        }

        $query = $this->getQuery();

        return $query->ttl($model->id);
    }
}
