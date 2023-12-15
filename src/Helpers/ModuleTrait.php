<?php
namespace nailfor\Redis\Helpers;

use nailfor\Redis\ClassIterator;

trait ModuleTrait
{
    public static array $modules = [];
    
    public function __call($method, $parameters)
    {
        $module = static::$modules[$method] ?? '';
        if ($module && method_exists($module, 'handle')) {
            return $module->handle($parameters);
        }
        
        return parent::__call($method, $parameters);
    }
    
    protected function init(string $interface, mixed $param): void
    {
        if (static::$modules) {
            return;
        }

        $iterator = new ClassIterator($interface);
        foreach ($iterator->handle() as $method => $class) {
            static::$modules[$method] = new $class($param);
        }
    }
    
    protected function getModules($method): array
    {
        $res = [];
        foreach(static::$modules as $module) {
            if (method_exists($module, $method)) {
                $res[] = $module;
            }
        }
        
        return $res;
    }
}
