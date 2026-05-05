<?php

declare(strict_types=1);

namespace Medas\JwtTokens\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class JwtTokensGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'jwt-tokens';
    }
}
