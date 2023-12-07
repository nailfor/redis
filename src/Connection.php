<?php

namespace nailfor\Redis;

use nailfor\Redis\Query\QueryBuilder;

use Illuminate\Database\Connection as BaseConnection;

class Connection extends BaseConnection
{
    protected Client $client;
    
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
        
        $this->client = new Client();
    }

    /**
     * @inheritdoc
     */
    public function query()
    {
        return new QueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }
    
    /**
     * Return Redis client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
