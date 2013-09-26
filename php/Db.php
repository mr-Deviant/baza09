<?php
class Db {
	// Local site settings
	private $dbEngine   = 'mysql'; 
	private $dbHost     = 'localhost';
	private $dbDatabase = 'base09';
	private $dbUser     = 'root';
	private $dbPassword = '';
	protected $dbPrefix = 'base09_';

	protected $db;

	public function __construct() {
		// Live site settings
		if ($_SERVER['HTTP_HOST'] == 'baza09.com.ua') {
			$this->dbDatabase = 'deviant_baza09';
			$this->dbUser     = 'deviant_baza09';
			$this->dbPassword = 'j7Jgd9Jd';
		}

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