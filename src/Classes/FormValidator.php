<?php

namespace Artemiyov\Test\Classes;

class FormValidator
{
  /**
   * Rules for input fields from client side
   * @var array|array[]
   */
	private array $rules = [
		'username' => ['pattern' => '/^[a-zа-я]{2,50}$/iu', 'alias' => 'Имя'],
		'login'    => ['pattern' => '/^[a-zа-я\-\_@0-9]{6,50}$/iu', 'alias' => 'Логин'],
		'password' => ['pattern' => '/^[a-zа-я0-9]{6,200}$/iu', 'alias' => 'Пароль'],
		'email'    => ['pattern' => '/^[a-z0-9]{2,200}@[a-z]{2,15}\.[a-z]{2,15}$/', 'alias' => 'Почта'],
	];

  /**
   * Checks all fields by the rules above
   * @param string $fieldname
   * @param mixed $value
   * @return array<string, string>|null Error [field, message]
   */
	public function checkFieldValue(string $fieldname, $value): ?array
	{
		if (array_key_exists($fieldname, $this->rules)) {
			if (! preg_match($this->rules[$fieldname]['pattern'], $value)) {
				return [
          'error'   => true,
					'field'   => $fieldname,
					'message' => "Поле {$this->rules[$fieldname]["alias"]} не прошло валидацию"
				];
			}
		}

		return null;
	}

  /**
   * Asserts two passwords are the same
   * @param string $password
   * @param string $confirm_password
   * @return array<string, string>|null Error [field, message]
   */
	public function assertPassword(string $password, string $confirm_password): ?array
	{
		if ($password !== $confirm_password) {
			return [
          'error'   => true,
          'field'   => 'confirm_password',
          'message' => "Пароли не совпадают"
      ];
		}

		return null;
	}

  /**
   * Validates all in one place
   * @param array $post_params Form fields
   * @param bool $registered Registration form or Login form
   * @return array<string ,string>|null One Error [field, message] or null if everything fine
   */
	public static function validate(array $post_params, bool $registered = true): ?array
	{
		$instance = new self();

		foreach($post_params as $field => $value) {
			if ($error = $instance->checkFieldValue($field, $value)) {
				return $error;
			}
		}

		if ($registered) {
			['password' => $password, 'confirm_password' => $password2] = $post_params;
			
			if ($error = $instance->assertPassword($password, $password2)) {
				return $error;
			}	
		}

		return null;
	}
}