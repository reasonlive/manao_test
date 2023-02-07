<?php

namespace Artemiyov\Test\Classes\Models;

use Artemiyov\Test\Classes\Model;
use Artemiyov\Test\Classes\Database;

class User extends Model
{
  /**
   * Tablename
   * @var string
   */
	protected static string $table = 'users';

  /**
   * Users fields
   * @var array|string[]
   */
	protected array $fields = [
		'username',
		'email',
		'login',
		'password',
		'confirm_password',
	];

  /**
   * Unique fields
   * @var array|string[]
   */
	protected array $unique_fields = [
		'email',
		'login',
	];

  /**
   * Fields getter
   * @param string $field
   * @return mixed|string
   */
	public function getField(string $field): string
	{
		return $this->fields[$field];
	}

  /**
   * Gets all fields
   * @return array|string[]
   */
	public function getFields(): array
	{
		return $this->fields;
	}

  /**
   * First load object and check that provided password is correct
   * @param string $login
   * @param string $password
   * @return array<string , string>|User User object or array with error [field, message]
   */
	public static function loadAfterValidation(string $login, string $password): mixed
	{
		if ($user = self::loadByLogin($login)) {
			$hash = sha1($password . $user->getField('salt'));

			if ($user->getField('password') !== $hash) {
				return ['field' => 'password', 'message' => 'Неверный пароль'];
			}

			return $user;				
		} else {
			return ['field' => 'login', 'message' => 'Такой логин не существует'];
		}
	}

  /**
   * Hashes password through sha1 with salt
   * Immediately writes password and salt into the fields
   * @param string $password
   * @return void
   * @throws \Exception
   */
	public function hashPassword(string $password): void
	{
		if ($this->new) {
			unset($this->fields['confirm_password']);
			$this->fields['salt'] = bin2hex(random_bytes(10));
		}

		$this->fields['password'] = sha1($password . $this->fields['salt']);
	}

  /**
   * Hashes password and saves into database
   * @return bool
   * @throws \Exception
   */
	public function save(): bool
	{
		$this->hashPassword($this->fields['password']);
		return parent::save();
	}

  /**
   * Loads object by login
   * @param string $value Login
   * @param string $field
   * @return User|null
   * @throws \Exception
   */
	public static function loadByLogin(string $value, string $field = 'login'): ?User
	{
		return parent::load($value, $field);
	}

  /**
   * Loads object by email
   * @param string $value Email
   * @param string $field
   * @return User|null
   * @throws \Exception
   */
	public static function loadByEmail(string $value, string $field = 'email'): ?User
	{
		return parent::load($value, $field);
	}
}