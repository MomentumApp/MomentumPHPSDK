<?php

/**
 * Include the Momentum API Library
 */
include('../../includes/api.php');
include('../../init.php');

try {
	$webhook = $api->webhookDonation();
	$webhook->queueResponses();
	$member = $webhook->getMember();
	$member->location = 'test';
	$member->save();
	$webhook->sendResponse(array('code' => 'P-7489hf','serial'=>'asfd9448hf83hf84h48849h','information' => array('test' => 'stuff')));
}
catch(Exception $e) {
	$api->webhookException($e);
}