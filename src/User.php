<?php

namespace xgerhard\nbheaders;

use InvalidArgumentException;

final readonly class User
{
    public function __construct(
        public string $name,
        public string $displayName,
        public string $provider,
        public string $providerId,
        public string $userLevel,
    ) {
    }

    /**
     * @throws InvalidArgumentException when a field is missing or isn't a string
     */
    public static function fromArray(array $data): self
    {
        return new self(...Fields::pick($data, ['name', 'displayName', 'provider', 'providerId', 'userLevel']));
    }
}
