<?php

/**
 * Use the Momentum Namespace for all requests.
 * @example new \API($account, $key);
 */
namespace Momentum;
use Exception;

class WebhookResponse {

	/**
	 * This is the contents of the Payload
	 */
	protected $__payload;


	/**
	 * This is the event that this webhook model can respond to.
	 */
	protected $__event;

	/**
	 * The instance of the current API.
	 */
	protected $__api;

	/**
	 * Execute the API Call to load this model.
	 */
	public function __construct($api) {

		$this->__api = $api;

		if(!$_POST['WebhookPayload'])
			throw new Exception("No Webhook Payload Found");

		if($_POST['WebhookEvent'] != $this->__event)
			throw new Exception("Trying to Initiate the Wrong Event Object for the webhook:".$_POST['WebhookEvent']);

		$this->__payload = json_decode($_POST['WebhookPayload'],1);
	}

	public function sendResponse($data= array()) {

		header("Content-Type: application/json");

		$data['__webhookQueue'] = \Momentum\Api::$__webhookQueueData;

		echo json_encode(array('status' =>array('code' => 200, 'message' => 'Received Webhook Successfully'), "data" => $data));

	}

	public function queueResponses() {
		\Momentum\Api::$__webhookQueue = true;
	}
}