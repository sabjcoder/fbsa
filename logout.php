<?php
include 'fb_config.php';
	session_start();
	$helper = $fb->getRedirectLoginHelper();
	$fbLogoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'http://localhost:81/fb_sentiment/index.php');
	session_destroy();
	header("location: $fbLogoutUrl");
	exit;
?>