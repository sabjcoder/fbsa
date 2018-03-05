<?php
require_once __DIR__ . '/vendor/autoload.php';
$appId = '131460884347524';
$fb = new Facebook\Facebook([
	'app_id' => $appId,
	'app_secret' => 'c3d73d017b978bc466b6329838ccfbf7',
	'default_graph_version' => 'v2.12',
]);
?>
