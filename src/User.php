<?php

namespace Gooby\EzAuthClient;

class User
{
	public int $id;
	public ?string $username = null;
	public ?string $firstName = null;
	public ?string $lastName = null;
	public ?string $email = null;

	/**
	 * Pass the decoded JWT to the constructor \
	 * Schema of the $payload object: \
	 * $payload->sub int The user's ID \
	 * $payload->username string The user's username \
	 * $payload->firstName string The user's first name \
	 * $payload->lastName string The user's last name \
	 * $payload->email string The user's email
	 * 
	 * @param object $payload (see above)
	 * @return void
	 */
	public function __construct(object $payload)
	{
		$this->id = (int) $payload->sub;
		$optional = ['username', 'firstName', 'lastName', 'email'];

		foreach ($optional as $key) {
			if (isset($payload->$key)) {
				$this->$key = (string) $payload->$key;
			}
		}
	}
}