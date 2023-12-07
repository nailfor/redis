<?php
namespace nailfor\Redis\Helpers;

trait GetSetTrait
{
    public $attributes = [];
    
    public function __get(string $name)
    {
        if (key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
    }
    
    public function __set(string $name, $val)
    {
        $this->attributes[$name] = $val;
    }
    
}
