<?php
class Geocoder
    {
        public static $url = 'http://maps.google.com/maps/geo';
 
        const G_GEO_SUCCESS             = 200;
        const G_GEO_BAD_REQUEST         = 400;
        const G_GEO_SERVER_ERROR        = 500;
        const G_GEO_MISSING_QUERY       = 601;
        const G_GEO_MISSING_ADDRESS     = 601;
        const G_GEO_UNKNOWN_ADDRESS     = 602;
        const G_GEO_UNAVAILABLE_ADDRESS = 603;
        const G_GEO_UNKNOWN_DIRECTIONS  = 604;
        const G_GEO_BAD_KEY             = 610;
        const G_GEO_TOO_MANY_QUERIES    = 620;
 
        protected $_apiKey;
 
        public function __construct($key)
        {
            $this->_apiKey = $key;
        }
 
        public function performRequest($search, $output = 'xml')
        {
            $url = sprintf('%s?q=%s&output=%s&key=%s&oe=utf-8',
                           self::$url,
                           urlencode($search),
                           $output,
                           $this->_apiKey);
 
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
 
            return $response;
        }
		
		public function lookup($streetnum, $streetname)
        {
			$search = $streetnum . " " . $streetname . ", Terre Haute, IN";
            $response = $this->performRequest($search, 'xml');
            $xml      = new SimpleXMLElement($response);
            $status   = (int) $xml->Response->Status->code;
 
            switch ($status) {
                case self::G_GEO_SUCCESS:
                    require_once('placemark.php');
 
                    $placemarks = array();
                    foreach ($xml->Response->Placemark as $placemark)
                        $placemarks[] = Placemark::FromSimpleXml($placemark);
 
                    return $placemarks;
 
                case self::G_GEO_UNKNOWN_ADDRESS:
                case self::G_GEO_UNAVAILABLE_ADDRESS:
                    return array();
 
                default:
                    throw new Exception(sprintf('Google Geo error %d occurred', $status));
            }
        }
    }
?>