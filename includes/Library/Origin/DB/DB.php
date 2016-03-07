<?php
namespace Origin\DB;

use \PDO;
use \PDOException;
use \Origin\Utilities\Settings;
use \Origin\Utilities\Types\Exception;

/*
* Example Call: DB::Get('connection_name_from_config')->Query($query, array('param' => ':param'));
*/
class DB extends \Origin\Utilities\Types\Singleton {
	private $error;
	private $statement;
	private $connection;
	private $connection_parameters;
	
	/*
	* Select back full result set as an array.
	*/
	public function Query($sql, array $parameters = null, $fetch_type = null){
		if($this->Execute($sql, $parameters)){
			return $this->statement->fetchAll(($fetch_type === null) ? PDO::FETCH_ASSOC : $fetch);
		}
	}

	/*
	* Select just the first row.
	*/
	public function QueryFirstRow($sql, array $parameters = null, $fetch_type = null){
		if($this->Execute($sql, $parameters)){
			return $this->statement->fetch(($fetch_type === null) ? PDO::FETCH_ASSOC : $fetch_type);
		}
	}

	/*
	* Select the first column.
	*/
	public function QueryFirstColumn($sql, array $parameters = null){
		if($this->Execute($sql, $parameters)){
			return $this->statement->fetchAll(PDO::FETCH_COLUMN);
		}
	}

	/*
	* Select the first column of the first row.
	*/
	public function QueryOne($sql, array $parameters = null){
		if($this->Execute($sql, $parameters)){
			return $this->statement->fetch(PDO::FETCH_COLUMN, 0);
		}
	}

	/*
	* Inserts a record into the database.
	*/
	public function Insert($table, array $parameters = null){
		$binds = array();
		$total_parameters = 1;
		foreach($parameters as $key => $value){
			if($value instanceof \DateTime){
				$value = $value->format('Y-m-d H:i:s');
			}

			$binds[sprintf(':parameter1%04d', $total_parameters)] = $value;
			$total_parameters++;
		}
		
		$query = sprintf(self::$insert_template, $table, implode(', ', array_keys($parameters)), implode(', ', array_keys($binds)));
		return $this->Execute($query, $binds);
	}
	
	public function LastID(){
		return $this->connection->lastInsertId();
	}

	/*
	* Updates a record in the database.
	*/
	public function Update($table, array $parameters, $where = null, array $where_parameters = null){
		$sql = null;
		$binds = array();
		$total_parameters = 1;
		foreach($parameters as $key => $value){
			$bind_key = sprintf(':parameter1%04d', $total_parameters);
			$sql .= (($sql === null) ? sprintf(self::$set_sql, $key, $bind_key) : ', '.sprintf(self::$set_sql, $key, $bind_key));
			$binds[$bind_key] = $value;
			$total_parameters++;
		}
		
		$query = sprintf(self::$update_template, $table, $sql);
		if($where !== null){
			$query .= sprintf(self::$update_where, $where);
			
			if($where_parameters !== null){
				$binds = array_merge($binds, $where_parameters);
			}
		}
		
		return $this->Execute($query, $binds);
	}

	/*
	* Lookup database connection information from settings file and setup connection.
	*/
	public function __construct($database_name = null){
		if($database_name === null){
			throw new Exception('Invalid database name passed. Please check the call and try again.');
		}

		$this->connection_parameters = Settings::Get('databases')->Values([$database_name]);
		if(!$this->Connect()){
			throw new Exception('Unable to connect to database: '.$database_name.' - '.$this->error);
		}
	}

	/*
	* Connect to the database server.
	*/
	private function Connect(){
		$dsn = sprintf('%s:host=%s;dbname=%s;port=%s',
			$this->connection_parameters->offsetGet('type'),
			$this->connection_parameters->offsetGet('hostname'),
			$this->connection_parameters->offsetGet('username'),
			$this->connection_parameters->offsetGet('port')
		);
		
		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		
		try {
			$this->connection = new PDO($dsn, $this->connection_parameters->offsetGet('username'), $this->connection_parameters->offsetGet('password'), $options);
			return true;
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}

	/*
	* Execute some SQL.
	*/
	private function Execute($sql, array $parameters = null){
		$this->statement = $this->connection->prepare($sql);
		if($parameters !== null){
			foreach($parameters as $key => $value){
				$this->statement->bindValue($key, $value, $this->GetType($value));
			}
		}

		return $this->statement->execute();
	}

	/*
	* Get type of value.
	*/
	private function GetType($value){
		switch (true) {
			case is_int($value):
				$type = PDO::PARAM_INT;
				break;
			case is_bool($value):
				$type = PDO::PARAM_BOOL;
				break;
			case is_null($value):
				$type = PDO::PARAM_NULL;
				break;
			default:
				$type = PDO::PARAM_STR;
				break;
		}

		return $type;
	}

	/*
	* SQL builder dump.
	*/
	private static $update_template = <<<'SQL'
UPDATE
	%s
SET
	%s
SQL;
	
	private static $insert_template = <<<'SQL'
INSERT INTO %s (
	%s
) VALUES (
	%s
)
SQL;

	private static $update_where = <<<'SQL'
 WHERE
	%s
SQL;

	private static $set_sql = <<<'SQL'
 %s = %s
SQL;
}