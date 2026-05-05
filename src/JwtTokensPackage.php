<?php

declare(strict_types=1);

namespace Medas\JwtTokens;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\ObjectToArraySerializer\ObjectToArraySerializerPackage;

class JwtTokensPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ObjectToArraySerializerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
