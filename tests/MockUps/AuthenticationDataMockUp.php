<?php

declare(strict_types=1);

namespace Medas\JwtTokensTest\MockUps;

use Medas\Core\Interfaces\{AuthenticationData, Uuid};

readonly class AuthenticationDataMockUp implements AuthenticationData
{
    public function __construct(
        private Uuid  $uuid,
        public string $binaryData = "\x00\x01",
    )
    {
    }

    public function getUserId(): Uuid
    {
        return $this->uuid;
    }
}
