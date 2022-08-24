<?php

namespace Gooby\EzAuthClient;

class User
{
	private Auth $auth;
	public int $id;
	public ?string $username = null;
	public ?string $firstName = null;
	public ?string $lastName = null;
	public ?string $email = null;
	public ?string $role = null;

	/**
	 * Pass the decoded JWT to the constructor \
	 * Schema of the $payload object: \
	 * $payload->sub int The user's ID \
	 * $payload->username string The user's username \
	 * $payload->firstName string The user's first name \
	 * $payload->lastName string The user's last name \
	 * $payload->email string The user's email
	 * $payload->role string The user's role
	 * 
	 * @param object $payload (see above)
	 * @return void
	 */
	public function __construct(object $payload, Auth $auth)
	{
		$this->id = (int) $payload->sub;
		$this->auth = $auth;

		foreach (['username', 'firstName', 'lastName', 'email', 'role'] as $key) {
			if (isset($payload->$key) && gettype($payload->$key) === 'string') {
				$this->$key = (string) $payload->$key;
			}
		}
	}

	/**
	 * Checks if user has the given role
	 *
	 * @param string|array $roles string or an array of strings
	 * @return boolean
	 */
	public function hasRole(mixed $roles): bool
	{
		if (!isset($this->role)) {
			return false;
		}

		foreach ((array) $roles as $role) {
			if (strtolower($this->role) === strtolower($role)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if user has the given permission and redirects them to the Auth server if they don't
	 *
	 * @param string|array $roles string or an array of strings
	 * @return boolean
	 */
	public function hasRoleOrForbidden(mixed $roles): void
	{
		if (!$this->hasRole($roles)) {
			$this->auth->forbidden((array) $roles);
		}
	}
}