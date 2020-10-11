<?php

namespace PK\Exceptions\Http;

class NotFound extends \Exception
{
    protected $message = 'Нет такого ресурса';
}
