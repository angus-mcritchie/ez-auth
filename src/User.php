<?php

namespace Gooby\EzAuthClient;

class User
{
	private Auth $auth;
	private object $payload;
	public int $id;
	public string $role = 'default';

	/**
	 * 
	 * @param object $payload raw payload of the JWT
	 * @param Auth $auth
	 * @return void
	 */
	public function __construct(object $payload, Auth $auth)
	{
		$this->auth = $auth;
		$this->payload = $payload;
		$this->id = (int) $payload->sub;
		$this->role = $payload->role ?? 'default';
	}

	public function __get(string $key)
	{
		return isset($this->payload->$key) ? $this->payload->$key : null;
	}

	/**
	 * Checks if user's role is in the provided array
	 *
	 * @param array $roles array of strings
	 * @return boolean
	 */
	public function hasRole(array $roles): bool
	{
		if (!isset($this->role)) {
			return false;
		}

		foreach ($roles as $role) {
			if (strtolower($this->role) === strtolower($role)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if user has the given permission and redirects them to the Auth server if they don't
	 *
	 * @param array $roles array of strings
	 * @return boolean
	 */
	public function hasRoleOrForbidden(array $roles): void
	{
		if (!$this->hasRole($roles)) {
			$this->auth->forbidden($roles);
		}
	}
}