<?php

namespace xgerhard\nbheaders;

use InvalidArgumentException;

/** @internal */
final class Fields
{
    public static function pick(array $data, array $fields): array
    {
        $picked = [];

        foreach ($fields as $field) {
            if (! is_string($data[$field] ?? null)) {
                throw new InvalidArgumentException("Missing or invalid field: $field");
            }

            $picked[$field] = $data[$field];
        }

        return $picked;
    }
}
