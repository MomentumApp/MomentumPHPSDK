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
	$webhook->sendResponse(array('test'));
}
catch(Exception $e) {
	$api->webhookException($e);
}