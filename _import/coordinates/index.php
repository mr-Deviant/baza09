<?php
require '../php/Db.php';

class Coordinates extends Db {
	private $importFile = 'db.txt';

	private $handle;

	public function __construct() {
		error_reporting(E_ALL);
		set_time_limit(0);
		ini_set('memory_limit', '128M');

		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';

		parent::__construct();
	}

	public function execute() {
		$foundCnt = 0;
		$notFoundCnt = 0;

		// Get all houses without coordinates
		try {
			$sth = $this->db->prepare('
				SELECT
					`h`.`id`,
					`cn`.`name` AS `country_name`,
					`ct`.`name` AS `city_name`,
					`s`.`type` AS `street_type`,
					`s`.`name` AS `street_name`,
					`l`.`name` AS `location_name`,
					`h`.`num` AS `house_num`,
					`h`.`block` AS `house_block`,
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
				WHERE
					(`lat` = ""
					OR `lon` = "")
					AND `s`.`name` <> ""
					AND `h`.`num` <> 0
				ORDER BY `h`.`id`
				LIMIT 0, 2500
			');

			$sth->execute();

			$results = $sth->fetchAll(PDO::FETCH_OBJ);

			foreach ($results as $row) {
				$address = array();
				$address[] = $row->house_num .
					($row->house_block
						? (is_numeric($row->house_block[0])
							? '/'
							: (is_string($row->house_block[0]) ? '-' : ' '))
							. $row->house_block
						: '');
				$address[] = ($row->street_type ? ($row->street_type . ' ') : '') . $row->street_name;
				if ($row->location_name) {
					$address[] = $row->location_name;
				}
				$address[] = $row->city_name;
				$address[] = $row->country_name;

				// Get coordinates from Google or Yandex
				list($lat, $lon) = $this->getCoordinatesByAddress($address);

				// Update statistics
				if (!empty($lat)) {
					$foundCnt++;
				} else {
					$notFoundCnt++;
				}

				// Display results
				echo 'Id: ' . $row->id . ', address: ' . implode(', ', $address) . ', coordinates: ' . $lat . ', ' . $lon . '<br>';

				// Insert coordinates
				try {
					$sth = $this->db->prepare('
						UPDATE `' . $this->dbPrefix . 'houses`
						SET `lat` = ?,
							`lon` = ?
						WHERE `id` = ?
						LIMIT 1
					');
					$sth->execute(array(
						$lat,
						$lon,
						$row->id
					));
				} catch (PDOException $e) {
					echo 'Error: ' . $e->getMessage() . '<br>';
					var_dump($e->getTrace());
					exit;
				}
			}
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}

		echo 'Total found ' . $foundCnt . ' coordinates<br>';
		echo 'Total not found ' . $notFoundCnt . 'coordinates<br>';
		echo 'Ready!';

	}

	protected function getCoordinatesByAddress(array $address) {
		$return = array('', '');

		// Ask google
		$googleAddress = implode(', ', $address);

	    $googleUrl = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($googleAddress) . '&sensor=false';

	    $json = file_get_contents($googleUrl);

	    $data = json_decode($json);

	    if ($data->status == 'OK') {
	    	$location = $data->results[0]->geometry->location;
	    	$return = array($location->lat, $location->lng);
	    }

	    // If google found nothing
	    if (empty($return[0])) {
	    	// Ask yandex
	    	// http://api.yandex.ru/maps/doc/geocoder/desc/concepts/input_params.xml
	    	$yandexAddress = implode(', ', array_reverse($address));

	    	$yandexUrl = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($yandexAddress) . '&format=json';

	    	$json = file_get_contents($yandexUrl);

	    	$data = json_decode($json);

	    	if ($data->response) {
		    	$location = $data->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;

		    	list($lon, $lat) = explode(' ', $location);

		    	$return = array($lat, $lon);
	    	}
	    }

	    return $return;
	}
}

$coordinates = new Coordinates();
$coordinates->execute();
?>