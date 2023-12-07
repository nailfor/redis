<?php

namespace nailfor\Redis\Exceptions;

use Exception;

class PrimaryKeyException extends Exception
{
    protected $message = 'Only primary key supported';
}
