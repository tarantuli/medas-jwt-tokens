<?php

declare(strict_types=1);

namespace Medas\JwtTokens;

use Firebase\JWT\{JWT, Key};
use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    Interfaces\AuthenticationData,
    Interfaces\AuthenticationTokenController,
    Interfaces\CacheManager
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
        private CacheManager            $cacheManager,
    )
    {
    }

    public function create(AuthenticationData $data): string
    {
        $payload = [
            'data' => $this->serializer->serialize($data),
            'class' => $data::class,
        ];

        return JWT::encode($payload, $this->tokenKey, $this->algorithm);
    }

    public function invalidate(string $token): void
    {
        // Do nothing, JWT tokens cannot be invalidated.
    }

    public function data(string $token): AuthenticationData|null
    {
        return $this->cacheManager->get('memory')->get(
            'jwt-tokens:data:' . sha1($token),
            fn() => $this->decode($token)
        );
    }

    private function decode(string $token): AuthenticationData|null
    {
        try {
            $payload = JWT::decode($token, new Key($this->tokenKey, $this->algorithm));
        }
        catch (\Exception) {
            return null;
        }

        // Check that the class name is an instance of AuthenticationData.
        /** @var string $class */
        $class = $payload->class;

        if (!is_a($class, AuthenticationData::class, true)) {
            return null;
        }

        // Cast stdClass object to array so the ObjectToArraySerializer can unserialize it.
        $data = json_decode(json_encode($payload->data), true);

        if ($data === null) {
            return null;
        }

        /** @var AuthenticationData */
        return $this->serializer->unserialize($data, class: $class);
    }

    public function userId(string $token): mixed
    {
        return $this->data($token)?->getUserId();
    }
}
