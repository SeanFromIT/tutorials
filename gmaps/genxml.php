<?php
/* Create database connection */
$database = new MySQLDB;

/* Init Geocoder */
require_once('geocoder.php');
$geocoder = new Geocoder('YOUR_API_KEY_HERE');

/* Begin XML Output */
echo "<markers>
";

   //get addresses
   $resultHolder = $database->getAddresses();
	while ($row = mysql_fetch_array($resultHolder, MYSQL_ASSOC)) {
		try {
			$num = $row["streetnum"];
			$placemarks = $geocoder->lookup($num, $row["streetname"]);
			if (count($placemarks) > 0) { foreach ($placemarks as $placemark) { ?>
<marker <?php echo "address=\"" . htmlSpecialChars($placemark) ?>" lat="<?php echo $placemark->getPoint()->getLatitude() ?>" lng="<?php echo $placemark->getPoint()->getLongitude() ?>"/>
<?php } 
}
		}
		catch (Exception $ex) {
			echo $ex->getMessage();
			exit;
		}
	}
echo "</markers>";
				
class MySQLDB {
	var $connection;         //The MySQL database connection
	/* Class constructor */
	function MySQLDB(){
		/* Make connection to database */
		$this->connection = mysql_connect('YOUR_DB_SERVER', 'YOUR_DB_USER', 'YOUR_DB_PASS') or die(mysql_error());
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
	}
	function getAddresses() {
		$q = "SELECT DISTINCT streetnum, streetname FROM YOUR_TABLE";
		$result = mysql_query($q, $this->connection) or die(mysql_error());
		return $result;
	}
}
?>