<?php

declare(strict_types=1);

namespace Medas\JwtTokens;

use Firebase\JWT\{JWT, Key};
use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    Interfaces\AuthenticationData,
    Interfaces\AuthenticationTokenController
};
use Medas\ObjectToArraySerializer\ObjectToArraySerializer;

#[Service]
readonly class JwtAuthTokenController implements AuthenticationTokenController
{
    public function __construct(
        #[ConfigValue(ConfigOptions\JwtAuthTokenKey::class)]
        private string                  $tokenKey,

        #[ConfigValue(ConfigOptions\JwtAuthTokenAlgorithm::class)]
        private string                  $algorithm,
        private ObjectToArraySerializer $serializer,
    )
    {
    }

    public function create(AuthenticationData $data): string
    {
        $payLoad = [
            'data' => $this->serializer->serialize($data),
            'class' => $data::class,
        ];

        return JWT::encode($payLoad, $this->tokenKey, $this->algorithm);
    }

    public function invalidate(string $token): void
    {
        // Do nothing, JWT tokens cannot be invalidated.
    }

    public function data(string $token): AuthenticationData|null
    {
        $payload = JWT::decode($token, new Key($this->tokenKey, $this->algorithm));

        /** @var AuthenticationData */
        return $this->serializer->unserialize($payload->data, class: $payload->class);
    }

    public function userId(string $token): mixed
    {
        return $this->data($token)?->getUserId();
    }
}
