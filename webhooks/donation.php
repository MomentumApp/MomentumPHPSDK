<?php

namespace Momentum\Webhooks;
use \Momentum\WebhookResponse;
use Exception;

class Donation extends WebhookResponse {

	/**
	 * The webhook event that this object can initialize on.
	 * @var string
	 */
	protected $__event = 'donation';

	/**
	 * Returns the ApiModel for the donation.
	 */
	public function getDonation() {
		return $this->__api->getDonation($this->__payload['donation']);
	}

	/**
	 * Return the ApiModel for the Member
	 */
	public function getMember() {
		return $this->__api->getMember($this->__payload['member']);
	}

	/**
	 * Returns an array of targets and their ApiModels
	 */
	public function getTargets() {
		foreach($this->__payload['targets'] as $key => $data) {
			$getMethod = "get$key";
			return $this->__api->$getMethod($data);
		}
	}


	public function getProject() {
		foreach($this->__payload['targets'] as $key => $data) 
			if($key == 'project') {
				return $this->__api->getProject($data);
			}
		}
		return false;
	}


	/**
	 * Tag the donation.
	 */
	public function tag() {

	}

}