<?php
require '../../php/Db.php';

class Sitemap extends Db {

	private $sitemapPath = '../../';

	protected $sitemapUrls = array();
	protected $sitemapIndex = 0;

	public function __construct() {
		error_reporting(E_ALL);
		set_time_limit(0);
		ini_set('memory_limit', '128M');

		parent::__construct();
	}

	public function execute() {
		
		// Add index page to sitemap
		$this->sitemapUrls[] = $this->prepareSitemapRecord('');

		// Get phone numbers
		try {
			$sth = $this->db->prepare('SELECT COUNT(*) AS `cnt` FROM `' . $this->dbPrefix . 'phones`');

			$sth->execute();

			$result = $sth->fetch(PDO::FETCH_OBJ);

			// Total number of pages in sitemap
			$totalCount = $result->cnt + count($this->sitemapUrls);

			// SQL limit offset value 
			$limitOffset = 0;

			// SQL limit count value 
			$limitCount = 50000 - count($this->sitemapUrls);

			for ($i = 0; $i <= $totalCount; $i += 50000) {
				try {
					$sth = $this->db->prepare('
						SELECT `phone_number`
						FROM `' . $this->dbPrefix . 'phones`
						ORDER BY `phone_number`
						LIMIT ' . $limitOffset . ', ' . $limitCount
					);

					$limitOffset += $limitCount;
					$limitCount = 50000;

					$sth->execute();

					$results = $sth->fetchAll(PDO::FETCH_OBJ);

					// Make sitemap files
					foreach ($results as $row) {
						$this->sitemapUrls[] = $this->prepareSitemapRecord($row->phone_number);
					}

					$this->makeSitemap();

				} catch (PDOException $e) {
					echo 'Error: ' . $e->getMessage() . '<br>';
					var_dump($e->getTrace());
					exit;
				}
			}

			// Make sitemap index file
			$this->makeSitemapIndex();

			echo 'Total pages in sitemaps: ' . $limitOffset . '<br>';
			echo 'Ready!';
			
		} catch (PDOException $e) {
			echo 'Error: ' . $e->getMessage() . '<br>';
			var_dump($e->getTrace());
			exit;
		}
	}

	protected function prepareSitemapRecord($phoneNumber) {
		return '
	<url>
		<loc>http://baza09.com.ua/' . ($phoneNumber ? '044' . $phoneNumber . '.html' : '') . '</loc>
	</url>';
	}

	protected function makeSitemap() {
		$sitemapContent = $this->prepareSitemap();
		$this->sitemapIndex++;
		file_put_contents($this->sitemapPath . 'sitemap' . $this->sitemapIndex . '.xml', $sitemapContent);
		$this->sitemapUrls = array();
	}

	protected function prepareSitemap() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . implode('', $this->sitemapUrls) . '
</urlset>';
	}

	protected function makeSitemapIndex() {
		$sitemaps = array();

		for ($i = 1; $i <= $this->sitemapIndex; $i++) {
			$sitemaps[] = $this->prepareSitemapIndexRecord($i);
		}

		$sitemapIndexContent = $this->prepareSitemapIndex($sitemaps);
		file_put_contents($this->sitemapPath . 'sitemap_index.xml', $sitemapIndexContent);
	}

	protected function prepareSitemapIndexRecord($i) {
		return '
	<sitemap>
		<loc>http://baza09.com.ua/sitemap' . $i . '.xml</loc>
	</sitemap>';
	}

	protected function prepareSitemapIndex($sitemaps) {
		return '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . implode('', $sitemaps) . '
</sitemapindex>';
	}
}

$sitemap = new Sitemap();
$sitemap->execute();
?>