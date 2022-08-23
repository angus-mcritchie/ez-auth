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

Quick Start
-------
```php
<?php

$auth = new Gooby\EzAuthClient\Auth();
$user = $auth->getUserOrLogin();

echo "You're logged in and your ID is {$user->id}";
```

Example
-------
```php
<?php

use Gooby\EzAuthClient\Auth;
use Gooby\EzAuthClient\JwtDecodeException;

$client = new Auth();

try {

    $user = $client->getUser();

} catch (JwtDecodeException $e) {

    // Invalid token, or bad secret
    MyApp::logFailedLoginAttempt('Invalid Token: ' . $e->getMessage());

    // Redirect request to login page
    $client->login();
}

echo "You're logged in and your ID is {$user->id}";