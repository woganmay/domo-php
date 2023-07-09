<?php

namespace WoganMay\DomoPHP;

class Util
{
    /**
     * Cleans up a provided array by removing all null elements, and optionally, dropping all keys not in $allowedKeys.
     * @param array $input The input data to clean up
     * @param array|null $allowedKeys The list of keys to allow through
     * @return array
     */
    public static function trimArrayKeys(array $input, ?array $allowedKeys = null) : array
    {
        $trimmed = [];

        if ($allowedKeys == null) {
            // If there's no filtering rule here, allow everything, but still remove null
            // values if any are present.
            foreach(array_keys($input) as $key) {
                if ($input[$key] != null) $trimmed[$key] = $input[$key];
            }
        }
        else
        {
            // Only allow given keys into the final array
            foreach($allowedKeys as $key) {
                if (array_key_exists($key, $input) && $input[$key] != null) $trimmed[$key] = $input[$key];
            }
        }

        return $trimmed;
    }
}