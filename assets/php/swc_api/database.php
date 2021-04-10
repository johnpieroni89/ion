<?php
/* DATABASE CONNECT */

class database{
	private $host;
	private $username;
	private $password;
	private $database;
	public $connection;

	function connect() {
		$this->host = "localhost:3308"; //Enter the host name/IP address of the device with the database. Typically will be localhost.
		$this->database = "cryptoglyph_ion"; //Enter the name of the database
		$this->username = "root"; //Enter the username of the account that is accessing the database
		$this->password = "123Xep624!1234"; //Enter the password for the username that is accessing the database
		$this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);
	}

	function disconnect() {
		mysqli_close($this->connection);
	}
}

?>