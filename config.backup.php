<?php
require_once 'config.inc.php';

if (LOCAL) {
	define('FB_APPID','');
	define('FB_SECRET','');
	define('FB_BASEURL','');
	define('TWITTER_CONSUMER_KEY','');
	define('TWITTER_CONSUMER_SECRET','');
	define('TWITTER_OAUTH_CALLBACK','');
} else {
	define('FB_APPID','');
	define('FB_SECRET','');
	define('FB_BASEURL','');
	define('TWITTER_CONSUMER_KEY','');
	define('TWITTER_CONSUMER_SECRET','');
	define('TWITTER_OAUTH_CALLBACK','');
}
?>
