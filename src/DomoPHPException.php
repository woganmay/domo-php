<?php

namespace WoganMay\DomoPHP;

class DomoPHPException extends \Exception
{
    public function __construct(string $source, ?\Throwable $previous = null)
    {
        parent::__construct("Exception at $source: " . $previous->getMessage(), 0, $previous);
    }
}