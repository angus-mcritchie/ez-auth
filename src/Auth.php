<?php

namespace Gooby\EzAuthClient;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
	protected string $tokenCookieName = 'ez_auth_token';
	protected string $algorithm = 'HS256';
	protected ?string $server = null;
	protected ?string $secret = null;

	/**
	 *  $config['secret'] string The secret to decode the JWT - defaults to getenv('EZ_AUTH_CLIENT_SECRET')
	 *  $config['secret'] string The EZ Auth Server's URL e.g. https://auth.example.com - defaults to getenv('EZ_AUTH_CLIENT_SERVER')
	 * 
	 *  @throws InvalidSecretArgumentException if the secret is invalid
	 *  @throws InvalidServerArgumentException if the server is invalid
	 * 
	 *  @param ?array $config (see above)
	 */
	public function __construct(?array $config = [])
	{
		$this->secret = isset($config['secret']) ? $config['secret'] : getenv('EZ_AUTH_CLIENT_SECRET');
		$this->server = isset($config['server']) ? $config['server'] : getenv('EZ_AUTH_CLIENT_SERVER');

		$this->assertValidSecret();
		$this->assertValidServer();
	}


	/**
	 * Redirects the request to the EZ Auth Server to logout
	 *
	 * @return void
	 */
	public function logout(): void
	{
		header("Location: {$this->server}/logout");
		exit;
	}


	/**
	 * Redirects the request to the EZ Auth Server to login
	 * 
	 * @param string $redirectTo The URL to redirect to after login - defaults to the current URL
	 * @return void
	 */
	public function login(?string $redirectTo = null)
	{
		header("Location: {$this->server}/login?redirectTo=" . urlencode($redirectTo ?? $this->getCurrentUrl()));
		exit;
	}


	/**
	 * Gets the authenticated User
	 *
	 * @throws JwtDecodeException if the token is invalid
	 * @return ?User
	 */
	public function getUser(): ?User
	{
		if ($this->user === null) {
			$payload = $this->getTokenPayload();

			if ($payload) {
				$this->user = new User($payload);
			}
		}

		return $this->user;
	}


	/**
	 * Check if the user is authenticated
	 * 
	 * @throws JwtDecodeException if the token is invalid
	 * @return bool
	 */
	public function isAuthenticated(): bool
	{
		return $this->getUser() !== null;
	}


	/**
	 * Gets the token from the cookie
	 *
	 * @return string|null
	 */
	private function getToken(): ?string
	{
		return isset($_COOKIE[$this->tokenCookieName]) ? $_COOKIE[$this->tokenCookieName] : null;
	}


	/**
	 * Gets the token and attempts to decode it
	 *
	 * @return object|null
	 */
	private function getTokenPayload(): ?object
	{
		$token = $this->getToken();

		if (!$token) {
			return null;
		}

		return $this->decodeToken($token);
	}


	/**
	 * Decodes the token with Firebase\JWT\JWT::decode and returns the payload
	 *
	 * @param string $token
	 * @return object
	 */
	private function decodeToken(string $token): object
	{
		try {
			return JWT::decode($token, new Key($this->secret, $this->algorithm));
		} catch (\Exception $e) {
			throw new JwtDecodeException($e->getMessage());
		}
	}


	/**
	 * Generates the current URL from PHP's $_SERVER superglobal
	 *
	 * @return string|null
	 */
	private function getCurrentUrl(): ?string
	{
		if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['REQUEST_URI'])) {
			return null;
		}

		return "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	}


	/**
	 * Checks to see if the server is a valid asset('img/photo.jpg')
	 *
	 * @throws InvalidServerArgumentException if the server is invalid
	 * @return void
	 */
	public function assertValidServer(): void
	{
		if (!$this->server) {
			throw new InvalidServerArgumentException('You must provide a EZ Auth Server URL with environment variable EZ_AUTH_CLIENT_SERVER or pass it in the constructor.');
		}

		if (gettype($this->server) !== 'string') {
			throw new InvalidServerArgumentException('Server must be a string');
		}

		if (!filter_var($this->server, FILTER_VALIDATE_URL)) {
			throw new InvalidServerArgumentException('Server must be a valid URL');
		}
	}


	/**
	 * Checks to see if the secret is valid, but doesn't actually check if the secret is correct
	 *
	 * @throws InvalidSecretArgumentException if the secret is invalid
	 * @return void
	 */
	public function assertValidSecret(): void
	{
		if (!$this->secret) {
			throw new InvalidSecretArgumentException('No secret provided');
		}

		if (gettype($this->secret) !== 'string') {
			throw new InvalidSecretArgumentException('Secret must be a string');
		}
	}
}
