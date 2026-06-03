# medas-jwt-tokens

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

A JWT-based implementation of `AuthenticationTokenController` using `firebase/php-jwt`. Tokens are signed with a configurable secret key and algorithm (default `HS256`). The `AuthenticationData` payload is serialized to an array via `medas-object-to-array-serializer`, JSON-encoded with `medas-json`'s binary-safe encoder, and embedded in the JWT claims alongside the class name. Decoding reverses the process — the class is reconstructed via `ObjectToArraySerializer::unserialize()`.

Decoded token payloads are cached in the memory cache (keyed by SHA1 of the token string), so repeated calls to `data()` within a single request only decode the JWT once.

**Important:** JWT tokens cannot be invalidated server-side — `invalidate()` is a no-op. Use `medas-api-keys`'s `NamedTokenManager` instead if you need revocable tokens.

## Configuration options

| Option                 | Default         | Description                                   |
|------------------------|-----------------|-----------------------------------------------|
| `jwt-tokens.key`       | none (required) | Secret key used to sign and verify tokens     |
| `jwt-tokens.algorithm` | `HS256`         | JWT signing algorithm (e.g. `HS256`, `RS256`) |

## Usage

### Package developer context

Register the package and configure `JwtAuthTokenController` as the `AuthenticationTokenController` implementation:

```php
use Medas\JwtTokens\JwtTokensPackage;

JwtTokensPackage::instance();
```

Since `JwtAuthTokenController` implements `AuthenticationTokenController`, it is resolved automatically by the DI container whenever that interface is injected — no manual binding needed.

**Configuration:**

```yaml
jwt-tokens:
  key: $env(JWT_SECRET_KEY)
  algorithm: HS256
```

```
JWT_SECRET_KEY=your-long-random-secret-here
```

**Creating a token:**

```php
use Medas\Core\Interfaces\AuthenticationTokenController;
use Medas\Users\RestControllers\Logins\AuthenticationData;

$data = new AuthenticationData(userId: $user->id());
$token = $tokenController->create($data);
// Returns a signed JWT string
```

**Decoding a token:**

```php
$data = $tokenController->data($token);
// Returns the AuthenticationData object, or null if invalid/expired
```

**Adding custom claims** — populate `AuthenticationData::$additionalData` before creating the token; it is serialized into the JWT payload and available after decoding:

```php
$data = new AuthenticationData($user->id());
$data->additionalData['roles'] = ['admin', 'editor'];

$token = $tokenController->create($data);

// On a later request:
$decoded = $tokenController->data($token);
$roles = $decoded->additionalData['roles'];
```

### Backend user context

**Token expiry** — `JwtAuthTokenController` does not add an `exp` claim by default. If you need expiry, add it to `additionalData` and verify it manually in an `#[EventListener]` on `AuthorizationVote`, or subclass `JwtAuthTokenController`.

**Key rotation** — changing `jwt-tokens.key` invalidates all existing tokens immediately (they will fail verification). Plan rotation carefully or implement a transition period by accepting both the old and new key.

**No revocation** — once issued, a JWT is valid until it expires (or forever, if no expiry is set). For sessions that must be revocable (e.g., logout), use `medas-api-keys`'s `NamedTokenManager` instead.
