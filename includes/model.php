<?php

/**
 * Use the Momentum Namespace for all requests.
 * @example new \API($account, $key);
 */
namespace Momentum;
use Exception;

class ApiModel {

	/**
	 * This is the model being accessed.
	 */
	protected $__model;

	/**
	 * This is the contents of the API Response.
	 */
	protected $__data;

	/**
	 * This is the contents of the API Response.
	 */
	protected $__status;

	/**
	 * Any Modified Fields
	 */
	protected $__modified = array();

	/**
	 * The instance of the current API.
	 */
	protected $__api;

	/**
	 * Cache a reference to the initialized API object.
	 */
	public function __construct($api) {
		$this->__api = $api;
	}

	/**
	 * Execute the API Call to load this model.
	 *
	 * @param $id [integer or array]
	 * @author David boskovic
	 * @since 09/23/2012
	 */
	public function __initialize($id) {

		/**
		 * If a model instance is being initialized with an array, we'll assume it's an array of 
		 * properly formatted data coming from a list or another api call.
		 */
		if(is_array($id))
			return $this->__data = $id;

		/**
		 * Otherwise this must be an ID or a Slug and we'll call the API for the data.
		 */
		$result = Curl::GetRequest($this->__model."/$id");

		/**
		 * If for some reason there's no data, this item doesn't exist.
		 * @todo add support for the http status codes that are returned.
		 */
		if(!$result->body)
			throw new Exception(ucwords($this->__model)." #$id not found!");

		/**
		 * Decode the response data into array format.
		 */
		$result = json_decode($result->body,1);

		$this->__data = $result['data'];

		$this->__status = $result['status'];
	}

	/**
	 * Get a variable that's attached to the model.
	 * @example $model->name
	 */
	public function __get($var)  {
		$var = str_replace('_', '-', $var);
		return $this->__data[$var];
	}

	/**
	 * Set a variable that's attached to the model. You must call ->save() in order for the variable to post to the API.
	 * @example $model->name = 'Cool New Name'; $model->save();
	 */
	public function __set($var, $val)  {
		$var = str_replace('_', '-', $var);
		if($var == 'id') return false;
		$this->__modified[$var] = $val;
		$this->__data[$var] = $val;
	}

	public function save($data = array()) {

		foreach($data as $key => $val) {
			$this->$key = $val;
		}

		if($this->__modified) {
			if(\Momentum\Api::$__webhookQueue)
				\Momentum\Api::$__webhookQueueData[$this->__model."/$this->id"] = $this->__modified;
			else {
				$response = Curl::PostRequest($this->__model."/$this->id", $this->__modified);
				$response = json_decode($response->body);

				if($response->status->code == 500)
					throw new Exception($response->status->message);
			}
		}


		return true;

	}

	public function getData() {
		return $this->__data;
	}

	public function getStatus() {
		return $this->__status;
	}
}