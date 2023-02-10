<?php

namespace Artemiyov\Test\Classes;

class Template
{
  /**
   * Full filepath of Main template (layout)
   * @var string|null
   */
	private ?string $template;

	private string $template_root;

	private array $data = [];

  /**
   * Creates Template object with main template
   * @param string|null $template_name main template (layout)
   */
	public function __construct(string $template_name = null)
	{
		$this->template_root = getcwd() . '/src/templates/';

		$this->template = $template_name ? $this->findFile($template_name) : null;

		return $this;
	}

  /**
   * Adds blocks to layout
   * @param string $block_name name of layout variable
   * @param string $template_name block filename
   * @return $this
   * @throws \Exception
   */
	public function addTemplateBlock(string $block_name, string $template_name)
	{
		if ($block_path = $this->findFile($template_name)) {
			$this->data[$block_name] = $block_path;
		} else {
			throw new \Exception('Unknown template');
		}

		return $this;
	}

  /**
   * Adds params as a string to render in template
   * @param string $param_name Template variable
   * @param string $value String to be rendered as a variable
   * @return $this
   */
	public function addTemplateParam(string $param_name, string $value)
  {
		$this->data[$param_name] = $value;
		return $this;
	}

  /**
   * Render the template by require method
   * @return void
   */
	public function render(): void
  {
		$data = $this->data;
		
		if ($this->template) {
			require $this->template;		
		} else {
			require $this->template_root . 'html.php';
		}
	}

  /**
   * Finds file in template directories by filename
   * @param string $name filename without extension
   * @param string|null $dir template directory
   * @return string|null - Full filepath or null
   */
	public function findFile(string $name, ?string $dir = null): ?string
	{
		if (! $dir) {
			$dir = $this->template_root;
		}
	
		foreach (scandir($dir) as $filename) {
			if (! preg_match('/^\.+$/', $filename)) {
					if (preg_match("/$name/", $filename)) {
						$found = $dir . $filename;
					}

					if (is_dir($dir . $filename)) {
						$found = $this->findFile($name, $dir . $filename . '/');
					}
			}
		}

		return $found ?? null;
	}
}