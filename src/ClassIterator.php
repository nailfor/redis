<?php

namespace nailfor\Redis;

use nailfor\Redis\Helpers\FileIterator;
use ReflectionClass;

class ClassIterator extends FileIterator
{
    const NAMESPACE = 'nailfor\Redis';

    protected string $interface = '';

    public function __construct(string $interface)
    {
        $this->interface = $interface;

        $dir = $this->getDir();
        $baseDir = $this->getBaseDir();
        parent::__construct($baseDir.$dir);
    }

    public function handle()
    {
        $namespace = $this->getNamespace();

        foreach (parent::handle() as $name) {
            $class = $namespace . '\\' . $name;
            if (!$this->check($class)) {
                continue;
            }

            yield $name => $class;
        }
    }

    protected function getDir(): string
    {
        $dir = str_replace(static::NAMESPACE, '', $this->getNamespace());

        return str_replace('\\', '/', $dir);
    }

    protected function getNamespace(): string
    {
        return substr($this->interface, 0, strrpos($this->interface, '\\'));
    }

    protected function getBaseDir(): string
    {
        return __DIR__;
    }

    protected function check(string $class): bool
    {
        $exists = class_exists($class);
        if (!$exists) {
            return false;
        }

        if ($this->interface && !is_subclass_of($class, $this->interface)) {
            return false;
        }

        $reflect = new ReflectionClass($class);

        return !$reflect->isAbstract();
    }
}
