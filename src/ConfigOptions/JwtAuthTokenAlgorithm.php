<?php

declare(strict_types=1);

namespace Medas\JwtTokens\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class JwtAuthTokenAlgorithm implements ConfigOption
{
    public function __construct(
        private JwtTokensGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'authentication-token-algorithm';
    }

    public function description(): string
    {
        return 'The algorithm used to sign authentication tokens.';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'HS256';
    }
}
