<?php

namespace EzAuthClient;

class User
{
	protected int $id;
	protected ?string $username = null;
	protected ?string $firstName = null;
	protected ?string $lastName = null;
	protected ?string $email = null;

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

		if (isset($payload->username)) {
			$this->username = (string) $payload->username;
		}

		if (isset($payload->firstName)) {
			$this->firstName = (string) $payload->firstName;
		}

		if (isset($payload->lastName)) {
			$this->lastName = (string) $payload->lastName;
		}

		if (isset($payload->email)) {
			$this->email = (string) $payload->email;
		}
	}

	public function id(): int
	{
		return $this->id;
	}

	public function username(): ?string
	{
		return $this->username;
	}

	public function firstName(): ?string
	{
		return $this->firstName;
	}

	public function lastName(): ?string
	{
		return $this->lastName;
	}

	public function email(): ?string
	{
		return $this->email;
	}

	public function toJson(): string
	{
		return json_encode($this);
	}
}