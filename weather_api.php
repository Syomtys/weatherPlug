<?
class weather_api{
	public function __construct()
	{
		include(__DIR__.'/config.php');
		$this->appid = $appid;
	}
	public function run($geoname_id){
		$apiUrl = "https://api.openweathermap.org/data/2.5/forecast?id={$geoname_id}&appid={$this->appid}";
		
		$response = file_get_contents($apiUrl);
		
		if ($response !== false) {
			$data = json_decode($response);
			if ($data !== null) {
				return $data->list;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}
}
?>