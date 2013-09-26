<?php
require '/php/Db.php';

class PhoneInfo extends Db {
	public function execute($phoneNumber) {
		$return = array();

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
					`ph`.`phone_number` = ?
				LIMIT 0, 1
			';

			$queryValues = array($phoneNumber);

			$sth = $this->db->prepare($query);

			$sth->execute($queryValues);

			$row = $sth->fetch(PDO::FETCH_OBJ);

			if ($row) {
				$return = array(
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

		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		return $return;
	}
}
?>