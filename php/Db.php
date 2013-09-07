<?php
class Db {
	private $dbEngine   = 'mysql'; 
	private $dbHost     = 'localhost';
	private $dbDatabase = 'base09';
	private $dbUser     = 'root';
	private $dbPassword = '';
	protected $dbPrefix = 'base09_';

	protected $db;

	public function __construct() {
		try {
		    $this->db = new PDO(
				$this->dbEngine . ':dbname=' . $this->dbDatabase . ';host=' . $this->dbHost,
				$this->dbUser,
				$this->dbPassword,
				array(
				    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
				)
			);
		} catch (PDOException $e) {
		    echo 'Connection failed: ' . $e->getMessage() . '<br>';
		    var_dump($e->getTrace());
			exit;
		}
	}
}
?>