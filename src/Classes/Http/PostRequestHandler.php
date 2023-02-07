<?php

namespace Artemiyov\Test\Classes\Http;

use Artemiyov\Test\Classes\Template;
use Artemiyov\Test\Classes\FormValidator;
use Artemiyov\Test\Classes\Models\User;

/**
 * Class for handling POST client requests
 */
class PostRequestHandler extends RequestHandler
{
  /**
   * Default JSON option for AJAX responses
   * @var int
   */
	private int $json_option = JSON_UNESCAPED_UNICODE;

  /**
   * Register routes and methods for processing
   * @var array|string[]
   */
	private array $route_handlers = [
		'registration' => 'registerUser',
		'login' => 'loginUser',
		'account/delete' => 'deleteUser',
	];

	public function process(?string $path = null): void
	{
		parent::process();
		// get handler for the current route
		$handler = null;
		foreach($this->route_handlers as $path => $method) {
			if (str_contains($this->getReferer(), $path)) {
				$handler = $method;
			}
		}

		if (! $handler) {
			$this->setHeader('code', 400)->sendHeaders();

			(new Template('layout'))
				->addTemplateBlock('content', 'error400')
				->addTemplateParam('message', 'Error 400: Bad request')
				->render();

				exit;
		}
				
		$this->$handler();
	}

  /**
   * Hanlder for POST registration data
   * @return void
   * @throws \Exception
   */
	public function registerUser()
	{
		$this
			->setHeader('Content-Type', 'application/json')
			->sendHeaders();

		if ($validation_error = FormValidator::validate($this->getRequestParams())) {
			echo json_encode(['error' => true, ...$validation_error], $this->json_option);
		} else {

			$user = new User($this->getRequestParams());
			if ($not_unique_fields = $user->getEqualFields()) {
				$errors_list = [];
				foreach($not_unique_fields as $field => $value) {
					$errors_list = ['field' => $field, 'message' => 'Придумайте уникальное значение']; 
					echo json_encode(['error' => true, ...$errors_list], $this->json_option);
					
					exit;	
				}
			}

			if ($user->save()) {
				echo json_encode(
					[
						'error'   => false,
						'message' => 'Поздравляем, вы удачно зарегистрированы!',
					],
					$this->json_option
				);	
			}
		}
	}

  /**
   * Handler for POST login data
   * @return void
   */
	public function loginUser()
	{
		$params = $this->getRequestParams();

		if ($validation_error = FormValidator::validate($params, false)) {
			echo json_encode(['error' => true, ...$validation_error], $this->json_option);
			exit;
		}

		// returns user instance or array with error
		$user = User::loadAfterValidation($params['login'], $params['password']);
		
		if (!($user instanceof User)) {
			echo json_encode(['error' => true, ...$user], $this->json_option);
			exit;
		}

		$this->rememberThroughSession('username', $user->getField('username'));
		echo json_encode(['error' => false, 'redirect' => '/account']);				
	}

	public function deleteUser()
	{

	}
}