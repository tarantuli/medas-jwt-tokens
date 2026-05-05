<?php

declare(strict_types=1);

namespace Medas\JwtTokens;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\Json\JsonPackage;
use Medas\ObjectToArraySerializer\ObjectToArraySerializerPackage;

class JwtTokensPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            JsonPackage::instance(),
            ObjectToArraySerializerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
