<?php

namespace nailfor\Redis\Helpers;

class FileIterator
{
    protected array $skip = [
        '.',
        '..',
    ];

    protected string $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function handle()
    {
        $files = scandir($this->dir);
        foreach ($files as $file) {
            $info = pathinfo($file);
            $name = $info['filename'] ?? '';
            if (!$name || in_array($name, $this->skip)) {
                continue;
            }

            yield $name;
        }
    }
}
