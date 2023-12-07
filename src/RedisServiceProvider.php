<?php

namespace nailfor\Redis;

use nailfor\Redis\Eloquent\Model;

use Illuminate\Support\ServiceProvider;

class RedisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Add database driver.
        $this->app->resolving('db', fn ($db) => $db
            ->extend('redis', function ($config, $name) {
                $config['name'] = $name;
                
                return new Connection(null, '', '', $config);
            })
        );
    }
}
