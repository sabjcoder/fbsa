<?php
if (!session_id()) {
    session_start();
}
require_once "vadersentiment.php";
include 'fb_config.php';
$helper = $fb->getRedirectLoginHelper();
if(isset($_GET['logout']))
{
	$fbLogoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'https://fbsa-sentiment.herokuapp.com/index.php');    
	session_destroy();
	unset($_SESSION['access_token']);
	header("Location: $fbLogoutUrl");
	exit;
}
if (!isset($_SESSION['fb_access_token'] )) {
	
	try {
		$accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	if (! isset($accessToken)) {
		if ($helper->getError()) {
			header('HTTP/1.0 401 Unauthorized');
			echo "Error: " . $helper->getError() . "\n";
			echo "Error Code: " . $helper->getErrorCode() . "\n";
			echo "Error Reason: " . $helper->getErrorReason() . "\n";
			echo "Error Description: " . $helper->getErrorDescription() . "\n";
		} else {
			header('HTTP/1.0 400 Bad Request');
			echo 'Bad request';
		}
		exit;
	}
	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb->getOAuth2Client();
	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);
	// Validation (these will throw FacebookSDKException's when they fail)
	$tokenMetadata->validateAppId($appId);
	$tokenMetadata->validateExpiration();
	if (! $accessToken->isLongLived()) {
		// Exchanges a short-lived access token for a long-lived one
		try {
			$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
			exit;
		}
	}
	$_SESSION['fb_access_token'] = (string) $accessToken;
	$accessToken1=$_SESSION['fb_access_token'];
	// glob $commentCount=0;
	$page_graph=$fb->get( '/me/accounts' );
	$page_edge=$page_graph->getGraphEdge();
	$noOfPages=$page_edge->asArray();
	echo "<pre>";
	print_r($noOfPages);
	echo "</pre>";
}
?>