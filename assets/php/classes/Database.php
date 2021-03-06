<?php
/* DATABASE CONNECT */

class Database {
    public $host;
    public $port;
    private $username;
    private $password;
    private $database;
	public $connection;
	
	function __construct() {
	    $this->host = MYSQL_DB_HOST;
	    $this->port = MYSQL_DB_PORT;
	    $this->database = MYSQL_DB_DATABASE;
	    $this->username = MYSQL_DB_USERNAME;
	    $this->password = MYSQL_DB_PASSWORD;
	}

	function connect() {
		$this->connection = mysqli_connect($this->host.":".$this->port, $this->username, $this->password, $this->database);
	}

	function disconnect() {
		mysqli_close($this->connection);
	}
}

?>