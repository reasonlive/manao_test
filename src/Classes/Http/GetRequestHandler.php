<?php

namespace Artemiyov\Test\Classes\Http;

use Artemiyov\Test\Classes\Template;

/**
 * Class for handling GET client requests
 */
class GetRequestHandler extends RequestHandler
{
  /**
   * Register all known routes with representation options
   * @var array
   */
	private array $content_list = [
		'/'             => ['block' => 'main', 'title' => 'Main page'],
		'/registration' => ['block' => 'registration-form', 'title' => 'Registration'],
		'/login'        => ['block' => 'login-form', 'title' => 'Login'],
		'/account'      => ['block' => 'user-account', 'title' => 'Account', 'authenticate'],
		'/error'        => ['block' => 'error400', 'title' => 'Error'],
		'/logout'       => ['redirect' => '/', ],  
	];

  /**
   * Main process method
   * @param string|null $path URI
   * @return void
   * @throws \Exception
   */
	public function process(?string $path = null): void
	{
		parent::process();

		$template = $this->template ?: new Template();

		if ($params = $this->content_list[$path] ?? null) {

      // if user should be authenticated
			if (in_array('authenticate', $params) && !($_SESSION['username'] ?? false)) {
				(new Template('layout'))->addTemplateBlock('content', 'error401')->render();
				exit;
			}

      // if route has redirect option
			if($params['redirect'] ?? false) {
				if (!$params['session']) {
					$this->forgetThroughSession();
				}

				$this->setHeader('Location', $params['redirect'])->sendHeaders();
				exit;
			}

			$this->setHeader('code', 200)->sendHeaders();

			$template
				->addTemplateBlock('content', $params['block'])
				->addTemplateParam('title', $params['title'])
				->render();

		} else { // if route is not registered in the app
			$this->setHeader('code', 404)->sendHeaders();

			$template
				->addTemplateBlock('content', 'error404')
				->render();
		}
	}
}