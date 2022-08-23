<?php

namespace Gooby\EzAuthClient;

class User
{
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
	public function __construct(object $payload)
	{
		$this->id = (int) $payload->sub;

		foreach (['username', 'firstName', 'lastName', 'email', 'role'] as $key) {
			if (isset($payload->$key) && gettype($payload->$key) === 'string') {
				$this->$key = (string) $payload->$key;
			}
		}
	}

	public function roleIsOneOf(array $roles): bool
	{
		foreach ($roles as $role) {
			if ($this->roleIs($role)) {
				return true;
			}
		}

		return false;
	}

	public function roleIs(string $role): bool
	{
		if (!isset($this->role)) {
			return false;
		}

		return strtolower($this->role) === strtolower($role);
	}
}