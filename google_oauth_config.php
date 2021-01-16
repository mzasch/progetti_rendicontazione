<?php
	//Google API PHP Library includes
	require_once 'vendor/autoload.php';
	require_once 'env.php';

	$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

	//Create and Request to access Google API
	$client = new Google_Client();
	$client->setApplicationName("Rendicontazione progetti");
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->addScope('https://www.googleapis.com/auth/userinfo.email');
	$client->setRedirectUri($redirect_uri);

  	//Logout
	if (isset($_REQUEST['logout'])) {
  		unset($_SESSION['access_token']);
		$client->revokeToken();
		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL)); //redirect user back to page
		exit();
	}

	if (isset($_SESSION['access_token'])) {
		$client->setAccessToken($_SESSION['access_token']);
		$google_oauth = new Google_Service_Oauth2($client);

		$google_account_info = $google_oauth->userinfo->get();

		$email = $google_account_info->email;
		$_SESSION['loggedEmail'] = $email;

		$picture = $google_account_info->picture;

	} else if (isset($_GET['code'])) {
		$token = $client->authenticate($_GET['code']);
		$_SESSION['access_token'] = $token;

		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	} else {
		$googleAuthUrl = $client->createAuthUrl();
	}
?>
