<?php

namespace Artemiyov\Test\Classes;

use Artemiyov\Test\Classes\Database;

abstract class Model
{
  /**
   * Database instance
   * @var \Artemiyov\Test\Classes\Database
   */
	private Database $db;

  /**
   * Table name for Model
   * @var string
   */
	protected static string $table = '';

  /**
   * Fields for model
   * @var array|int[]|string[]
   */
	protected array $fields = [];

  /**
   * Fields with unique key
   * @var array|int[]|string[]
   */
	protected array $unique_fields = [];

  /**
   * New instance or from database
   * @var bool
   */
	protected bool $new = false;

  /**
   * Creates the Model object
   * @param array $fields
   * @param bool $new
   * @throws \Exception
   */
	public function __construct(array $fields, bool $new = true) {

		$this->new = $new;
		$this->db = Database::getInstance();

    //object from database
		if (! $new) {
			$this->fields = $fields;
			return $this;
		}

    // convert array list to associative array
		$this->fields = array_flip($this->fields);
		$this->unique_fields = array_flip($this->unique_fields);

		foreach ($fields as $field => $value) {
			if (! array_key_exists($field, $this->fields)) {
				throw new \Exception("$field is not exist in the schema");
			}

			$this->fields[$field] = $value;

			if (array_key_exists($field, $this->unique_fields)) {
				$this->unique_fields[$field] = $value;
			}
		}
	}

  /**
   * Checks Unique fields, Gets equal fields for display errors
   * @return array|null Null if fields are Unique
   * @throws \Exception
   */
	public function getEqualFields(): ?array
	{
		$all_records = $this->db->getTableData(static::$table);

		foreach ($all_records as $record_fields) {
			$equal_fields = array_intersect_assoc($this->unique_fields, $record_fields);
			
			if (count($equal_fields)) {
				return $equal_fields;
			}
		}

		return null;
	}

  /**
   * Saves object to database
   * @return bool true if OK
   * @throws \Exception
   */
	public function save(): bool
	{
		if ($this->getEqualFields($this->unique_fields)) {
			throw new \Exception('Database writestream error: table has unique fields');			
		}

		if ($this->new) {
			$this->db->addOne($this->fields);
			$this->new = false;
		} else {
			// $this->db->updateOne($this->fields);
		}

		$this->db->write(static::$table);
		return true;
	}

  /**
   * Updates object from database by Id
   * @param int $id Object id
   * @param array $new_fields
   * @return void
   * @throws \Exception
   */
	public function update(int $id, array $new_fields): void
	{
		foreach($this->db->getTableData(static::$table) as $key => $fields) {
			if ($key === $id) {
				$this->db->updateOne($id, $new_fields);
			}
		}
	}

  /**
   * Deletes object from database by id
   * @param int $id
   * @return void
   * @throws \Exception
   */
	public function delete(int $id): void
	{
		foreach($this->db->getTableData(static::$table) as $key => $fields) {
			if ($key === $id) {
				$this->db->deleteOne($id);
			}
		}	
	}

  /**
   * Load object from database
   * @param mixed $value
   * @param string|null $field if field not provided, value must be Id
   * @return Model|null Object from database or null
   * @throws \Exception
   */
	public static function load(mixed $value, ?string $field = null): ?Model
	{
		$data = []; // data from database about one record

		$db = Database::getInstance();
		foreach($db->getTableData(static::$table) as $id => $fields) {
			if(!$field && $value === $id) {
				$data = $db->readOne($id);
			}

			if ($field) {
				foreach($fields as $field_name => $field_value) {
					if ($field_name === $field && $field_value === $value) {
						$data = $db->readOne($id);
					}
				}
			}
		}

		if (count($data)) {
			return new static(reset($data), false);
		}

		return null;
	}
}