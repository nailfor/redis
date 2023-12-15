<?php

namespace nailfor\Redis;

use Illuminate\Support\Facades\Redis;
use Illuminate\Redis\Connections\PredisConnection;
use nailfor\Redis\Query\Commands\CommandsInterface;

class Client
{
    protected PredisConnection $client;

    public static array $commands = [];
    
    public function __construct()
    {
        $this->client = Redis::connection();
        $this->addScripts();
    }

    protected function addScripts(): void
    {
        /** @vat Predis\Command\RedisFactory $factory */
        $factory = $this->client->getCommandFactory();

        $iterator = new ClassIterator(CommandsInterface::class);
        foreach ($iterator->handle() as $method => $class)
        {
            $factory->define($method, $class);
            static::$commands[$method] = new $class();
        }
    }

    public function __call($name, $params)
    {
        $class = static::$commands[$name] ?? 0;
        if ($class && method_exists($class, 'before')) {
            $params = $class->before(...$params);
        }

        $result =  $this->client->{$name}(...$params);

        if ($class && method_exists($class, 'after')) {
            $result = $class->after($result);
        }

        return $result;
    }
}
