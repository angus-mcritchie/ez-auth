EZ Auth Client
=
Used along side a EZ Auth Server to allow SSO across multiple subdomains sharing a [TLD](https://developer.mozilla.org/en-US/docs/Glossary/TLD) using [JWT](https://jwt.io/)s.

Installation
-

Use composer to manage your dependencies and download EZ Auth Client:

```bash
composer require gooby/ez-auth-client
```

Setup
---
We recommend adding these to your .env file, otherwise, you can pass these settings in the Auth constructor.
```
EZ_AUTH_CLIENT_SECRET="my-shared-secret-here"
EZ_AUTH_CLIENT_SERVER="https://auth.my-ez-auth-server.com"
```

Example
-------
```php
use Gooby\EzAuthClient\Auth;
use Gooby\EzAuthClient\User;
use Gooby\EzAuthClient\JwtDecodeException;

$client = new Auth();
$user = null;

try {
    $user = $client->getUser();
} catch (JwtDecodeException $e) {
    // Invalid token, or bad secret
    // log message, etc
}

if( ! $client->isAuthenticated() ) {
    $client->login();
}

// user is logged in
$userId = $user->id();