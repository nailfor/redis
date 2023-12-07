<?php

namespace nailfor\Redis\Exceptions;

use Exception;

class UnsupportedException extends Exception
{
    protected $message = 'Unsupported model type';
}
