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
			echo $logoutUrl;
		} else {
			$loginUrl = $facebook->getLoginUrl();
			header('Location: '. $loginUrl);
		}

		// https://www.facebook.com/dialog/oauth?client_id=%09147275868807346&redirect_uri=http%3A%2F%2Flocalhost%2Fgit%2Fauthentication%2Fconnect.php%3Foauth%3Dfacebook&state=220084b1525938096841775e07435f7f
		// http://localhost/git/authentication/connect.php?oauth=facebook&code=AQAK_G8I_AU1yFytirkpvC_VPBpTddEXNH0FOq6xchnF-jKytn7eEHe1JYgnepfvUd3ghvspklzBJx7f08-6Q44g20Vaz5ZLzKQRQlrSSXfsMsF0aaWq5pTFmgnGwthCI2R2hmX29xQFUJiNlsOP1yjgBJMgcW6xVnB0mleLCY15GGh-HgnVbXlaVVw-ye5RMyJxcPSoXPenaRHN5_IUhnMqLtRXmLp_g3Ts_H5ak5q66fpsojkW9dIs7L_YMXE5x3DcUrn-NfGGjhzaGKTiIWBCajnVWGWFPUvCSG4vle4vQkJEU4PJ7yfDZnMq3cQ1KFc&state=220084b1525938096841775e07435f7f#_=_
		// https://www.facebook.com/logout.php?next=http%3A%2F%2Flocalhost%2Fgit%2Fauthentication%2Fconnect.php%3Foauth%3Dfacebook&access_token=CAACF8lXYILIBAD0BVzR99h3oFbiZCU87epUcgn0ZB0HFtxGTdoodgvqY6BlmUtHUVmCyesaQsYvTllsUYMigZAhbR5OJsF9vpjUE5mr7DWLdW6FPNRhJweKbum1gDugPLyG3mZAyPV7QREFZBXv3P3VHrkJqjdPYIXDJILcizcKjZBQReYy2JL
		
		// $naitik = $facebook->api('/naitik');
		// echo '<pre>' . htmlspecialchars(print_r($naitik, true)) . '</pre>';
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
			//die("OH NOOOOES! YIKES!");
			header('Location: connect.php?oauth=twitter&error');
		}

		if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
		} else {
			//die("OH NOOOOES!");
			header('Location: connect.php?oauth=twitter&error');
		}

		if (isset($_GET['denied'])) {
			//die("Please login!");
		}

		$twitteroauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		// die($_GET['oauth_verifier']); // (changes) B5E8TD526OBtQyv6F1JPMxoaHqqYftQQmIr2xMv2U
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
