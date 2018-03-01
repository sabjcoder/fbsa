<?php
require_once __DIR__ . '/vendor/autoload.php';
$appId = '2046848278920294';
$fb = new Facebook\Facebook([
	'app_id' => $appId,
	'app_secret' => '5cd90ef0db943a0d1c3098be2a164797',
	'default_graph_version' => 'v2.12',
]);
?>
