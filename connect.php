<?php
session_start();

require_once 'config.php';

$AUTH = $_GET['oauth'];
$CALLBACK = $_GET['callback'];

if ($AUTH === "facebook") {
	require_once 'api/facebook/facebook.php';

	if (!isset($CALLBACK)) {
		$facebook = new Facebook(array(
			'appId'  => FB_APPID,
			'secret' => FB_SECRET
		));

		$fb_uid = $facebook->getUser();

		if ($fb_uid) {
			try {
				$user_profile = $facebook->api('/me');
				echo '<pre>' . htmlspecialchars(print_r($user_profile, true)) . '</pre>';
			} catch (FacebookApiException $e) {
				echo '<pre>' . htmlspecialchars(print_r($e, true)) . '</pre>';
				$fb_uid = null;
			}
			$logoutUrl = $facebook->getLogoutUrl();
		} else {
			$loginUrl = $facebook->getLoginUrl();
		}

		$naitik = $facebook->api('/naitik');
		echo '<pre>' . htmlspecialchars(print_r($naitik, true)) . '</pre>';
	} else {

	}
} else if ($AUTH === "twitter") {
	require_once 'api/twitter/twitteroauth.php';

	if (!isset($CALLBACK)) {
		$access_token = $_SESSION['access_token'];
		$twitteroauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
		$request_token = $twitteroauth->getRequestToken(TWITTER_OAUTH_CALLBACK);
		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		if ($twitteroauth->http_code == 200) {
			$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
			header('Location: '. $url);
		} else {
			die('Something wrong happened.');
		}
	} else {
		if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
			$_SESSION['oauth_status'] = 'oldtoken';
			session_destroy();
			die("OH NOOOOES! YIKES!");
			header('Location: connect.php?oauth=twitter&error');
		}

		if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
		} else {
			die("OH NOOOOES!");
			header('Location: connect.php?oauth=twitter&error');
		}

		if (isset($_GET['denied'])) {
			die("Please login!");
		}

		$twitteroauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		die($_GET['oauth_verifier']);
		$access_token = $twitteroauth->getAccessToken($_REQUEST['oauth_verifier']);
		$_SESSION['access_token'] = $access_token;
		$user_info = $twitteroauth->get('account/verify_credentials');
		print_r($user_info);

		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);

		if ($twitteroauth->http_code == 200) {
			$_SESSION['status'] = 'verified';
			// Update Database
			//header('Location: index.html');
		} else {
			session_destroy();
			die("OH NOOOOES! NOT SUCCESSFUL!");
			header('Location: connect.php?oauth=twitter&error');
		}
	}
} else if ($AUTH === "google") {

}
?>
