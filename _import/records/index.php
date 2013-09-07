<?php
require '../php/Db.php';

class DbImport extends Db {
	private $importFile = 'db.txt';

	private $handle;

	public function __construct() {
		error_reporting(E_ALL);
		set_time_limit(0);
		ini_set('memory_limit', '128M');

		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';

		parent::__construct();

		$this->handle = fopen($this->importFile, 'r');
	}

	public function __destruct() {
		fclose($this->handle);
	}

	public function execute() {

		$importedCnt = 0;
		$skipedCnt = 0;

		if ($this->handle) {

			// Delete previous data from tables
			// $tableNames = array(
			// 	$this->dbPrefix . 'persons',
			// 	$this->dbPrefix . 'phones',
			// 	$this->dbPrefix . 'houses',
			// 	$this->dbPrefix . 'streets',
			// 	$this->dbPrefix . 'locations',
			// 	$this->dbPrefix . 'cities',
			// 	$this->dbPrefix . 'countries'
			// );

			// foreach ($tableNames as $tableName) {
			// 	try {
			// 		$this->db->exec('DELETE FROM ' . $tableName);
			// 		$this->db->exec('ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 1');
			// 	} catch (PDOException $e) {
			// 		echo 'Error: ' . $e->getMessage() . '<br>';
			// 		var_dump($e->getTrace());
			// 		exit;
			// 	}
			// }

			// Insert country
			try {
				$sth = $this->db->prepare('
					SELECT `id`
					FROM `' . $this->dbPrefix . 'countries`
					WHERE `id` = ?
					LIMIT 0, 1
				');

				$sth->execute(array(1));

				$country = $sth->fetch(PDO::FETCH_OBJ);

				if (!$country) {
					$this->db->exec('
						INSERT INTO `' . $this->dbPrefix . 'countries`
						SET `id` = 1,
							`name` = "Украина",
							`code` = "380"
					');
				}
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				var_dump($e->getTrace());
				exit;
			}
			

			// Insert city
			try {
				$sth = $this->db->prepare('
					SELECT `id`
					FROM `' . $this->dbPrefix . 'cities`
					WHERE `id` = ?
					LIMIT 0, 1
				');

				$sth->execute(array(1));

				$city = $sth->fetch(PDO::FETCH_OBJ);
				
				if (!$city) {
					$this->db->exec('
						INSERT INTO `' . $this->dbPrefix . 'cities`
						SET `id` = 1,
							`country_id` = 1,
							`name` = "Киев",
							`code` = "44"
					');
				}
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage() . '<br>';
				var_dump($e->getTrace());
				exit;
			}

		    while (($record = fgets($this->handle, 4096)) !== false) {
				$record = explode("\t", $record);

				/*
				[0]=> "Телефон"
				[1]=> "Фамилия"
				[2]=> "Имя"
				[3]=> "Отчество"
				[4]=> "Нас. пункт"
				[5]=> "Улица"
				[6]=> "Дом"
				[7]=> "Корпус"
				[8]=> "Квартира"
				[9]=> "Буква квартиры"
				*/

				// Trim values
				$record = array_map('trim', $record);

				preg_match("/(.*?)\s+(ул\.|пр\-т|б\-р|пл\.|пер\.)+$/i", $record[5], $matches);

				$streetName = sizeof($matches) ? $matches[1] : $record[5];
				$streetType = sizeof($matches) ? $matches[2] : '';

		    	// Skip first line
		    	if ($record[0] == 'Телефон') {
					continue;
				}


				// Check if such phone number already exist, if yes go to next phone number
				try {
					$sth = $this->db->prepare('
						SELECT `phone_number`
						FROM `' . $this->dbPrefix . 'phones`
						WHERE `phone_number` = ?
						LIMIT 0, 1
					');

					$sth->execute(array(
						$record[0]
					));

					$phone = $sth->fetch(PDO::FETCH_OBJ);
					
					if ($phone) {
						// Display info about process
						$skipedCnt++;
						if ($skipedCnt % 50 == 0) {
							echo 'Skiped ' . $skipedCnt . ' phones<br>';
						}
						continue;
					}
				} catch (PDOException $e) {
					echo 'Error: ' . $e->getMessage() . '<br>';
					var_dump($e->getTrace());
					exit;
				}


				// Insert location
				$locationId = $this->getLocationId($record[4]);

				if (!$locationId) {
					try {
						$sth = $this->db->prepare('
							INSERT INTO `' . $this->dbPrefix . 'locations`
							SET `city_id` = 1,
								`name` = ?
						');
						$sth->execute(array(
							$record[4]
						));
					} catch (PDOException $e) {
						echo 'Error: ' . $e->getMessage() . '<br>';
						var_dump($e->getTrace());
						exit;
					}

					// Get location id
					$locationId = $this->getLocationId($record[4]);
				}


				// Insert street
				$streetId = $this->getStreetId($locationId, $streetType, $streetName);

				if (!$streetId) {
					try {
						$sth = $this->db->prepare('
							INSERT INTO `' . $this->dbPrefix . 'streets`
							SET `location_id` = ?,
								`type` = ?,
								`name` = ?
						');
						$sth->execute(array(
							$locationId,
							$streetType,
							$streetName
						));
					} catch (PDOException $e) {
						echo 'Error: ' . $e->getMessage() . '<br>';
						var_dump($e->getTrace());
						exit;
					}

					// Get street id
					$streetId = $this->getStreetId($locationId, $streetType, $streetName);
				}
					

				// Insert house
				$houseId = $this->getHouseId($streetId, $record[6], $record[7]);

				if (!$houseId) {
					try {
						$sth = $this->db->prepare('
							INSERT INTO `' . $this->dbPrefix . 'houses`
							SET `street_id` = ?,
								`num` = ?,
								`block` = ?
						');
						$sth->execute(array(
							$streetId,
							$record[6],
							$record[7]
						));
					} catch (PDOException $e) {
						echo 'Error: ' . $e->getMessage() . '<br>';
						var_dump($e->getTrace());
						exit;
					}

					// Get house id
					$houseId = $this->getHouseId($streetId, $record[6], $record[7]);
				}
				

				// Insert person
				$personId = $this->getPersonId($record[1], $record[2], $record[3], $houseId, $record[8], $record[9]);

				if (!$personId) {
					try {
						$sth = $this->db->prepare('
							INSERT INTO `' . $this->dbPrefix . 'persons`
							SET `second_name` = ?,
								`first_name` = ?,
								`middle_name` = ?,
								`house_id` = ?,
								`room_num` = ?,
								`room_letter` = ?
						');
						$sth->execute(array(
							$record[1],
							$record[2],
							$record[3],
							$houseId,
							$record[8],
							$record[9]
						));
					} catch (PDOException $e) {
						echo 'Error: ' . $e->getMessage() . '<br>';
						var_dump($e->getTrace());
						exit;
					}

					// Get person id
					$personId = $this->getPersonId($record[1], $record[2], $record[3], $houseId, $record[8], $record[9]);
				}
				

				// Insert phone
				try {
					$sth = $this->db->prepare('
						INSERT INTO `' . $this->dbPrefix . 'phones`
						SET `person_id` = ?,
							`phone_number` = ?
					');
					$sth->execute(array(
						$personId,
						$record[0]
					));
				} catch (PDOException $e) {
					echo 'Error: ' . $e->getMessage() . '<br>';
					var_dump($e->getTrace());
					exit;
				}

				// Display info about process
				$importedCnt++;
				if ($importedCnt % 50 == 0) {
					echo 'Imported ' . $importedCnt . ' phones<br>';
				}
		    }
		    echo 'Total skiped ' . $skipedCnt . ' phones<br>';
		    echo 'Total imported ' . $importedCnt . ' phones<br>';
		    echo 'Ready!';
		}
	}

	protected function getLocationId($name) {
		$locationId = 0;

		try {
			$sth = $this->db->prepare('
				SELECT `id`
				FROM `' . $this->dbPrefix . 'locations`
				WHERE `city_id` = 1
					AND `name` = ?
				LIMIT 0, 1
			');

			$sth->execute(array(
				$name
			));

			$location = $sth->fetch(PDO::FETCH_OBJ);
			if ($location) {
				$locationId = $location->id;
			}
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		return $locationId;
	}

	protected function getStreetId($locationId, $streetType, $streetName) {
		$streetId = 0;

		try {
			$sth = $this->db->prepare('
				SELECT `id`
				FROM `' . $this->dbPrefix . 'streets`
				WHERE `location_id` = ?
					AND `type` = ?
					AND `name` = ?
				LIMIT 0, 1
			');

			$sth->execute(array(
				$locationId,
				$streetType,
				$streetName
			));

			$street = $sth->fetch(PDO::FETCH_OBJ);
			if ($street) {
				$streetId = $street->id;
			}
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		return $streetId;
	}

	protected function getHouseId($streetId, $num, $block) {
		$houseId = 0;

		try {
			$sth = $this->db->prepare('
				SELECT `id`
				FROM `' . $this->dbPrefix . 'houses`
				WHERE `street_id` = ?
					AND `num` = ?
					AND `block` = ?
				LIMIT 0, 1
			');

			$sth->execute(array(
				$streetId,
				$num,
				$block
			));

			$house = $sth->fetch(PDO::FETCH_OBJ);
			if ($house) {
				$houseId = $house->id;
			}
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		return $houseId;
	}
	
	protected function getPersonId($secondName, $firstName, $middleName, $houseId, $roomNum, $roomLetter) {
		$personId = 0;

		try {
			$sth = $this->db->prepare('
				SELECT `id`
				FROM `' . $this->dbPrefix . 'persons`
				WHERE `second_name` = ?
					AND `first_name` = ?
					AND `middle_name` = ?
					AND `house_id` = ?
					AND `room_num` = ?
					AND `room_letter` = ?
				LIMIT 0, 1
			');

			$sth->execute(array(
				$secondName,
				$firstName,
				$middleName,
				$houseId,
				$roomNum,
				$roomLetter
			));

			$person = $sth->fetch(PDO::FETCH_OBJ);
			if ($person) {
				$personId = $person->id;
			}
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		return $personId;
	}
				
}

$dbImport = new DbImport();
$dbImport->execute();
?>