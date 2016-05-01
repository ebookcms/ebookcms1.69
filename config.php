<?php

	# DATABASE TYPE
	define('DB_TYPE', 'sqlite');
	
	# DATABASE DRIVER - (pdo/mysqli) - mysqli (only for mysql, oracle databases)
	define('DB_DRIVER', 'pdo');

	# SECURITY KEYS
	define('SECURE_ID', '1234');
	define('POST_ID', '4321');
	define('LOGIN_ID', '5678');
	define('SUM_ID', '8765');
	define('SESSION_NAME','guest123');
	
	# RTE - RICH-TEXT EDITOR
	define('RTE_with', '920');				// 920 pixels
	define('RTE_height', '300');			// 300 pixels
	define('RTE_CSS', 'css/content.css');	// CSS FOR CENTER FUNCTION
	define('RTE_TB1', 'blockquote,div'); 	// ,youtube,pre,span,smcool,smcry,smembarrassed,smsmille

	# OTHER OPTIONS
	define('HTACCESS', true);				// BETTER SEO
	define('use_https', false);				// EXPERIMENTAL MUST HAVE CERTIFICATES OR WONT WORK
	define('TIME_OUT', '120');				// 2 min = 120 seconds
	define('USE_ADDONS', true);				// USE ADDONS TO EXPAND 

	# DO NOT CHANGE NEXT LINES
	if (!defined('SECURE_ID')) die('ACCESS DENIED');
	$req_uri = substr(dirname(__FILE__), strripos(dirname(__FILE__), '/')+1);
	define('HURI', $req_uri); unset($req_uri);
	ini_set('session.gc_maxlifetime', TIME_OUT);
	
	# LOCAL
	if ($_SERVER['SERVER_ADDR'] === "127.0.0.1" || $_SERVER['SERVER_ADDR'] === "::1") {
		$server = 'localhost';
		$database = 'ebookcms';
		$user   = 'root';
		$password = '';
		$dbpath = 'ebookcms.db3';
	} else {

	# ONLINE
		$server = 'localhost';
		$database = 'your_database';
		$user   = 'your_user';
		$password = 'your_password';
		$dbpath = 'ebookcms.db3';
	}
	
	// WHAT DATABASE YOU CHOOSE
	switch(DB_TYPE){
		case "mysql"  : $dbconn = "mysql:host=$server;dbname=$database;"; break;
  		case "sqlite" : $dbconn = "sqlite:$dbpath"; break;
		case "postgresql" : $dbconn = "pgsql:host=$server dbname=$database"; break;
		case "sqlexpress" : $dbconn = "mssql:host=$server dbname=$database"; break;
		case "firebird" : $dbconn = "firebird:dbname=$server:$dbpath"; break;
	}

	# PREFIX
	define('PREFIX', '');
	
	# LANGUAGE DEFAULT
	define('LANG','en');
	
	# YOUR KEY ENCRYPTION - PLEASE DO NOT DELETE OR CHANGE IT - 64 unique chars [A-Za-z0-9_-]
	define('YOUR_KEY', 'aPO7IU8Yz6xTREWQc9ASDvFGHJb4KLnZXmCVB3NM5lkjhgfdsqwerty01u2iop-_');
	
?>