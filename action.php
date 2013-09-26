<?php

require 'php/Db.php';

class Base09Actions extends Db {
	public function performSearch() {
		$return = array();

		// Remove whitespaces from begining and end of search parameters
		array_map('trim', $_GET);

		// Get search parameters
		$phoneNumber = $_GET['phone-number'];
		$secondName  = $_GET['second-name'];
		$firstName   = $_GET['first-name'];
		$middleName  = $_GET['middle-name'];
		$street      = $_GET['street'];
		$house       = $_GET['house'];
		$room        = $_GET['room'];

		// Remove all non numeric characters from phone number
		$phoneNumber = mb_substr(preg_replace('/[^0-9]/', '', $phoneNumber), 0, 256, 'UTF-8');

		$secondName = mb_substr($secondName, 0, 256, 'UTF-8');
		$firstName  = mb_substr($firstName, 0, 1, 'UTF-8');
		$middleName = mb_substr($middleName, 0, 1, 'UTF-8');

		$streetType = '';
		$streetName = $street;

		// Split street into type and name
		preg_match_all('/^((ул|вул|улица|вулиця|пр|пр\-т|просп|проспект|бул|б\-р|бульвар|пл|площадь|площа|пер|пров|переулок|провулок)+\.?\s+)?(.*?)(\s+(ул|вул|улица|вулиця|пр|пр\-т|просп|проспект|бул|б\-р|бульвар|пл|площадь|площа|пер|пров|переулок|провулок)+\.?)?$/i', $street, $streetMatches);

		if (!empty($streetMatches[0])) {
			$streetType = $streetMatches[2][0] ? $streetMatches[2][0] : ($streetMatches[5][0] ? $streetMatches[5][0] : '');
			$streetName = $streetMatches[3][0] ? $streetMatches[3][0] : '';

			$streetType = preg_replace('/^(ул|вул|улица|вулиця)$/i',        'ул.', trim($streetType));
			$streetType = preg_replace('/^(пр|пр\-т|просп|проспект)$/i',    'пр-т',     $streetType);
			$streetType = preg_replace('/^(бул|б\-р|бульвар)$/i',           'б-р',      $streetType);
			$streetType = preg_replace('/^(пл|площадь|площа)$/i',           'пл.',      $streetType);
			$streetType = preg_replace('/^(пер|пров|переулок|провулок)$/i', 'пер.',     $streetType);
		}

		$streetType = mb_substr($streetType, 0, 256, 'UTF-8');
		$streetName = mb_substr($streetName, 0, 256, 'UTF-8');

		$houseNum   = $house;
		$houseBlock = '';

		// Split house into number and block
		preg_match_all('/^(\d+)[\s\-\/]*(.*?)$/i', $house, $houseMatches);

		if (!empty($houseMatches[0])) {
			$houseNum   = $houseMatches[1][0];
			$houseBlock = $houseMatches[2][0];
		}

		$houseNum   = mb_substr($houseNum, 0, 256, 'UTF-8');
		$houseBlock = mb_substr($houseBlock, 0, 256, 'UTF-8');

		$roomNum = $room;
		$roomLetter = '';

		// Split room into number and letter
		preg_match_all('/^(\d+)[\s\-\/]*(.*?)$/i', $room, $roomMatches);
		if (!empty($roomMatches[0])) {
			$roomNum    = $roomMatches[1][0];
			$roomLetter = $roomMatches[2][0];
		}

		$roomNum    = mb_substr($roomNum, 0, 256, 'UTF-8');
		$roomLetter = mb_substr($roomLetter, 0, 256, 'UTF-8');

		// Prepare search query
		try {
			$query = '
				SELECT
					`ph`.`phone_number`,
					`pr`.`second_name`,
					`pr`.`first_name`,
					`pr`.`middle_name`,
					`s`.`type` AS `street_type`,
					`s`.`name` AS `street_name`,
					`l`.`name` AS `location_name`,
					`h`.`num` AS `house_num`,
					`h`.`block` AS `house_block`,
					`pr`.`room_num`,
					`pr`.`room_letter`,
					`h`.`lat`,
					`h`.`lon`
				FROM
					`' . $this->dbPrefix . 'countries` AS `cn`
					INNER JOIN `' . $this->dbPrefix . 'cities` AS `ct`
					ON `cn`.`id` = `ct`.`country_id`
					INNER JOIN `' . $this->dbPrefix . 'locations` AS `l`
					ON `ct`.`id` = `l`.`city_id`
					INNER JOIN `' . $this->dbPrefix . 'streets` AS `s`
					ON `l`.`id` = `s`.`location_id`
					INNER JOIN `' . $this->dbPrefix . 'houses` AS `h`
					ON `s`.`id` = `h`.`street_id`
					INNER JOIN `' . $this->dbPrefix . 'persons` AS `pr`
					ON `h`.`id` = `pr`.`house_id`
					INNER JOIN `' . $this->dbPrefix . 'phones` AS `ph`
					ON `pr`.`id` = `ph`.`person_id`
				WHERE
					1=1 
			';

			$queryValues = array();

			if ($phoneNumber) {
				$query .= ' AND `ph`.`phone_number` LIKE ?';
				$queryValues[] = $phoneNumber . '%';
			}

			if ($secondName) {
				$query .= ' AND (`pr`.`second_name` LIKE ? OR `pr`.`second_name` LIKE ?)';
				$queryValues[] = $secondName . '%';
				$queryValues[] = $this->translateToUkrainian($secondName) . '%';
			}

			if ($firstName) {
				$query .= ' AND (`pr`.`first_name` = ? OR `pr`.`first_name` = ?)';
				$queryValues[] = $firstName;
				$queryValues[] = $this->translateToUkrainian($firstName);
			}

			if ($middleName) {
				$query .= ' AND (`pr`.`middle_name` = ? OR `pr`.`middle_name` = ?)';
				$queryValues[] = $middleName;
				$queryValues[] = $this->translateToUkrainian($middleName);
			}

			if ($streetType) {
				$query .= ' AND `s`.`type` = ?';
				$queryValues[] = $streetType;
			}

			if ($streetName) {
				$query .= ' AND (`s`.`name` LIKE ? OR `s`.`name` LIKE ?)';
				$queryValues[] = $streetName . '%';
				$queryValues[] = $this->translateToUkrainian($streetName) . '%';
			}

			if ($houseNum) {
				$query .= ' AND `h`.`num` = ?';
				$queryValues[] = $houseNum;
			}

			if ($houseBlock) {
				$query .= ' AND (`h`.`block` = ? OR `h`.`block` = ?)';
				$queryValues[] = $houseBlock;
				$queryValues[] = $this->translateToUkrainian($houseBlock);
			}

			if ($roomNum) {
				$query .= ' AND `pr`.`room_num` = ?';
				$queryValues[] = $roomNum;
			}

			if ($roomLetter) {
				$query .= ' AND (`pr`.`room_letter` = ? OR `pr`.`room_letter` = ?)';
				$queryValues[] = $roomLetter;
				$queryValues[] = $this->translateToUkrainian($roomLetter);
			}

			// Display results sorted by phone number
			$query .= ' ORDER BY `ph`.`phone_number`';

			// Return maximum 500 records
			$query .= ' LIMIT 0, 500';

// echo vsprintf(str_replace('?', '"%s"', $query), $queryValues);exit;

			// If at least one search parameter was passed
			if (sizeof($queryValues)) {

				$sth = $this->db->prepare($query);

				$sth->execute($queryValues);

				$results = $sth->fetchAll(PDO::FETCH_OBJ);

				foreach ($results as $row) {
					$return[] = array(
						'phoneNumber' => $row->phone_number,
						'secondName'  => $row->second_name,
						'firstName'   => $row->first_name,
						'middleName'  => $row->middle_name,
						'street'      => ($row->street_type ? ($row->street_type . ' ') : '') .
							$row->street_name .
							($row->location_name ? (' (' . $row->location_name . ')') : ''),
						'house'       => $row->house_num ? ($row->house_num .
							($row->house_block
								? (is_numeric($row->house_block[0])
									? '/'
									: (is_string($row->house_block[0]) ? '-' : ' '))
									. $row->house_block
								: '')) : '',
						'room'        => $row->room_num ? ($row->room_num .
							($row->room_letter
								? (is_numeric($row->room_letter[0])
									? '/'
									: (is_string($row->room_letter[0]) ? '-' : ' '))
									. $row->room_letter
								: '')) : '',
						'lat'        => $row->lat,
						'lon'        => $row->lon
					);
				}
			}

// echo $query;
// var_dump($results);exit;
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		echo json_encode($return);
		exit;
	}

	private function translateToUkrainian($text) {
		$letters = array(
			'Е' => 'Є',
			'е' => 'є',
			'Ё' => 'Є',
			'ё' => 'є',
			'И' => 'І',
			'и' => 'і',
			'Ъ' => '',
			'ъ' => '',
			'Ы' => 'И',
			'ы' => 'и',
			'Э' => 'Е',
			'э' => 'е',
		);

		return str_replace(
			array_keys($letters),
			array_values($letters),
			$text
		);
	}

	public function secondNameAutocomplete() {
		$secondName = mb_substr(trim($_GET['query']), 0, 256, 'UTF-8');

		$query = '
				SELECT DISTINCT `second_name`
				FROM `' . $this->dbPrefix . 'persons`
				WHERE
					(`second_name` LIKE ?
					OR `second_name` LIKE ?)
					AND `second_name` <> ?
				ORDER BY `second_name`
				LIMIT 0, 10
			';

		$sth = $this->db->prepare($query);

		$sth->execute(array(
			$secondName . '%',
			$this->translateToUkrainian($secondName) . '%',
			$secondName
		));

		$results = $sth->fetchAll(PDO::FETCH_COLUMN);

		$return = array('query' => 'Li', 'suggestions' => $results);

		// Json response
		echo json_encode($return);
		
		// Jsonp response
		// header('Content-type: application/json');
		// echo $_REQUEST['callback'] . '('. json_encode($return) .')';

		exit;
	}

	public function streetAutocomplete() {
		$street = mb_substr(trim($_GET['query']), 0, 256, 'UTF-8');

		$query = '
				SELECT `concat_name` FROM (
					SELECT DISTINCT `name`,
						CONCAT_WS(" ", `type`, `name`) AS `concat_name`
					FROM `' . $this->dbPrefix . 'streets`
					HAVING
						(`name` LIKE ?
						OR `name` LIKE ?
						OR `concat_name` LIKE ?
						OR `concat_name` LIKE ?)
						AND `name` <> ?
					ORDER BY `name`
					LIMIT 0, 10
				) AS a
			';

		$sth = $this->db->prepare($query);

		$sth->execute(array(
			$street . '%',
			$this->translateToUkrainian($street) . '%',
			$street . '%',
			$this->translateToUkrainian($street) . '%',
			$street
		));

		$results = $sth->fetchAll(PDO::FETCH_COLUMN);

		$return = array('query' => 'Li', 'suggestions' => $results);

		echo json_encode($return);
		
		exit;
	}
}

$base09Actions = new Base09Actions();
if (array_key_exists('action', $_GET)) {
	if (method_exists($base09Actions, $_GET['action'])) {
		return $base09Actions->$_GET['action']();
	} else {
		echo 'Such action doesn\'t exists';
		exit;
	}
} else {
	echo 'No action provided';
	exit;
}
?>