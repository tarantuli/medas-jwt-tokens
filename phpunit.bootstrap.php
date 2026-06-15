<?php

declare(strict_types=1);

use Medas\JwtTokens\JwtTokensPackage;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\RamseyUuidBridge\RamseyUuidBridgePackage;
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        JwtTokensPackage::instance(),
        RamseyUuidBridgePackage::instance(),
    ]);

    return $config;
});
