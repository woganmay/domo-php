<?php

namespace WoganMay\DomoPHP;

class Util
{
    public static function trimArrayKeys(array $input, array $allowedKeys) : array
    {
        $trimmed = [];

        foreach($allowedKeys as $key) {
            if (array_key_exists($key, $input) && $input[$key] != null) $trimmed[$key] = $input[$key];
        }

        return $trimmed;
    }
}