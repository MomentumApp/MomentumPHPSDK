<?php

include('includes/api.php');
include('init.php');

try {

$project = $api->getProject(32);
$project->name = 'This is an awesome project.';
$project->save();

$project = $api->getProject(32);


echo '<div style="white-space:pre;font-family:courier;font-size:10px">';

var_dump($project->getData());


}
catch(Exception $e) {
	echo  '<div style="color:red;font-family:Arial;padding:20px;border:3px solid red;">'.$e->getMessage().'</div>';
}