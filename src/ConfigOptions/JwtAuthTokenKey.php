<?php

declare(strict_types=1);

namespace Medas\JwtTokens\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class JwtAuthTokenKey implements ConfigOption
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
        return 'authentication-token-key';
    }

    public function description(): string
    {
        return 'The key used to sign authentication tokens.';
    }

    public function hasDefault(): bool
    {
        return false;
    }

    public function default(): null
    {
        return null;
    }
}
