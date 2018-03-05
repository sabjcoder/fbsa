<?php
require_once __DIR__ . '/vendor/autoload.php';
$appId = '131460884347524';
$fb = new Facebook\Facebook([
	'app_id' => $appId,
	'app_secret' => '2b1786c5c413be56531c8b613898942a',
	'default_graph_version' => 'v2.12',
]);
?>
