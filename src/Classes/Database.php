<?php

namespace Artemiyov\Test\Classes;

class Database
{
  /**
   * Base path to database files
   * @var string
   */
	private string $data_base_path;

  /**
   * Data from database tables
   * @var array
   */
	private array $data = [];

  /**
   * Self instance
   * @var Database
   */
	private static Database $db;

  /**
   * Id for first table record
   * @var int
   */
	private int $increment_start = 1;

	private function __construct()
	{
		$this->data_base_path = getcwd() . '/src/data/';
	}

  /**
   * @return static Database instance
   */
	public static function getInstance(): self
	{
		if (empty(self::$db)) {
			self::$db = new self();
		}

		return self::$db;
	}

  /**
   * @param string $table_name
   * @return bool
   * @throws \Exception
   */
	public function createTable(string $table_name): bool
	{
		$path = $this->data_base_path . $table_name . '.json';

		if (file_exists($path)) {
			return false;
		}

		$resource = fopen($path, 'w');
		$result = fwrite($resource, '{}');
		fclose($resource);

		if ($result === false) {
			throw new \Exception('Database file was not be written');
		}

		return true;
	}

  /**
   * All table data from json file
   * @param string $table_name
   * @return array
   * @throws \Exception
   */
	public function getTableData(string $table_name): array
	{
		if (count($this->data)) {
			return $this->data;
		}

		$table_path = $this->data_base_path . $table_name . '.json';

		if (! file_exists($table_path)) {
			$this->createTable($table_name);	
		}

		if ($json = file_get_contents($table_path)) {
			$this->data = json_decode($json, true);
		} else {
			$this->data = [];
		}

		return $this->data;
	}

  /**
   * Adds one record to json file
   * @param array $fields
   * @return void
   */
	public function addOne(array $fields)
	{
		if (count($this->data)) {
			$this->data[] = $fields;	
		} else {
			$this->data = [$this->increment_start => $fields];
		}
	}

  /**
   * Reads one record from json file
   * @param int $id
   * @return array|null
   */
	public function readOne(int $id): ?array
	{
		if ($result = $this->data[$id] ?? null) {
			return [$id => $result];
		}

		return null;
	}

  /**
   * Updates one record in json file
   * @param int $id
   * @param array $data
   * @return void
   */
	public function updateOne(int $id, array $data)
	{
		$this->data[$id] = $data;
	}

  /**
   * Deletes one record from json file
   * @param int $id
   * @return void
   */
	public function deleteOne(int $id)
	{
		unset($this->data[$id]);
	}

  /**
   * Writes all changes to json file
   * @param string $table_name
   * @param string|null $data - Data for whole rewriting
   * @return void
   * @throws \Exception
   */
	public function write(string $table_name, ?string $data = null): void
	{
		if ($data) {
			$this->data = $data;
		}
		
		$json = json_encode($this->data, JSON_PRETTY_PRINT);

		if (! file_put_contents($this->data_base_path . $table_name . '.json', $json)) {
			throw new \Exception('Database writestream error: Data was not be written to database');
		}	
	}

  /**
   * Deletes table as json file from file system
   * @param string $table_name
   * @return bool
   */
	public function deleteTable(string $table_name): bool
	{
		$path = $this->data_base_path . $table_name . '.json';

		if (file_exists($path)) {
			unlink($path);
			return true;			
		}

		return false; 
	}
}