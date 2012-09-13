<?php

/**
 * Use the Momentum Namespace for all requests.
 * @example new \API($account, $key);
 */
namespace Momentum;
use Exception;

include(__DIR__.'/curl.php');
include(__DIR__.'/model.php');
include(__DIR__.'/list.php');
include(__DIR__.'/webhook.php');

/**
 * Include the models.
 * @todo make this use autoloader, however could conflict with other autoloaders if this library is included.
 */
include(__DIR__.'/../models/project.php');
include(__DIR__.'/../models/donation.php');
include(__DIR__.'/../models/member.php');


/**
 * Include the lists.
 */
include(__DIR__.'/../lists/projects.php');


/**
 * Include the webhooks.
 */
include(__DIR__.'/../webhooks/donation.php');

class API {

	/**
	 * Save the authentication credentials here.
	 * @var array
	 */
	private $__credentials;

	/**
	 * The initialized instance of the API.
	 */
	public static $instance;

	/**
	 * Queue Model saving to get passed back in one response.
	 */
	public static $__webhookQueue = false;
	public static $__webhookQueueData = array();

	/**
	 * In order to use the API, initiate the class with the proper credentials.
	 *
	 * @author David Boskovic
	 * @since 09/11/2012
	 */
	public function __construct($account, $apikey, $url) {

		$this->__credentials = array('account' => $account, 'key' => $apikey);

		Curl::$base_url = $url;
		Curl::setCredentials($account, $apikey);
		self::$instance = $this;
	}

	public function __call($method, $args) {
		if(substr($method, 0, 3) == 'get') {
			$mode = 'get';
			$method = substr($method,3);
			$class = "Momentum\\Models\\$method";
		}
		elseif(substr($method, 0, 3) == 'new') {
			$mode = 'new';
			$method = substr($method,3);
			$class = "Momentum\\Models\\$method";
		}
		elseif(substr($method, 0, 4) == 'list') {
			$mode = 'list';
			$method = substr($method,4);
			$class = "Momentum\\Lists\\$method";
		}
		elseif(substr($method, 0, 7) == 'webhook') {
			$mode = 'webhook';
			$method = substr($method,7);
			$class = "Momentum\\Webhooks\\$method";
		}
		else {
			throw new Exception('You must either call a get, new, webhook or list method.');
		}

		if(class_exists($class)){
			$object = new $class($this);

			if($mode == 'get')
				call_user_func_array(array($object,'__initialize'),  $args);
			return $object;
		}
	}

	public function webhookException(Exception $e) {

		/**
		 * Return the results
		 */
		header("Content-Type: application/json");

		echo json_encode(array('status' =>array('code' => 500, 'message' => $e->getMessage())));
	}

}