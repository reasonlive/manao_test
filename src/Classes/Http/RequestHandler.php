<?php

namespace Artemiyov\Test\Classes\Http;

use Artemiyov\Test\Classes\Template;

abstract class RequestHandler
{
  /**
   * HTTP method from request
   * @var string|mixed
   */
	protected string $method;

  /**
   * Client request Headers
   * @var array
   */
	private array $request_headers;

  /**
   * Server response Headers
   * @var array
   */
	protected array $response_headers;

  /**
   * GET or POST params
   * @var array|null
   */
	private ?array $request_params;

  /**
   * Template for Response representation
   * @var Template|null
   */
	protected ?Template $template;

  /**
   * @param Template|null $template
   */
	public function __construct(?Template $template = null)
	{
		$this->request_headers = [];

		foreach ($_SERVER as $server_var => $value) {
			if (preg_match('/^HTTP_/', $server_var)) {
				$this->request_headers[$server_var] = $value;
			}

			if ($server_var === 'REQUEST_METHOD') {
				$this->method = $value;
			}
		}

		$this->request_params = $this->method === 'GET'
			? ($_GET ?? null)
			: ($_POST ?? null);

		$this->template = $template;

		return $this;
	}

  /**
   * Gets request headers
   * @return array
   */
	protected function getRequestHeaders(): array
	{
		return $this->request_headers;
	}

  /**
   * URI referer
   * @return string
   */
	public function getReferer(): string
	{
		return $this->getRequestHeaders()['HTTP_REFERER'];
	}

  /**
   * GET or POST params
   * @return array|null
   */
	protected function getRequestParams(): array
	{
		return $this->request_params;
	}

  /**
   * Sets response header for the client
   * @param string $name
   * @param string $value
   * @return $this
   */
	protected function setHeader(string $name, string $value)
	{
		$this->response_headers[$name] = $value;
		return $this;
	}

  /**
   * Sends headers to the client
   * @return void
   */
	protected function sendHeaders()
	{
		if (! headers_sent()) {
			foreach ($this->response_headers as $header => $value) {
				if ($header === 'code') {
					http_response_code($value);
					continue;
				}

				header("$header: $value");
			}
		}
	}

  /**
   * Sets cookie
   * @param string $key
   * @param string $value
   * @param int $expire_days_count Amount of days, 1 by default
   * @return $this
   */
	public function setCookie(string $key, string $value, int $expire_days_count = 1)
	{
		$expire = time() + 60 * 60 * 24 * $expire_days_count;

		setcookie($key, $value, $expire, '/', '', false, true);
		return $this;
	}

  /**
   * Clears cookie
   * @param string|null $key is null, clears all cookie
   * @return $this
   */
	public function clearCookie(?string $key = null)
	{
		if ($key) {
			unset($_COOKIE[$key]);
		} else {
			$_COOKIE = [];
		}

		return $this;
	}

  /**
   * Set session, or start the session if it not has been started
   * @param string $key empty by default
   * @param string $value empty by default
   * @return $this
   */
	public function setSession(string $key = '', string $value = '')
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();			
		}

		if ($key) {
			$_SESSION[$key] = $value;			
		}

		return $this;
	}

  /**
   * Clear session
   * @param string|null $key if it null, clears all keys
   * @return $this
   */
	public function clearSession(?string $key = null)
	{
		if ($key) {
			unset($_SESSION[$key]);
		} else {
			session_unset();		
		}

		return $this;
	}

  /**
   * Sets key and value to the cookie and session at a time
   * @param string $key
   * @param string $value
   * @return $this
   */
	public function rememberThroughSession(string $key, string $value)
	{
		$this->setCookie($key, $value);
		$this->setSession($key, $value);

		return $this;
	}

  /**
   * Clears cookie and session keys at a time
   * @param string|null $key if it null, clears all keys
   * @return $this
   */
	public function forgetThroughSession(string $key = null)
	{
		$this->clearCookie($key);
		$this->clearSession($key);

		return $this;
	}

  /**
   * Main method that do the work
   * @return void
   */
	public function process(): void
	{
		$this->setSession();
	}
}