<?php

namespace Artemiyov\Test\Classes;

use Artemiyov\Test\Classes\Http\RequestHandler;
use Artemiyov\Test\Classes\Http\GetRequestHandler;
use Artemiyov\Test\Classes\Http\PostRequestHandler;
use Artemiyov\Test\Classes\Template;

class Router
{
  /**
   * Reads client requests with provided uri path
   * @param string $path URI
   * @param RequestHandler $handler Object that do the work
   * @return void
   */
	private static function readRequest(string $path, RequestHandler $handler): void
	{
		$handler->process($path);
	}

  /**
   * Reads GET requests
   * @param string $path URI
   * @param \Artemiyov\Test\Classes\Template|null $template Representation
   * @return void
   */
	public static function get(string $path, ?Template $template = null)
	{
		self::readRequest($path, new GetRequestHandler($template));
	}

  /**
   * Reads POST requests
   * @param string $path URI
   * @param \Artemiyov\Test\Classes\Template|null $template Representation
   * @return void
   */
	public static function post(string $path, ?Template $template = null)
	{
		self::readRequest($path, new PostRequestHandler($template));
	}

  /**
   * Main request receiver
   * @return void
   */
	public static function watch(): void
	{
		['REQUEST_METHOD' => $method, 'REQUEST_URI' => $url] = $_SERVER;

		$template = new Template('layout');

		switch ($method) {
			case 'GET': self::get($url, $template);
			break;

			case 'POST': self::post($url);
			break;

			default: self::get('/error', $template);
		}
	}
}