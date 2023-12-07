<?php

namespace nailfor\Redis\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Database\Query\Processors\Processor;
use nailfor\Redis\Helpers\GetSetTrait;
use nailfor\Redis\Helpers\ModuleTrait;
use nailfor\Redis\Query\Modules\ModuleInterface;

class QueryBuilder extends Builder
{
    use GetSetTrait;
    use ModuleTrait;

    /** @var nailfor\Redis\Connection $connection */
    public $connection;

    public string $type;

    public function __construct(ConnectionInterface $connection, Grammar $grammar = null, Processor $processor = null)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->init(ModuleInterface::class, $this);
    }

    /**
     * @inheritdoc
     */
    public function get($columns = ['*']): Collection
    {
        $res = $this->onceWithColumns(Arr::wrap($columns), function () {
            $items = $this->runSelect();
            return $this->processor->processSelect($this, $items);
        });
        
        return new Collection($res);
    }

    protected function runSelect(): array
    {
        return $this->SelectPlugin();
    }

    /**
     * @inheritdoc
     */
    public function insert(array $values): mixed
    {
        return $this->InsertPlugin($values);
    }

    public function insertGetId(array $values, $sequence = null): mixed
    {
        return $this->InsertPlugin($values);
    }
}
