<?php

declare(strict_types=1);

namespace Medas\JwtTokensTest\Functional;

use Medas\Core\Interfaces\{Uuid, UuidProvider};
use Medas\JwtTokens\JwtAuthTokenController;
use Medas\JwtTokensTest\MockUps\AuthenticationDataMockUp;
use PHPUnit\Framework\TestCase;

class JwtAuthTokenControllerTest extends TestCase
{
    public function testBasicCreation(): void
    {
        $data = new AuthenticationDataMockUp(service(UuidProvider::class)->create());
        $controller = $this->getTokenController();
        $token = $controller->create($data);

        self::assertIsString($token);

        $result = $controller->data($token);

        self::assertEquals((string) $result->getUserId(), (string) $data->getUserId());
        self::assertInstanceOf(Uuid::class, $controller->userId($token));
    }

    private function getTokenController(): JwtAuthTokenController
    {
        sm()->config()->addManualBinding(
            JwtAuthTokenController::class,
            'tokenKey',
            random_bytes(32)
        );

        sm()->config()->addManualBinding(JwtAuthTokenController::class, 'algorithm', 'HS256');

        return service(JwtAuthTokenController::class);
    }
}
