<?php

namespace nailfor\Redis\Query\Modules;

use Illuminate\Redis\Connections\PredisConnection;
use nailfor\Redis\Client;
use nailfor\Redis\Eloquent\Builder;
use nailfor\Redis\Query\QueryBuilder;
use nailfor\Redis\Exceptions\UnsupportedException;
use Predis\Command\RedisFactory;

abstract class Module implements ModuleInterface
{
    protected QueryBuilder $builder;

    protected string $command;

    public function __construct(QueryBuilder $builder)
    {
        $this->init($builder);
    }

    public function init(QueryBuilder $builder): void
    {
        $this->builder = $builder;
    }

    public function newEloquentQuery(): Builder
    {
        $queryBuilder = $this->builder->connection->query();

        return new Builder($queryBuilder);
    }

    public function newBuilder(): QueryBuilder
    {
        $query = $this->newEloquentQuery();

        return $query->getQuery();
    }

    public function handle(array $data): mixed
    {
        /** @var PredisConnection $client */
        $client = $this->getClient();
        /** @var RedisFactory $factory */
        $factory = $client->getCommandFactory();

        $type = $this->getType();
        $cmd = "{$type}{$this->command}";
        if (!$factory->supports($cmd)) {
            throw new UnsupportedException($type);
        }
        $params = $this->getParams($data);

        return $client->{$cmd}(...$params);
    }

    protected function getParams(array $data): mixed
    {
        return array_merge([$this->builder], $data);
    }

    protected function getType(): string
    {
        return $this->builder->type;
    }

    protected function getClient(): Client
    {
        return $this->builder->connection->getClient();
    }
}
