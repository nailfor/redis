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

    public function handle(mixed $data): mixed
    {
        /** @var PredisConnection $client */
        $client = $this->getClient();
        /** @var RedisFactory $factory */
        $factory = $client->getCommandFactory();

        $type = $this->builder->type;
        $cmd = "{$type}{$this->command}";
        if (!$factory->supports($cmd)) {
            throw new UnsupportedException($type);
        }

        return $client->{$cmd}($this->builder, ...$data);
    }

    protected function getClient(): Client
    {
        return $this->builder->connection->getClient();
    }
}
