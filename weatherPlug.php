
<?php
/*
Plugin name: weather plugin
Author: Syomtys
Version: 1.0.0
Description: weather from location user
*/
	
class weather_ip {

	public function __construct()
	{
		include(__DIR__.'/dadata_api.php');
		$this->dadata_api = new Dadata;
		include(__DIR__.'/weather_api.php');
		$this->weather_api = new weather_api;
	}
	
	private function filter_list($list){
		$sorted_list = [];
		foreach ($list as $item) {
			$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $item->dt_txt);
			$sorted_list[$dateTime->format('Y-m-d')][$dateTime->format('H:i:s')] = [
				'temp_min' => (int)$item->main->temp_min-273.15,
				'temp_max' => (int)$item->main->temp_max-273.15,
				'temp' => (((int)$item->main->temp_min+(int)$item->main->temp_max)/2)-273.15
			];
		}
		foreach ($sorted_list as $day => $arr){
			$temp_day = 0;
			foreach ($arr as $temt){
				$temp_day = $temp_day + $temt['temp'];
			}
			$sorted_list[$day]['sr_temp'] = $temp_day/(count($sorted_list[$day]));
		}
		return $sorted_list;
	}
	
	private function add_data_db($list,$city,$ip){
		global $wpdb;
		$table_name = $wpdb->prefix . 'weather_data';
		
		$json = [];
		foreach ($list as $day => $data){
			$json[$day] = $data['sr_temp'];
		}
		$json['city'] = $city;
		$json = json_encode($json);
		
		$query = $wpdb->prepare(
			"SELECT * FROM $table_name WHERE ip = %s",
			$ip
		);
		$results = $wpdb->get_results($query);
		
		if (!empty($results)){
			$data = array(
				'temperature' => reset($list[date('Y-m-d')])['temp'],
				'recorded_at' => current_time('mysql'),
				'data_json' => $json
			);
			$where = array(
				'ip' => $ip,
			);
			$result = $wpdb->update($table_name, $data, $where);
		} else {
			$data = array(
		  	'temperature' => reset($list[date('Y-m-d')])['temp'],
		  	'city' => $city,
		  	'recorded_at' => current_time('mysql'),
		  	'ip' => $ip,
		  	'data_json' => $json
			);
			$wpdb->insert($table_name, $data);
		}
		return ['city' => $city, 'temp' => (reset($list[date('Y-m-d')])['temp'])];
	}
	
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
	
	private function get_list($ip){
		global $wpdb;
		$table_name = $wpdb->prefix . 'weather_data';
		$query = $wpdb->prepare("SELECT temperature, city, recorded_at FROM $table_name ORDER BY recorded_at DESC LIMIT 5");
		$list = $wpdb->get_results($query);
		
		$queryU = $wpdb->prepare("SELECT temperature, city, recorded_at FROM $table_name WHERE ip = %s",
			$ip
		);
		$user = $wpdb->get_results($queryU);
		
		return ['list'=>$list, 'user'=>$user];
	}
	
	private function update_status($ip,$status){
		// echo $status.'/'.$ip;
		$table_name = 'wp_weather_data';
		global $wpdb;
		if ($status == 'OFF'){
			$action = 0;
		} elseif ($status == 'ON'){
			$action = 1;
		}
		$data = array(
			'active' => $action
		);
		$where = array(
			'ip' => $ip
		);
		$result = $wpdb->update($table_name, $data, $where);
		if ($result === false) {
			echo "Ошибка при выполнении запроса: " . $wpdb->last_error;
		} else {
			echo "Запрос успешно выполнен!";
		}
	}
	private function return_data($status,$val,$user){
		header('Content-Type: application/json');
		$data = [
			'status'=>$status, 
			'user'=>$user[0], 
			'val'=>$val
		];
		echo json_encode($data);
	}

	public function run($type){
		if ($type == 'add'){
			$this->dadata_api->init();
			$result = $this->dadata_api->iplocate($_SERVER['REMOTE_ADDR']);
			$city = $result['location']['data']['city'];
			$this->dadata_api->close();
			$list_weather = $this->weather_api->run($result['location']['data']['geoname_id']);
			$filtered_list = $this->filter_list($list_weather);
			$this->add_data_db($filtered_list,$city,$_SERVER['REMOTE_ADDR']);
		} elseif ($type == 'check') {
			$active = $this->check_active($_SERVER['REMOTE_ADDR']);
			if ($active) {
				$data = $this->get_list($_SERVER['REMOTE_ADDR']);
				$this->return_data(1,$data['list'],$data['user']);
			} else {
				$data = [];
				$this->return_data(0,0,0);
			}
		} else {
			$this->update_status($_SERVER['REMOTE_ADDR'],$type);
		}
	}
}
function weather_ip_run() {
	$weather_ip = new weather_ip;
	$weather_ip->run('add');
}
add_action('wp_footer', 'weather_ip_run');


function html_weather() {
  $template_path = plugin_dir_path(__FILE__) . 'templates/section.php';
  if (file_exists($template_path)) {
	include $template_path;
  }
}
add_action('wp_footer', 'html_weather');

add_action('wp_enqueue_scripts', 'plugin_scripts');
function plugin_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('plugin_scripts', plugin_dir_url(__FILE__) . '/js/script.js', array('jquery'), '1.0', true);
}

add_action( 'wp_ajax_weatherPlug', 'get_data_weatherPlug' );
add_action( 'wp_ajax_nopriv_weatherPlug', 'get_data_weatherPlug' );
function get_data_weatherPlug() {
	$weather_ip = new weather_ip;
	$weather_ip->run('check');
	wp_die();
}
add_action( 'wp_ajax_weatherPlug_form', 'get_data_weatherPlug_form' );
add_action( 'wp_ajax_nopriv_weatherPlug_form', 'get_data_weatherPlug_form' );
function get_data_weatherPlug_form() {
	$weather_ip = new weather_ip;
	$weather_ip->run($_POST['form']);
	wp_die();
}
function enqueue_weatherPlug() {
	wp_enqueue_style( 'weatherPlug', plugin_dir_url( __FILE__ ) . '/css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_weatherPlug' );
?>
