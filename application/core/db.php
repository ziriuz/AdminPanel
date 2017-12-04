<?php
class Database {
	private $db;

	public function __construct($driver, $hostname, $username, $password, $database, $port = NULL) {
		$class = 'DB\\' . $driver;

		if (class_exists($class)) {
			$this->db = new $class($hostname, $username, $password, $database, $port);
		} else {
			exit('Error: Could not load database driver ' . $driver . '!');
		}
	}

	public function query($sql,$mode='assoc') {
		return $this->db->query($sql,$mode);
	}

	public function escape($value) {
		return $this->db->escape($value);
	}

	public function countAffected() {
		return $this->db->countAffected();
	}

	public function getLastId() {
		return $this->db->getLastId();
	}
}
class NoDataFoundException extends Exception { };
class TooManyRowsException extends Exception { };
class SqlException extends Exception { 
    protected $statement;	
	function __construct( $message, $code=0, $stmt = null)
	{
		$this->statement = $stmt;		
		parent::__construct($message, $code);
	}
	function getSql() { return $this->statement; }
};
