<?php 

$ini = parse_ini_file(dirname(__FILE__).'/../../../version.ini');
define('BITWASP_CREATED_TIME', $ini['bitwasp_created_time']);
/**
 * Version Checker
 *
 * This job is used to check for an updated version of BitWasp on GitHub
 * and is run once a week. Called by the autorun library, and reports 
 * new version information to the admin via the logging system.
 * 
 * @package		BitWasp
 * @subpackage	Autorun
 * @category	Version Checker
 * @author		BitWasp
 */
class Version_Checker {

	/**
	 * Config
	 * 
	 * This stores predefined information about the job, such as the name,
	 * description, and the frequency at which it should be run.
	 */
	public $config = array(	'name' => 'Version Check',
							'description' => 'An autorun job to check for updates to the BitWasp source code.',
							'index' => 'version_checker',
							'interval' => '7',
							'interval_type' => 'minutes');
	public $CI;
	
	/**
	 * Constructor
	 * 
	 * Load's the CodeIgniter framework.
	 */
	public function __construct() {
		$this->CI = &get_instance();
	}

	/**
	 * Job
	 * 
	 * This function is called by the Autorun script. 
	 */
	public function job() {

		$latest_version = parse_ini_string($this->call_curl('https://raw.github.com/Bit-Wasp/BitWasp/master/version.php'));
		if($latest_version !== FALSE && BITWASP_CREATED_TIME !== FALSE){
			if($latest_version['bitwasp_created_time'] > BITWASP_CREATED_TIME) {
				$this->CI->load->model('logs_model');
				if($this->CI->logs_model->add('Version Checker', 'New BitWasp code available', 'There is a new version of BitWasp available on GitHub. It is recommended that you download this new version (using '.BITWASP_CREATED_TIME.')', 'Info') == TRUE)
					return TRUE;
			}
		}
		return FALSE;
	}
	
	public function call_curl($url, $proxy_arr = array()){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_REFERER, "");
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36");
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		if(isset($source['proxy']) && is_array($source['proxy'])){
			curl_setopt($curl, CURLOPT_PROXYTYPE, $proxy_arr['type']);
			curl_setopt($curl, CURLOPT_PROXY, $proxy_arr['url']);
		}
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}
	
};

/* End of File: Version_Checker.php */
