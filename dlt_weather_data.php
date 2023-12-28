<?
class get_data_db{
	private function check_active($ip){
		global $wpdb;
		$table_name = $wpdb->prefix . 'weather_data';
		
		$query = $wpdb->prepare(
			"SELECT active FROM $table_name WHERE ip = %s",
			$ip
		);
		$results = $wpdb->get_results($query);
		if (!empty($results)){
			if ($results[0]->active == 1){
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	private function return_data($status,$val){
		echo json_encode(['status'=>$val, 'val'=>$val]);
	}
	
	public function run(){
		$active = $this->check_active($_SERVER['REMOTE_ADDR']);
		if ($active) {
			$data = [];
			$this->return_data(1,$data);
		} else {
			$this->return_data(0,0);
		}
	}
}
add_action( 'wp_ajax_weatherPlug', 'get_data_weatherPlug' );
add_action( 'wp_ajax_nopriv_weatherPlug', 'get_data_weatherPlug' );
function get_data_weatherPlug() {
	$get_data = new get_data_db;
	$get_data->run();
	wp_die();
}
?>