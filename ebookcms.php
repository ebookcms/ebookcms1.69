<?php

/*------------------------------------------------------------------------------

	eBookCMS 1.69
	Stable Release date: September 16, 2015
	Copyright (C) Rui Mendes
	eBookCMS is licensed under a "Creative Commons License"

------------------------------------------------------------------------------*/


// REPORT ERRORS
	error_reporting(1);

// ADD CONFIGURATION FILE
	require(dirname(__FILE__).'/config.php');

// NO SECURE_ID OR NOT EQUAL (config.php) THEN EXIT
	if (!defined('SECURE_ID') || SECURE_ID != '1234') {die('ACCESS DENIED');}
	$sid = substr(session_id(), 1, 10);

// RSA FUNCTIONS
function mo($g, $l) {return $g-($l*floor($g/$l));}
function powmod ($base, $exp, $modulus) {
	$accum = 1; $i = 0;
	$basepow2 = $base;
	while (($exp >> $i)>0) {
		if ((($exp >> $i) & 1) == 1) {$accum = mo(($accum * $basepow2), $modulus);}
		$basepow2 = mo(($basepow2 * $basepow2), $modulus); $i++;
	} unset($g, $l, $base, $basepow2, $i, $modulus, $exp); return $accum;
}

// RUKKA ENCRYPTION
function erukka ($text, $key1 = '', $ignore = true) { $result = ''; $k = 0;
	if (empty($text)) {return '';}
	if (empty($key1)) {$key1 = 'xCPO7IU8Yz6TREWQc9ASDvFGHJb4KLnZXmVB3NMa5lkjhgfdsqwerty01u2iop-_';}
	$key2 = 'qY8wTR9yoVNpIlHJk7g2s3Bazxc-4vbA6SDuiFGt1KLP_O0Unm5EWerQZjhXfdCM';
	if (strlen($key1) != 64 || strlen($key2) != 64) {return '';}
	for ($i = 0; $i < strlen($text); $i++) {
		$k += ord($key1[$i % 63]); $kpos = mo($k, 64); $index = strpos($key1, $text[$i]);
		if ($index !== false) {
			$pk = ord((chr($index) ^ $key1[(int)$kpos]) & chr(63));
			$result .= $key2[$pk];
		} else if ($ignore != true) {
			$char = urlencode($text[$i]);
			$char = str_replace('%','$', $char);
			$result .= $char;
		} else {$result .= chr(0);}
	} unset($text, $key1, $ignore, $k, $i, $key2, $kpos, $index, $pk); return $result;
}

// RUKKA ENCRYPTION WITH "YOUR_KEY"
function erukka1($text, $ignore = true) {
	$pkey = defined('YOUR_KEY') ? YOUR_KEY : '';
	return erukka($text, $pkey, $ignore);
}

// RUKKA DECRYPTION
function drukka($text, $key1 = '') {
	if (empty($text)) {return '';}
	$str = '';  $str1 = ''; $result = ''; $k=0;
	if (empty($key1)) {$key1 = 'xCPO7IU8Yz6TREWQc9ASDvFGHJb4KLnZXmVB3NMa5lkjhgfdsqwerty01u2iop-_';}
	$key2 = 'qY8wTR9yoVNpIlHJk7g2s3Bazxc-4vbA6SDuiFGt1KLP_O0Unm5EWerQZjhXfdCM';
	$text = str_replace('$', '%', $text); $text = urldecode($text);
	for ($i= 0; $i < strlen($text); $i++) {
		$k += ord($key1[$i % 63]); $kpos = (int)mo($k, 64);
		$index = strpos($key2, $text[$i]);
		if ($index !== false) {
			$pk = ord((chr($index) ^ $key1[(int)$kpos]) & chr(63));
			$result .= $key1[$pk];
		} else if ($text[$i] != chr(0)){$result .= $text[$i];}
	} unset($text, $key2, $key1, $pk, $i, $k, $str, $str1, $kpos, $index); return $result;
}

// RUKKA DECRYPTION WITH "YOUR_KEY"
function drukka1($text) {
	$pkey = defined('YOUR_KEY') ? YOUR_KEY : '';
	return drukka($text, $pkey);
}

// DECRYPT STRING USING RSA+RUKKA
function rdecrypt($str, $key = '', $rukka = true) {
	if ($rukka) {$str = drukka($str, $key);}
	$n = ceil(strlen($str)/3); $txt = '';
	for ($i=0; $i < $n; $i++) {
		$k = ($i*3);
		$val = substr($str, $k, 3);
		$val = hexdec($val);
		$ks = $i % 5; $x = '';
		if ($ks == 0) {$x = powmod($val,43,341);}
		if ($ks == 1) {$x = powmod($val,463,589);}
		if ($ks == 2) {$x = powmod($val,283,713);}
		if ($ks == 3) {$x = powmod($val,103,407);}
		if ($ks == 4) {$x = powmod($val,187,319);}
		$txt .= chr($x);
	} return $txt;
}

// DECRYPT STRING USING RSA+RUKKA WITH "YOUR_KEY"
function rdecrypt1($str, $rukka = true) {
	$pkey = defined('YOUR_KEY') ? YOUR_KEY : '';
	return rdecrypt($str, $pkey, $rukka);
}

// ENCRYPT STRING USING RSA+RUKKA
function rencrypt($str, $key = '', $rukka = true) { $txt = '';
	for ($i = 0; $i < strlen($str); $i++) {
		$val = ord(substr($str, $i, 1));
		$ks = $i % 5; $x = '';
		if ($ks == 0) {$x = powmod($val,7,341);}
		if ($ks == 1) {$x = powmod($val,7,589);}
		if ($ks == 2) {$x = powmod($val,7,713);}
		if ($ks == 3) {$x = powmod($val,7,407);}
		if ($ks == 4) {$x = powmod($val,3,319);}
		$res = dechex($x);
		if (strlen($res) == 1) {$res = '00'.$res;}
		if (strlen($res) == 2) {$res = '0'.$res;}
		$txt .= $res;
	} if ($rukka) {return erukka($txt, $key);} else {return $txt;}
}

// ENCRYPT STRING USING RSA+RUKKA  WITH "YOUR_KEY"
function rencrypt1($str, $rukka = true) {
	$pkey = defined('YOUR_KEY') ? YOUR_KEY : '';
	return rencrypt($str, $pkey, $rukka);
}

// CHECK LOGIN
function checkLogin() {
	$a = SECURE_ID.substr(session_id(), 1, 10);
	$b = $_SERVER['HTTP_USER_AGENT'].HURI;
	$c = POST_ID.$_SERVER['REMOTE_ADDR'];
	$value = sha1(md5($a.$b.$c));
	$key = defined('YOUR_KEY') ? YOUR_KEY : '';
	$value = erukka($value, $key);
	return $value;
}

// TERMINATE SESSION
function terminateSession() {
	$_SESSION = array();
	if (isset($_COOKIE['PHPSESSID'])) {setcookie('PHPSESSID', '', time()-30000, '/');}
	session_destroy(); session_start(); session_regenerate_id();
	$_SESSION[HURI][md5(POST_ID.'try_hack')] = true;
	die('No Hack please: Shut down'); exit();
}

# CONTANTS
	define('HTML5', true); $meta = HTML5 ? ' /' : '';
	define('divider','&middot;');
	define('separator',' | ');
	define('TIMEOUT', 300);
	define('LOGGED', isset($_SESSION[HURI][sha1(POST_ID.$sid)]) && $_SESSION[HURI][sha1(POST_ID.$sid)] == checkLogin() ? true : false);
	if (LOGGED) {include('core/admin.php');}

// HEADER COOKIES
	if (LOGGED || (isset($_COOKIE['PHPSESSID']) && !isset($_COOKIE['ejava']))) {
		setcookie("ejava", "", time()-3600);
		if (!isset($_COOKIE['ejava'])) {$_COOKIE['ejava'] = 'disabled';}
	}

// CHECK SITE AND SESSION
	if (isset($_SESSION[HURI]['site']) && $_SESSION[HURI]['site'] != HURI) {session_destroy(); $_SESSION[HURI]['site'] = HURI;}

// DATABASE CONNECTION
function db() { global $server, $dbconn, $database, $user, $password; static $conn;
	if (!$conn) { $conn = false;
		if (DB_DRIVER == 'pdo' || DB_DRIVER == 'PDO') {
			try {$conn = new PDO($dbconn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));}
			catch (PDOException $msg) {die ('Connection error, because: '.$msg->getMessage());}
		} else if (DB_DRIVER == 'mysqli' || DB_DRIVER == 'MYSQLI') {
			$conn = new mysqli($server, $user, $password, $database);
			if (mysqli_connect_errno()) {printf("Connect failed: %s\n", mysqli_connect_error()); exit();}
		} else {die('<h1>Driver Error.</h1><p>Check your config.php (<b>DB_DRIVER</b>)</p>');}
	} return $conn;
} ini_set('default_charset', 'utf-8');

// RETURN SUM ASCII CODE HTTP_USER_AGENT/STRING
function get_hsa($var = '', $a = 1, $b = 0) { static $get_hsa;
	if (!$get_hsa) { $get_hsa = 0;
		$var = empty($var) ? SESSION_NAME.$_SERVER['HTTP_USER_AGENT'] : $var;
		for ($i = 1; $i < strlen($var); $i++) {$get_hsa += ord($var[$i]) * $a - $b;}
	} return (int)$get_hsa;
}

// CLEAN URL TO AVOID INJECTION HACK
function clean($text) {
	if (get_magic_quotes_gpc()) {$text = stripslashes($text);}
	$text = strip_tags(htmlspecialchars($text));
	return $text;
}

// CLEAN INPUT_TEXT
function cleanTxt($text) {
	$array = array('(',')','String.fromChar','/','%','#','\'',';','\0','$','{','}','=','!--','"','&','\\');
	$text = str_replace($array, '', $text);
	$text = strip_tags($text);
	return $text;
}

// PUT ALL $_GET IN ARRAY and clean it(SANITIZE)
if ($_GET) {
	foreach ($_GET as $key => $value) {
		if (strtoupper($key) !== 'PHPSESSID' && strtoupper($key) !== 'GET' && !is_int($key)) {
			$ignore = array('<','>','%','\'','--','+',',','@','&','?','=','\\','#',';','(',')',' ', '{','}','[',']');
			$cleanK = clean($key); $cleanV = clean($value);
			$cleanV = str_replace($ignore, '', $cleanV);
			$ignore = array('/etc/passwd','/etc/httpd/conf/httpd.conf');
			$cleanV = str_replace($ignore, '', $cleanV);
			$get[$cleanK] = $cleanV;
			$uri[] = $cleanK;
		} else {terminateSession();}
	} unset($key, $value);
}

// BETTER SEAO LINK WITH .htaccess
if (defined('HTACCESS') && SECURE_ID !== false) {
	if (isset($get['url'])) {$url = explode('/', $get['url']);}
	else if (isset($_SERVER['PATH_INFO'])) {
		$path = trim($_SERVER['PATH_INFO']);
		if ($path[0] == '/') {$path = substr($path, 1);}
		$url = explode('/', clean($path)); unset($path);
	} else {$url = '';}
	$url = is_array($url) && !empty($url) ? array_filter($url) : $url;
	$k = isset($url[0]) ? 0 : 1;
	$num = ceil(count($url)/2);
	for ($i=$k; $i < $num; $i++) {
		if (isset($url[$i*2])) {
			$get[$url[$i*2]] = isset($url[$i*2+1]) ? $url[$i*2+1] : '';
			$uri[$i] = $url[$i*2];
		}
	} unset($url, $get['url'], $num);
}

// INCLUDE ADDONS
if (USE_ADDONS && is_dir('addons/')) {
	$fd = opendir('addons/');
	while (($file = @readdir($fd)) == true) {
		clearstatcache();
		$ext = substr($file, strrpos($file, '.') + 1);
		if ($ext == 'php' || $ext == 'txt') {include_once('addons/'.$file);}
	}
}

// Mysqli Bind and PDO
function dbbind($result, $args, $binds) {
	$driver = DB_DRIVER != 'mysqli' ? true : false;
	$mysqlnd = function_exists('mysqli_fetch_all');
	if ($driver !== false) {try {$result -> execute($args);} catch (PDOException $msg) {
		die ('Connection error, because: '.$msg->getMessage());} return $result;
	} else {
		$count = is_array($args) ? count($args) : 0;
		$bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
		$bindParamsReferences = array();
		foreach($args as $key => $value) {$bindParamsReferences[$key] = &$args[$key];}
		array_unshift($bindParamsReferences, $binds);
		$bindParamsMethod -> invokeArgs($result, $bindParamsReferences);
		$result -> execute();
		if ($mysqlnd) {return $result -> get_result();} else {return $result;}
	}
}

// RETURN CURRENT DRIVER
function dbDriver(){ static $driver;
	if (!$driver) {$driver = DB_DRIVER;}
	return $driver;
}

// FETCH
function dbfetch($result, $prepared = false) {
	$driver = dbDriver();
	$mysqlnd = function_exists('mysqli_fetch_all');
	if (!isset($result)) {return null;}
	if ($driver == 'pdo') {return $result -> fetch(PDO::FETCH_ASSOC);} else
	if ($prepared && $driver == 'mysqli' && $mysqlnd) {return $result -> fetch_assoc();} else
	if ($prepared && $driver == 'mysqli' && !$mysqlnd) {
		echo 'Extension "mysqlnd" is disable or not installed. Please use PDO (DB_DRIVER in config.php)'; return;
	} else if ($driver == 'mysqli') {return mysqli_fetch_assoc($result);}
}

// PLUGINS (AUTO-LOAD)
	$plug_qwr = "SELECT filename,sef_file FROM ".PREFIX."plugins WHERE enabled = 1 AND auto_load = 1 AND installed = 1";
	if ($result = db() -> query($plug_qwr)) {
		while ($r = dbfetch($result)) {
			if (file_exists('plugins/'.$r['filename'])) {include('plugins/'.$r['filename']);}
			if (function_exists('root_'.$r['sef_file'])) {call_user_func('root_'.$r['sef_file'], 0);}
		} unset($result);
	}

// RETURN SOME SPECIAL INFORMATION
function getInfo($command) {
	switch ($command) {
		case 'PageName' :
			$pagename = clean($_SERVER['SCRIPT_NAME']);
			$pagename = substr($pagename,strripos($pagename, '/')+1);
			if ($pagename=='index.php') {$pagename = '';}
			return $pagename; break;
		case 'ROOT' :
			$http = defined('use_https') && use_https == true && LOGGED && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
			$root = dirname($_SERVER['SCRIPT_NAME']) == '/'
				? $http.$_SERVER['HTTP_HOST']
				: $http.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']);
			$root .= '/'; return $root; break;
		case 'ROOT_DIR' : return getcwd(); break;
		case 'ROOT_FILE' : return dirname(__FILE__); break;
		case 'YOUR_IP' : if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip=$_SERVER['HTTP_CLIENT_IP'];}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}
			else {$ip=$_SERVER['REMOTE_ADDR'];} return $ip;
		break;
	}
}

// RETURN LINK ROOT
function webroot() { static $root;
	if (!$root) {
		$https = defined('HTACCESS') && HTACCESS == true ? true : false;
		$page = getInfo('PageName');
		$root = getInfo('ROOT').$page.($https && !empty($page) ? '/' : '');
	} return $root;
}

// PREPARE FOR SEAO
function pre_seao($key, $value, $slash = true, $link = false, $secure = false) {
	if (!empty($value) || is_numeric($value)) {
		if ($link) {$root = defined('HTACCESS') && HTACCESS == true ? webroot() : webroot().'?';} else {$root = '';}
		if ($secure && defined('use_https') && use_https == true) {$root = str_replace('http://', 'https://', $root);}
		$char = $slash ? '&amp;' : '';
		$result = defined('HTACCESS') && HTACCESS == true ? $root.$key.'/'.$value.'/' : $root.$char.$key.'='.$value;
	} else {$result = '';}
	return $result;
}

// HTTPS PROTOCOL AND NOT LOGGED > HTTP
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && !LOGGED) {
	terminateSession();	echo '<meta http-equiv="refresh" content="0; url='.webroot().'">'; exit;
}

// PAGE FIELDS
	$pfields = "p.id, p.ptype, p.lang_id, p.published, p.date_reg, p.date_upd, p.title, p.showtitle, p.seftitle, p.show_pmsg, ";
	$pfields.= "p.image, p.dmeta, p.keywords, p.texto1, p.texto2, p.author_id, p.comments, p.pcsstype, p.plugin_id";

// REQUEST PAGE OR HOME
	if (isset($uri[0]) && ($uri[0] == 'page' || $uri[0] == 'show')) {
		$qwr = ''; $gx = ''; $gm = 0;
		$pagename = $uri[0] == 'page' ? $get['page'] : $get['show'];
		if (LOGGED) {
			$gm = load_value('user_gm'); if (strlen($gm) == 1) {$gx = '0'.$gm;}
			$pfields .= ", p.key_security";
		}
		$query = "SELECT $pfields, u.realname, l.prefixo, s.admin_by AS sadmin_by, s.sec_type,
			s.only_author AS sauthor, s.show_plugin, s.seftitle AS stitle, s.csstype,
			count(DISTINCT c.id) AS total_comments
			FROM ".PREFIX."pages AS p
			LEFT OUTER JOIN ".PREFIX."users AS u
				on p.author_id = u.id
			LEFT OUTER JOIN ".PREFIX."languages AS l
				on l.id = p.lang_id
			LEFT OUTER JOIN ".PREFIX."sections AS s
				on p.section_id = s.id
			LEFT OUTER JOIN ".PREFIX."comments AS c
				on c.pcontent_id = p.id AND c.approved = 1 AND c.spam = 0 AND c.block_email = 0
			WHERE (p.published = 1 OR p.published = 2) AND p.seftitle = ? LIMIT 1";
		if ($result = db() -> prepare($query)) {
			$result = dbbind($result, array($pagename), 's');
			$acontent = dbfetch($result, true);
			if (!$acontent || !isset($acontent['id'])) {unset($acontent, $result); set_error();}
			if ($acontent['ptype'] == 0) {
				$acontent['admin_by'] = $acontent['sadmin_by'];
				$acontent['only_author'] = $acontent['sauthor'];
			}
			unset($acontent['sadmin_by'], $acontent['sauthor'], $pfields);
			unset($result); $list = explode(',', $acontent['admin_by']);
			$allowed = (!empty($gx) && in_array($gx, $list)) || $gm == 1 ? true : false; unset($list);
			if (LOGGED && !$allowed) {unset($acontent['key_security']);}
		} else {set_error();}
	} else
	// HOME
	if (empty($uri[0])) { $qwr = '';
		if (LOGGED){ $gx = '';
			$gm = load_value('user_gm'); if (strlen($gm) == 1) {$gx = '0'.$gm;}
			$pfields .= ", p.key_security";
		}
		$query = "SELECT $pfields, u.realname, l.prefixo, s.admin_by, s.only_author, s.sec_type, s.seftitle AS stitle, csstype,
			p.seftitle AS home_sef, l.id AS lid, count(DISTINCT c.id) AS total_comments
			FROM ".PREFIX."languages AS l
			LEFT OUTER JOIN ".PREFIX."pages AS p
				on p.author_id = l.page_id
			LEFT OUTER JOIN ".PREFIX."users AS u
				on p.author_id = u.id
			LEFT OUTER JOIN ".PREFIX."sections AS s
				on p.section_id = s.id
			LEFT OUTER JOIN ".PREFIX."comments AS c
				on c.pcontent_id = p.id AND c.approved = 1 AND c.spam = 0  AND c.block_email = 0
			WHERE (p.published = 1 OR p.published = 2) AND l.page_id = p.id";
		if ($result = db()->query($query)) {
			$acontent = dbfetch($result);
			if (!$acontent || !isset($acontent['id'])) {unset($acontent, $result); set_error();}
			unset($result, $pfields);
		} else {set_error();}
	}

// LANGUAGES
	$lang = defined('LANG') ? LANG : 'en';
	if (isset($acontent['prefixo']) && $acontent['prefixo'] != strtoupper($lang) && 
	file_exists('languages/'.strtolower($acontent['prefixo']).'.php')) {
		include('languages/'.strtolower($acontent['prefixo']).'.php');
	} else if (file_exists('languages/'.$lang.'.php')) {include('languages/'.$lang.'.php');}
	else {die("Missing Language: <b>languages/".$lang.".php</b>");} unset($lang);
	
// ADD TAG DEFINITIONS
function tag_def() { global $t;
	# Pages
	if (!isset($t['page_cssopen'])) {$t['page_cssopen'] = '';}
	if (empty($t['page_topline'])) {
		$t['page_topline'] = '<div class="header"><p class="small">$weekday2, $month2 $day2, $year2</p>
		<h1>$title</h1><p class="small">$hour:$minute $posted_by $author</p></div>';}
	if (empty($t['page_bodyline'])) {$t['page_bodyline'] = '<div class="text_content">$text1</div>$privatemsg';}
	if (empty($t['page_footline'])) {$t['page_footline'] = '$edit_page';}
	if (!isset($t['page_cssclose'])) {$t['page_cssclose'] = '';}
	# Comments
	if (empty($t['comments_top'])) {$t['comments_top'] = '<h3>$total_comments $comments</h3><div id="comment-list">';}
	if (empty($t['comments_tbody'])) {$t['comments_tbody'] = '<div class="comment">';}
	if (empty($t['comments_body'])) {
		$t['comments_body'] = '<div class="vcard">
			<div class="vleft"><cite class="user_cite">$id_comment - <strong>$username </strong>$says_web:</cite>
			<div class="date">$month $day, $year $at $hour:$minute</div>
			</div><div class="comment-avatar">$avatar</div>
		</div><br class="clear_right" /><div class="comment-content">$comment</div>
		<div class="comment_footer">$reply_comment $report_spam $edit_comment $delete_comment</div>
		<br class="clear_right" />';
	}
	if (empty($t['comments_bbody'])) {$t['comments_bbody'] = '</div>';}
	if (empty($t['comments_bottom'])) {$t['comments_bottom'] = '</div>';}
} tag_def();
function t($var) {global $t; return $t[$var];}

// TAGS DEFINITION FOR PAGES
function tags($option, $tag) { global $t; global $tags;
	$template = getPHPName(); $result = '';
	if (!isset($tags[$option.'_'.$tag])) {
		if (!empty($template) && isset($t[$template][$option.'_'.$tag])) {
			$tags[$option.'_'.$tag] = $t[$template][$option.'_'.$tag];
		} else if (isset($t[$option.'_'.$tag])) {$tags[$option.'_'.$tag] = $t[$option.'_'.$tag];}
	} if (!empty($tag) && isset($tags[$option.'_'.$tag])) {return $tags[$option.'_'.$tag];}
	else  {return $result;}
}

// WEBSITE LOAD CONFIGURATION
function load_cfg($var) { global $webconf;
	if (!$webconf) {
		$query = "SELECT field, value FROM ".PREFIX."config";
		if ($result = db() -> query($query)) {
			while ($r = dbfetch($result)) {$webconf[$r['field']] = $r['value'];}
			unset($result);
		}
	} $value = isset($webconf[$var]) ? $webconf[$var] : '';
	return $value;
}

// RETRIEVE QUERY
function retrieve_mysql($table, $field, $value, $requested, $other = '', $limit = ' LIMIT 1', $outer = '') {
	$where = empty($field) && empty($value) ? "" : "WHERE $field = $value $other ";
	$where = empty($where) && !empty($other) ? "WHERE $other" : $where;
	$query = "SELECT $requested FROM ".PREFIX.$table." ".$outer." ".$where.$limit;
	if ($result = db() -> query($query)) { $k = 0;
		if ($limit != ' LIMIT 1' && $requested != '*' && empty($outer)) {
			$list = explode(',', $requested);
			while ($r = dbfetch($result)) {
				for ($i = 0; $i < count($list); $i++) {
					$array[$k][trim($list[$i])] = $r[trim($list[$i])];
				} $k++;
			}
		} else {$array = dbfetch($result);}
		unset($result);
	} if (isset($array)) {return $array;}
}

# HEADER REQUESTS
if (isset($uri) && $uri[0] == 'services') {
	if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {session_start();}
	include('core/services.php');
	services();
	session_write_close();
	exit;
}

// STRUCTURE HEAD
function get_header() {global $get, $charset, $acontent; $lfeed = PHP_EOL.chr(9);
	if (empty($webtitle)) {$webtitle = load_cfg('web_title');}
	echo '<title>'.$webtitle.'</title>';
	echo $lfeed.'<base href = "'.getInfo('ROOT').'" />';
	if (isset($get['error']) && $get['error'] == '404') {$webtitle = t('error_404');}
	$charset = !empty($charset) ? $charset : load_cfg('web_charset');
	if (HTML5) {echo $lfeed.'<meta charset = "'.$charset.'" />';}
	else {echo $lfeed.'<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />';}
	$dmeta = isset($acontent['dmeta']) ? $acontent['dmeta'] : '';
	echo $lfeed.'<meta name = "description" content = "'.(!empty($dmeta) ? $dmeta : load_cfg('description')).'" />';
	$kmeta = isset($acontent['keywords']) ? $acontent['keywords'] : '';
	echo $lfeed.'<meta name = "keywords" content = "'.(!empty($kmeta) ? $kmeta : load_cfg('web_keywords')).'" />';
	$author = isset($acontent['realname']) ? rdecrypt1($acontent['realname']) : '';
	echo $lfeed.'<meta name = "author" content = "'.(!empty($author) ? $author : load_cfg('web_author')).'" />';
	if (file_exists('favicon.ico')) {
		echo $lfeed.'<link rel = "Shortcut Icon" href = "'.getInfo('ROOT').'favicon.ico" />';}
	if (file_exists('css/main.css')) {
		echo $lfeed.'<link rel = "stylesheet" type = "text/css" href = "css/main.css" />';}
	if (file_exists('css/style.css')) {
		echo $lfeed.'<link rel = "stylesheet" type = "text/css" href = "css/style.css" />';
	}	
	if (file_exists('css/content.css')) {
		echo $lfeed.'<link rel = "stylesheet" type = "text/css" href = "css/content.css" />';
	}
	echo $lfeed.'<link rel = "stylesheet" type = "text/css" href = "js/ebookcms.css" >';
	if (isset($get['admin']) || LOGGED) {echo $lfeed.'<script type = "text/javascript" src = "js/ebookrte.js"></script>';}
	echo $lfeed.'<script type = "text/javascript" src = "js/ebookcms.js"></script>'.PHP_EOL.chr(13);
}

// GENERATE KEY SECURITY FOR EDITION
function genKey($text = 'NewKey', $minLength = 4, $maxLength = 15) {
	$list = 'Jx3guPE8BY1aW5HfvCZLdrAK9mctziSF4bp2wRMqGlUyI6TnjkKVehsD7NX';
	$len = strlen($text); $val = '';
	srand((double)microtime()*100001);
	$seed = rand(100000+$len,999999);
	srand((double)microtime()*$seed*$len);
	$count = rand($minLength, $maxLength);
	for ($i = 0; $i < $count; $i++) {
		$num = rand(0, 58);
		$val = $i == 0 ? $list[$num] : $val.$list[$num];
	} unset($list, $num, $seed, $text, $minLength, $maxLength, $len, $count, $i);
	return $val;
}

// GENERATE PRIVATE KEY (list 64 unique chars [A-Za-z0-9-_])
function gen_PKey($length = 64) {
	if (!defined('YOUR_KEY')) {$key = 'aPO7IU8Yz6xTREWQc9ASDvFGHJb4KLnZXmCVB3NM5lkjhgfdsqwerty01u2iop-_';}
	else {$key = YOUR_KEY;} $str = ''; $k = 0;
	while (strlen($str) < $length-1) {
		srand((double) microtime() * 99999);
		$var = rand(1, strlen($key));
		$str .= isset($key[$var]) ? $key[$var] : '';
		$key = isset($key[$var]) ? str_replace($key[$var], '', $key) : $key;
	} return $str.$key;
}

// RETURN FILENAME
function getPHPName() { $value = '';
	$basename = getInfo('PageName');
	$position = strpos($basename, '.php');
	if ($position == 0) {return $value;}
	$value = strpos($basename,'.php') > 0 ? substr($basename, 0, strpos($basename,'.php') +4) : $basename;
	return $value;
}

// SHOW SECTION
function showSection($sectionName, $liOpen = '<ul>', $liClose = '</ul>', $liOpen1 = '<ul>', $liClose1 = '</ul>', $show = false) {
	$lang_id = load_value('lang'); $num = 0;
	if (LOGGED) {
		$gm = load_value('user_gm');
		if (strlen($gm)==1) {$gm = '0'.$gm;}
		$special = "AND (member_by LIKE '%00%' OR member_by LIKE '%".$gm."%')";
	} else {$special = "AND member_by LIKE '%00%'";}
	$query = "SELECT id, seftitle, title, sec_type, plug_id FROM ".PREFIX."sections 
		WHERE seftitle = ? AND enabled = 1 ".$special;
	if ($result = db() -> prepare($query)) {
		$result = dbbind($result, array($sectionName), 's');
		while ($r = dbfetch($result, true)) {
			switch($r['sec_type']) {
				case 1 : showPagesByID($r['id'], 0, $liOpen, $liClose, $show); break; // Pages
				case 2 : break; // Plugin
			} $num = 1;
		} unset($result);
	} if ($num == 0 && $show) {echo $liOpen.'<li><a href="#">'.t('no_section').'</a></li>'.$liClose;}
}

// SHOW ROOT PAGES (SECTION)
function showPagesByID($content_id, $type, $liOpen = '<ul>', $liClose = '</ul>', $show = false) {
	global $get, $acontent; $lfeed = PHP_EOL.chr(9).chr(9);
	$lang_id = load_value('lang'); $content = '';
	$query = "SELECT p.id, p.title, p.seftitle, l.page_id, l.id AS pid 
		FROM ".PREFIX."pages AS p 
		LEFT OUTER JOIN ".PREFIX."languages AS l 
			on p.lang_id = l.id
		WHERE (p.published = 1 OR p.published = 2) AND p.section_id = ? AND p.lang_id = ?
		ORDER BY p.order_content ASC";
	if ($result = db() -> prepare($query)) {
		$result = dbbind($result, array($content_id, $lang_id), 'ii');
		$https = defined('HTACCESS') && HTACCESS == true ? true : false;
		$page = $https ? webroot().'page/' : '?page='; $end = $https ? '/' : '';
		while($r = dbfetch($result, true)) {
			$current = isset($acontent['id']) && $acontent['id'] == $r['id'] ? ' class="current"' : '';
			if ($r['id'] == $r['page_id'] && $r['pid'] == 1) {
				$content .= '<li class="li'.$r['seftitle'].'">';
				$content .= '<a href="'.webroot().'"'.$current.'>'.$r['title'].'</a></li>'.$lfeed.chr(9);
			} else {
				$content .= '<li class="li'.$r['seftitle'].'">';
				$content .= '<a href="'.$page.$r['seftitle'].$end.'"'.$current.'>'.$r['title'].'</a></li>'.$lfeed.chr(9);
			}
		} unset($result);
		if (!empty($content)) {echo $liOpen.$lfeed.chr(9).$content.$liClose;}
		else if ($show) {echo $liOpen.'<li><a href="#">'.t('no_content').'</a></li>'.$liClose.$lfeed;}
	} echo PHP_EOL;
}

// SHOW ROOT PAGES (SECTION)
function showPageByName($name) {
	$query = "SELECT texto1 FROM ".PREFIX."pages WHERE (published = 1 OR published = 2) AND seftitle = ?";
	if ($result = db() -> prepare($query)) {
		$result = dbbind($result, array($name), 's');
		while($r = dbfetch($result, true)) {echo $r['texto1'];}
		unset($result);
	}
}

// SHOW SUB-CONTENT
function showSubContent($sectionName, $css = 'page', $option = false) { global $acontent;
	if (!$sectionName) {return;}
	$page = $css == 'page' ? get_css($css) : $css;
	$open_tags = tags($page, 'open');
	$close_tags = tags($page, 'close');
	$content_tags = tags($page, 'cbodyline');
	if (LOGGED) {$gm = load_value('user_gm'); if (strlen($gm) == 1) {$gm = '0'.$gm;} $member = "(member_by = '00' OR member_by = '$gm')";} 
	else {$member = "member_by = '00'";}
	$sectionID = retrieve_mysql("sections", "seftitle", "'$sectionName'", "id", " AND ".$member);
	$query = "SELECT p.*, u.realname
		FROM ".PREFIX."pages AS p
		LEFT OUTER JOIN ".PREFIX."users AS u ON u.id = p.author_id
		WHERE (p.published = 1 OR p.published = 2) AND p.section_id = ?";
	if ($result = db() -> prepare($query)) {
		$result = dbbind($result, array($sectionID['id']), 'i');
		if (!empty($open_tags) && $option != true) {echo format_tags($acontent, $open_tags);}
		while ($r = dbfetch($result, true)) {
			if (!empty($open_tags) && $option == true) {echo format_tags($r, $open_tags);}
			if (!empty($content_tags)) {echo format_tags($r, $content_tags);}
			if (!empty($close_tags) && $option == true) {echo format_tags($r, $close_tags);}
		} if (!empty($close_tags) && $option != true) {echo format_tags($acontent, $close_tags);}
		unset($result);
	}
}

// Fight Spam
function fightSpamRSA(){ global $get, $uri, $fightSpamRSA;
	if (!$fightSpamRSA || empty($fightSpamRSA)) {
		$vget = isset($uri[1]) ? implode(',', $get) : '';
		$vuri = isset($uri[1]) ? implode(',', $uri) : '';
		srand ((double) microtime() * 10000000);
		$random1 = rand(1, 9999); $sid = load_value('sid');
		$random2 = $random1 * 2 * SUM_ID + get_hsa();
		$ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP');
		$values  = '<input type="hidden" name="ip_address" value="'.erukka($ip, '', false).'">';
		$values .= '<input type="hidden" name="your_time" value="'.$_SERVER['REQUEST_TIME'].'" />';
		$values .= '<input type="hidden" name="x'.$sid.'" value="'.dechex(crc32(session_id())).'" />';
		$values .= '<input type="hidden" name="dateW" value="'.erukka(load_value('get_week')).'" />';
		$values .= '<input type="hidden" name="random" value="'.erukka(strval($random1)).'">';
		$values .= '<input type="hidden" name="'.'x'.erukka(strval($random1)).'" value="'.md5($random2).'">';
		$values .= '<textarea class="ghost" name="ghost" cols="1" rows="1" style="DISPLAY: none"></textarea>';
		$values .= '<a href="?submit=true" class="ghost" style="DISPLAY: none"></a>';
		$parent = $get && isset($get['parent']) ? $get['parent'] : '';
		$values .= '<input type="hidden" name="parent" value="'.$parent.'">';
		$tries = $_SESSION && isset($_SESSION[HURI][erukka('tries')]) ? $_SESSION[HURI][erukka('tries')] : 0;
		$values .= '<input type="hidden" name="'.erukka('tries').'" value="'.$tries.'">';
		$values .= '<input type="hidden" name="'.erukka('get').'" value="'.$vget.'">';
		$values .= '<input type="hidden" name="'.erukka('uri').'" value="'.$vuri.'">';
		$fightSpamRSA = $values;
	} echo $fightSpamRSA;
	if ($get && isset($get['id'])){echo '<input type="hidden" name="'.erukka('id').'" value="'.$get['id'].'">';}
	if ($get && isset($get['lang'])){echo '<input type="hidden" name="lang" value="'.$get['lang'].'" />';}
	
}

// CHECK SPAM
function checkSpamRSA(){ global $get, $last, $lget, $luri;
	$valid = false;
	if ($_POST) {
		$sid = load_value('sid');
		$ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP');
		$vip = $ip == drukka($_POST['ip_address']) ? true : false;
		$time = isset($_POST['your_time']) ? $_POST['your_time'] : 0;
		$vsid = $_POST['x'.$sid] == dechex(crc32(session_id())) ? true : false;
		$gweek = erukka(load_value('get_week')) == $_POST['dateW'] ? true : false;
		$random = $_POST['random'];
		$random1 = drukka($random) * 2 * SUM_ID + get_hsa();
		$random2 = $_POST['x'.$random];
		$vrandom = md5($random1) == $random2 ? true : false;
		$ghost = empty($_POST['ghost']) ? true : false;
		$date_keys = load_value('date_keys');
		$date = isset($get[$date_keys]) ? $get[$date_keys] : null;
		$vdate = drukka($date) == date('d-m-Y') ? true : false;
		$lget = explode(',', clean($_POST[erukka('get')]));
		$luri = explode(',', clean($_POST[erukka('uri')]));
		if (isset($lget)) {
			for ($i=0; $i < count($luri); $i++) {
				$last[$luri[$i]] = isset($lget[$i]) ? $lget[$i]: '';
			}
		}
		if ($vrandom && $ghost && $vsid && $gweek && $vip && $vdate && $time) {$valid = true;}
	} return $valid;
}

// SEARCH FORM
function searchform($classform = 'search_engine', $classtext = 'text', $classbutton = 'submit', $show_btn = true) {
	$url = pre_seao('search', 'true', false, true).load_value('durl');
	echo '<style ';
	echo (HTML5 ? 'scoped' : 'type="text/css"').'>.ghost {display: none; border:0; padding:0;}</style>';
	echo '<form id="search_engine" class="'.$classform.'" method="post" action="'.$url.'" ';
		echo 'accept-charset="'.load_cfg('web_charset').'">';
		echo '<input class="'.$classtext.'" name="search" type="text" id="skeywords" ';
		echo 'value="'.t('search_keywords').'" title="'.t('search_title').'" ';
			echo 'onfocus="document.forms[\'search_engine\'].skeywords.value=\'\'" ';
			echo 'onblur="if (document.forms[\'search_engine\'].skeywords.value == \'\')';
			echo 'document.forms[\'search_engine\'].skeywords.value=\''.t('search_keywords').'\'" />';
		fightSpamRSA();
		if ($show_btn) {
			echo '<input type="submit" class="'.$classbutton.'" name="'.erukka('submit').'" ';
			echo 'value="'.t('search_button').'" />';
		}
	echo '</form>';
}

// SEARCH
function search() { global $get;
	if ($_POST && checkSpamRSA()) {
		echo '<h2>'.t('search_results').'</h2><br />';
		$search = clean($_POST['search']);
		$keywords = explode(' ', $search);
		$lang = isset($_POST['lang']) ? pre_seao('lang', $_POST['lang'], false) : '';
		$query = 'SELECT p.id, p.seftitle, p.title, p.date_upd, p.ptype, count (p.id) AS total
			FROM '.PREFIX.'pages AS p WHERE ';
		for ($i = 0; $i < count($keywords); $i++) {
			$query = $query.' p.title LIKE "%'.$keywords[$i].'%" OR p.texto1 LIKE "%'.$keywords[$i];
			$query .= '%" OR p.dmeta LIKE "%'.$keywords[$i].'%" OR p.keywords LIKE "%'.$keywords[$i].'%"';
			if ($i+1 < count($keywords)) {$query .= ' || ';}
		}
		if ($result = db() -> query($query)) {
			$found = 0; $text = '';
			while ($r = dbfetch($result)) {
				$date = date(load_cfg('date_format'), strtotime($r['date_upd']));
				$link = $r['id'] == 1 && $r['ptype'] == 0 ? '' : webroot();
				$link .= pre_seao('page', $r['seftitle'], false).$lang;
				$text .= '<p><a href="'.$link.'" title="'.$r['title'].'">'.$r['title'].'</a> - '.$date.'</p>';
				if ($r['total'] !=0) {$found++;}
			} $text = '<p>'.t('resultsfound1').' '.$found.' '.t('results').'</p>'.$text;
			if ($found == 0) {echo '<p>'.t('noresults').': <strong>'.$search.'</strong></p>';} else {echo $text;}
		}
	} else if ($_POST){$_SESSION[HURI][md5(POST_ID.'try_hack')] = true;}
} 

// LOAD SOME VALUES
function load_value($field, $options = '') { global $acontent, $get;
	if ($field == 'date_keys' || $field == 'durl') {
		$date_keys = explode(',','a,m,J,U,g,r,F,D,g,q,j,d,u,n,N,J,S,K,A,b,c,z,q,a,I,Z,m,j,U,y,t,J');
		$month_keys = explode(',','k,o,i,e,J,h,E,w,Q,m,N,p,Z');
	}
	switch ($field) {
		case 'sid': return substr(session_id(), 1, 10); break;
		case 'lang':
			if (isset($_POST['lang_id'])) {$value = drukka(clean($_POST['lang_id']));} else
			if (isset($acontent['lang_id'])) {$value = $acontent['lang_id'];} else
			if (isset($get['lang'])) {$value = $get['lang'];}
			if (empty($value) || $value == 0) {$value = 1;} break;
		case 'getfilename' : $value = substr($options, 0, strpos($options, '.')); break;
		case 'user_id':
			$value = LOGGED && $_SESSION && isset($_SESSION[HURI]['user_id']) ? 
				drukka($_SESSION[HURI]['user_id']) : 0; break;
		case 'user_ks':
			$value = LOGGED && $_SESSION && isset($_SESSION[HURI][erukka('user_security')]) ? 
				$_SESSION[HURI][erukka('user_security')] : null; break;
		case 'user_gm':
			$value = LOGGED && $_SESSION && isset($_SESSION[HURI][erukka('group_member')]) ? 
				drukka($_SESSION[HURI][erukka('group_member')]) : null; break;
		case 'get_week':
			$week_keys = explode(',','y,u,h,r,T,f,e,Q,A,c,n,L,P,O,g,f,D,A,S,h,g,F,r,e,S,A,Q,v,X,D,G,H,J,K,I,O,Y,r,d,f,E,S,D,h,j,K,G,F,p,O,T,R,f');
			return $week_keys[(int)date('W')]; break;
		case 'date_keys':
			return $date_keys[(int)date('j')].$month_keys[(int)date('n')];
		break;
		case 'durl':
			$date_keys = $date_keys[(int)date('j')].$month_keys[(int)date('n')];
			$date_keys = pre_seao($date_keys, erukka(date('d-m-Y'),'', false));
			return $date_keys;
		default: return null;
	} return $value;
}

// CHECK IF ALLOWED
function isAllowed($field, $cfg = true, $getlist = '', $all = false) {
	$result = false; $gm = load_value('user_gm');
	if (!LOGGED || empty($gm)) {return $result;}
	if (strlen($gm) == 1) {$gx = '0'.$gm;}
	if ($cfg) {$getlist = load_cfg($field);}
	$list = explode(',', $getlist);
	$allowed = $all != false && in_array('00', $list) ? true : false;
	$result = in_array($gx, $list) || $gm == 1 || $allowed ? true : false;
	return $result;
}

// CHECK GROUP RIGHTS
function isGroupAllowed($data, $field = 'admin_by', $all = false) { global $get;
	$result = false; $gm = load_value('user_gm');
	$id = isset($get['tid']) ? $get['tid'] : 0;
	if ((!LOGGED || empty($gm)) && $all != true) {return $result;}
	if (strlen($gm) == 1) {$gx = '0'.$gm;} else {$gx = $gm;}
	$gmlist = isset($data[$field]) ? explode(',', $data[$field]) : array('01');
	$allowed = $all != false && in_array('00', $gmlist) ? true : false;
	$result = $gm == 1 || in_array($gx, $gmlist) || $allowed ? true : false;
	return $result;
}

// LOAD PLUGIN DATA
function get_permissions($id) {
	$query = "SELECT s.title, s.admin_by, s.add_content, s.edit_by, s.delete_by, s.member_by, s.options_by, s.group_by
		FROM ".PREFIX."sections as s
		LEFT OUTER JOIN ".PREFIX."pages AS p
			on p.section_id = s.id
		WHERE p.id = '$id'";
	if ($result = db() -> query($query)) {
		$r = dbfetch($result); unset($result);
	} return $r;
}

// YOU GOT LIST CHECK IS TRUE OR FALSE TO EDIT
function admin_allowed($admin_by) { $result = false;
	if (LOGGED) {
		$gm = load_value('user_gm'); if (strlen($gm) == 1) {$gx = '0'.$gm;}
		$list = explode(',', $admin_by);
		if (in_array($gm, $list)) {$result = true;}
	} return $result;
}

// LANG LIST
function lang_list() {
	$query = "SELECT l.id, l.prefixo, l.use_prefix, l.image_li, p.title AS ptitle, p.seftitle AS pseftitle, p.id AS pid 
		FROM ".PREFIX."languages AS l, ".PREFIX."pages AS p 
		WHERE enabled = 1 AND l.page_id = p.id ORDER BY l.order_content";
	if ($result = db() -> query($query)) {
		echo '<ul>';
		while ($r = dbfetch($result)) {
			$url = $r['id'] == 1 ? '' : pre_seao('page', $r['pseftitle'], false, true).pre_seao('lang', $r['id']);
			if (isset($r['use_prefix']) && $r['use_prefix'] != 1) {
				echo '<li><a href="'.$url.'">'.$r['prefixo'].'</a></li>';
			} else {
				echo '<li><a href="'.$url.'">';
				echo '<img src="'.webroot().$r['image_li'].'" alt="'.$r['image_li'].'" />'.'</a></li>';
			}
		} echo '</ul>';
		unset($result);
	} else {echo '<ul><li>'.t('not_defined').'</li></ul>';}
}

// LOGIN LINK
function loginlink($li = true, $personal = true) { global $get;
	$lang_id = load_value('lang');
	$tag_li = $li ? '<li>' : '';
	$tag_cli = $li ? '</li>' : '';
	if (!LOGGED) {
		$current = isset($get['login']) && $get['login'] == 'true' ? ' class="current"' : '';
		$login = pre_seao('login', 'true', false, true);
		if (isset($get['parent'])) {$parent = pre_seao('parent', $get['parent']);} else {$parent = '';}
		if (isset($get['tid'])) {$tid = pre_seao('tid', $get['tid']);} else {$tid = '';}
		if (isset($get['vkey'])) {$vkey = pre_seao('vkey', $get['vkey']);} else {$vkey = '';}
		if ($lang_id != 1) {$lang = pre_seao('lang', $lang_id);} else {$lang = '';}
		$register = pre_seao('register', 'true', false, true);
		echo $tag_li.'<a href="'.$login.$parent.$tid.$vkey.$lang.'"'.$current.'>'.t('login').'</a>'.$tag_cli;
		if (load_cfg('registration') === '1') {
			$current = isset($get['register']) && $get['register'] == 'true' ? ' class="current"' : '';
			echo $tag_li.'<a href="'.$register.$parent.$tid.$vkey.$lang.'"'.$current.'>'.t('register').'</a>'.$tag_cli;
		}
	} else {
		$ukey = load_value('user_ks'); if (!empty($ukey)) {$ukey = pre_seao('ukey', $ukey);}
		if (isset($get['lang'])) {$lang = pre_seao('lang', $get['lang']);} else {$lang = '';}
		if ($lang_id != 1) {$lang = pre_seao('lang', $lang_id);} else {$lang = '';}
		$ukey = LOGGED && $_SESSION && isset($_SESSION[HURI][erukka('user_security')]) ? 
			pre_seao('ukey', $_SESSION[HURI][erukka('user_security')]) : '';
		$current = isset($get['admin']) && $get['admin'] == 'root' ? ' class="current"' : '';
		if (isAllowed('root_access')) {
			echo $tag_li.'<a href="'.pre_seao('admin', 'root', false, true).$ukey.$lang.'"'.$current.'>'.t('admin').'</a>'.$tag_cli;
		} $current = isset($get['admin']) && $get['admin'] == ' personal_data' ? ' class="current"' : '';
		if ($personal) {
			echo $tag_li.'<a href="'.pre_seao('admin', 'personal_data', false, true).$ukey.$lang.'"';
			echo $current.'>'.t('personal_data').'</a>'.$tag_cli;
		} $current = isset($get['logout']) && $get['logout'] == 'true' ? ' class="current"' : '';
		echo $tag_li.'<a href="'.pre_seao('logout', 'true', false, true, false).'">'.t('logout').'</a>'.$tag_cli;
	}
}

// UNICODE UTF-8 CONVERSION
function convert_utf8($text, $code = 195, $mode = true) {
	$r = chr($code);
	$x1 = array(
/*A*/	$r.chr(160),$r.chr(161),$r.chr(162),$r.chr(163),$r.chr(164),$r.chr(165),$r.chr(166),$r.chr(128),$r.chr(129),
		$r.chr(130),$r.chr(131),$r.chr(132),$r.chr(133),$r.chr(134),
/*E*/	$r.chr(168),$r.chr(169),$r.chr(170),$r.chr(171),$r.chr(136),$r.chr(137),$r.chr(138),$r.chr(139),
/*I*/	$r.chr(172),$r.chr(173),$r.chr(174),$r.chr(175),$r.chr(140),$r.chr(141),$r.chr(142),$r.chr(143),
/*O*/	$r.chr(178),$r.chr(179),$r.chr(180),$r.chr(181),$r.chr(182),$r.chr(146),$r.chr(147),$r.chr(148),
		$r.chr(149),$r.chr(150),
/*U*/	$r.chr(185),$r.chr(186),$r.chr(187),$r.chr(188),$r.chr(153),$r.chr(154),$r.chr(155),$r.chr(156),
		$r.chr(135),$r.chr(145),$r.chr(152),$r.chr(157),$r.chr(159),$r.chr(167),$r.chr(176),$r.chr(177),
		$r.chr(144),$r.chr(158),$r.chr(176),$r.chr(189),$r.chr(190));
	$x2 = array(
/*A*/	'&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&Agrave;','&Aacute;','&Acirc;',
		'&Atilde;','&Auml;','&Aring;','&AElig;',
/*E*/	'&egrave;','&eacute;','&ecirc;','&euml;','&Egrave;','&Eacute;','&Ecirc;','&Euml;',
/*I*/	'&igrave;','&iacute;','&icirc;','&iuml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;',
/*O*/	'&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;',
/*U*/	'&ugrave;','&uacute;','&ucirc;','&uuml;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;',
		'&Ccedil;','&Ntilde;','&Oslash;','&Yacute;','&szlig;','&ccedil;','&oslash;','&ntilde;',
		'&ETH;','&THORN;','&eth;','&yacute;','&thorn;','&quot;');
	if ($mode != false) {$text = str_replace($x1, $x2, $text);}
	else {$text = str_replace($x2, $x1, $text);}
	$text = str_replace(array('\\\'', '\\"'), array('&#39;','"'), $text);
	$text = preg_replace("#(\[video=youtube\])(http\:\/\/www\.youtube\.com\/embed\/)(\w+)(\[\/video\])#siU", 
		"<iframe width=\"640\" height=\"480\" src=\"http://www.youtube.com/embed/$3\" frameborder=\"0\" allowfullscreen></iframe>", $text);
	return $text;
}

// FORMAT TAGS
function format_tags($content, $tags, $pagelink = false, $sef = 'page', $admin_sef = 'pages') { global $get, $uri, $acontent;
	$gm = load_value('user_gm');
	$ukey = pre_seao('ukey', load_value('user_ks'));
	if (strlen($gm) == 1) {$gx = '0'.$gm;} else {$gx = $gm;}
	if (!LOGGED) {$tags = str_replace(array('$edit_page','$edit_comment','$delete_comment'), '', $tags);}
	$author = isset($content['realname']) ? rdecrypt1($acontent['realname']) : '';  $readmore = ''; $limage = '';
	$title = isset($content['title']) ? $content['title'] : '';
	$texto1 = isset($content['texto1']) ? $content['texto1'] : '';
	$texto2 = isset($content['texto2']) ? $content['texto2'] : '';
	$long_text = !empty($text1) ? $text1 : '';
	$total_comments = isset($content['total_comments']) ? $content['total_comments'] : 0;
	$image = isset($content['image']) ? $content['image'] : '';	$img_link = $image;
	if (!empty($image)) {$image = '<img src="'.$image.'" alt="'.$content['title'].'" />';}
	if (!empty($image) && isset($content['seftitle'])) {
		$limage = '<a href="'.pre_seao('show', $content['seftitle'], false, true).'">'.$image.'</a>';
	} else if (!empty($image)) {$limage = $image;}
	$admin_sef = $admin_sef == 'pages' && isset($content['ptype']) && $content['ptype'] == 1 ? 'cpages' : $admin_sef;
	$link = isset($content['seftitle']) ? 
		' <a href="'.pre_seao($sef, $content['seftitle'], false, true).pre_seao('read', 'full').'">' : '';
	$plink = isset($content['seftitle']) ? ' <a href="'.pre_seao('page', $content['seftitle'], false, true).pre_seao('read', 'full').
		'" class="more">'.$content['title'].'</a>' : '';
	$more = isset($content['seftitle']) ? ' <a href="'.pre_seao($sef, $content['seftitle'], false, true).pre_seao('read', 'full').
		'" class="more">'.t('read_more').'</a>' : '';
	$seftitle = isset($content['seftitle']) ? pre_seao($sef, $content['seftitle'], false, true).pre_seao('read', 'full') : '';
	$sseftitle = isset($content['seftitle']) ? $content['seftitle'] : '';
	if ((strpos($texto1, '[break]') !== false && !isset($get['read'])) || $pagelink) {
		if (strpos($texto1, '[break]') !== false) {
			$txt = substr($texto1, 0, strpos($texto1, '[break]'));
			$txt = str_replace('[break]', '', $txt);
			$readmore .= t('read_more').'</a>';
			$texto1 = !empty($txt) ? $txt.'...'.t('divider').$link.$readmore : '';
		} else {
			$txt = substr($texto1, 0, 255);
			$npos = strripos($txt,' ');
			$txt = substr($txt, 0, $npos);
			$readmore .= t('read_more').'</a>';
			$texto1 = !empty($txt) ? cleanTxt($txt).'...'.t('divider').$link.$readmore : '';
		}
		$ltitle = !empty($title) ? $link.$content['title'].'</a>' : '';
	} else {$texto1 = str_replace('[break]', '', $texto1); $ltitle = '';}
	$texto1 = convert_utf8($texto1);
	if (!empty($text)) {$text = $text.'<br />';}
	$date1 = isset($content['date_reg']) ? $content['date_reg'] : '';
	$date1 = date('Y-m-d w H:i:s', strtotime($date1));
	$date2 = isset($content['date_upd']) ? $content['date_upd'] : '';
	$date2 = date('Y-m-d w H:i:s', strtotime($date2));
	$week_list = explode(',', t('week_names'));
	$month_list = explode(',',t('month_names'));
	$year1 = substr($date1, 0, 4); $year2 = substr($date2, 0, 4);
	$month1 = $month_list[substr($date1, 5, 2)-1]; $month2 = $month_list[substr($date2, 5, 2)-1];
	$day1 = substr($date1, 8, 2); $day2 = substr($date2, 8, 2);
	$week1 = $week_list[substr($date1, 11, 1)]; $week2 = $week_list[substr($date2, 11, 1)];
	$time1 = substr($date1, 13, 8); $time2 = substr($date2, 13, 8);
	$hour1 = substr($date1, 13, 2); $hour1 = ltrim($hour1);
	$hour2 = substr($date2, 13, 2); $hour2 = ltrim($hour2);
	$minute1 = substr($date1, 16, 2); $minute2 = substr($date2, 16, 2);
	$small_date1 = substr($date1, 0, 10); $small_date2 = substr($date2, 0, 10);
	$comments = t('comments'); $says = t('says');
	$comment = isset($content['comment']) ? showBBCodes($content['comment']) : '';
	$com_id = isset($content['comment_id']) ? $content['comment_id'] : '';
	$com_id = erukka($com_id, '', false);
	$num = isset($content['num']) ? $content['num'] : '';
	$level = isset($content['level']) ? $content['level'] : 0;
	$page_number = isset($get['comment_page']) ? pre_seao('comment_page', $get['comment_page']) : '';
	$pmsg = isset($content['show_pmsg']) && $content['show_pmsg'] == 1 ? pre_seao('pmessage', 'true', false, true) : '';
	$privatemsg = !empty($pmsg) ? ' <a href="'.$pmsg.pre_seao('pageid', erukka1($content['id'])).'">'.t('send_pm').'</a><br /> ' : '';
	if (isset($uri[0])) {
		if (isset($get['page'])) {$main_link = pre_seao('page', $get['page'], false, true);}
		else {
			$plugin = retrieve_mysql("plugins", "sef_file", "'$uri[0]'", "id", "");
			$sid = isset($plugin['id']) ? $plugin['id'] : 0;
			$main_link = pre_seao($uri[0], $get[$uri[0]], false, true);
			for ($i=1; $i<count($uri); $i++) {
				if (isset($uri[$i])) {$main_link .= pre_seao($uri[$i], $get[$uri[$i]]);}
			}
		}
	} else if (isset($acontent['home_sef'])) {
		$main_link = pre_seao('page', $acontent['home_sef'], false, true);
	} else {$main_link = '';}
	if (load_cfg('comments_logged') === '1' && !LOGGED) {
		$link_admin = pre_seao('login', 'true', false, true);
		if (isset($get['page'])) {$link_admin .= pre_seao('parent', $get['page']);}
		else if (isset($acontent['home_sef'])) {$link_admin .= pre_seao('parent', $get['home_sef']);}
		$reply = isset($content['comments']) && $content['comments'] == '1' && !isset($get['cf']) ? 
			'<a href="'.$link_admin.'">'.t('reply').'</a>' : '';
		if ($level >= load_cfg('level_comments')) {$reply = '';}
		$report = ''; $edit_comment = ''; $delete_comment = '';
		$num_id = '<a href="'.$main_link.$page_number.'#comment'.$num.'" id="comment'.$num.'">'.$num.'</a>';
	} else {
		$num_id = '<a href="'.$main_link.$page_number.'#comment'.$num.'" id="comment'.$num.'">'.$num.'</a>';
		# REPLY
		$link_reply = !empty($main_link) ? $main_link.pre_seao('cf', $num).pre_seao('id', $com_id) : '';
		$reply = isset($content['comments']) && $content['comments'] == '1' && !isset($get['cf']) ? 
			'<a href="'.$link_reply.$page_number.'#comment'.$num.'">'.t('reply').'</a>' : '';
		if ($level >= load_cfg('level_comments')) {$reply = '';}
		# REPORT ERROR OR SPAM
		$report_link = !empty($main_link) ? $main_link.pre_seao('ra', '1').pre_seao('id', $com_id) : '';
		$report1 = isset($content['report_abuse']) && $content['report_abuse'] == '0' ? 
			' <a href="'.$report_link.'#comment'.$num.'">'.t('report_spam').'</a> ' : '';
		$report = !empty($reply) && !empty($report1) ? ' '.t('divider').' '.$report1 : $report1;
		# EDIT COMMENT
		$edit_link = isset($get['page']) ? 
			pre_seao('admin', 'comment', false, true).pre_seao('action', 'edit') : 
			pre_seao('admin', 'pcomment', false, true).pre_seao('action', 'edit').pre_seao('fp', '4');
		$edit_link .= isset($content['comment_id']) ? pre_seao('tid', $content['comment_id']) : '';
		$edit_link .= isset($content['comks']) ? pre_seao('tks', erukka($content['comks'], '', false)) : '';
		$edit_link .= isset($get['page']) ? $ukey.pre_seao('parent', $get['page']) : $ukey;
		$edit_comment = isset($content['comks']) ? ' <a href="'.$edit_link.'">'.t('edit_comments').'</a>' : '';
		$edit_comment = !empty($edit_comment) ? ' '.t('divider').' '.$edit_comment : $edit_comment;
		# DELETE COMMENT
		$delete_link = pre_seao('admin', 'comment', false, true).pre_seao('action', 'delete');
		$delete_link .= isset($content['comment_id']) ? pre_seao('tid', $content['comment_id']) : '';
		$delete_link .= isset($content['comks']) ? pre_seao('dks', erukka($content['comdel'], '', false)) : '';
		$delete_link .= isset($get['page']) ? $ukey.pre_seao('parent', $get['page']) : $ukey;
		$delete_comment = isset($content['comdel']) ? 
			' '.t('divider').' <a href="'.$delete_link.
			'" onClick="return confirm(\''.t('delete_comments').'\');">'.t('delete').'</a>' : '';
	}
	$username = isset($content['username']) ? $content['username'] : '';
	$username = drukka($username);
	$web_link = isset($content['web_link']) ? $content['web_link'] : '';
	$says_web = !empty($web_link) ? 
		'<a href="'.drukka($web_link).'" rel="external nofollow"> '.t('says').'</a>': t('says');
	$default = getInfo('ROOT')."js/ebookcms/unknown.png";
	$avatar = isset($content['avatar']) ? $content['avatar'] : '';
	$avatar = drukka($avatar);
	$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($avatar)))."?d=".urlencode($default)."&amp;s=40";
	$grav_url = '<img src="'.$grav_url.'" alt="" />';
	# Functions
	$to_find = array('$title', '$ltitle', '$text1', '$text2', '$author', '$comments', '$comment','$id_comment', '$username','$web_link', '$avatar', '$reply_comment', '$report_spam', '$edit_comment', '$delete_comment', '$image', '$limage', '$date1', '$small_date1', '$day1', '$weekday1', '$month1', '$year1', '$time1', '$hour1', '$minute1', '$date2', '$small_date2', '$day2', '$weekday2', '$month2', '$year2', '$time2', '$hour2', '$minute2', '$says_web', '$readmore', '$seftitle', '$long_text', '$total_comments', '$sseftitle', '$plink', '$img_link');
	$replace = array($title, $ltitle, $texto1, $texto2, $author, $comments, $comment, $num_id, $username, $web_link, $grav_url, $reply, $report, $edit_comment, $delete_comment, $image, $limage, $date1, $small_date1, $day1, $week1, $month1, $year1, $time1, $hour1, $minute1, $date2, $small_date2, $day2, $week2, $month2, $year2, $time2, $hour2, $minute2, $says_web, $more, $seftitle, $long_text, $total_comments, $sseftitle, $plink, $img_link);
	$tag = str_replace($to_find, $replace, $tags);
	$to_find = array('$date', '$small_date', '$day', '$weekday', '$month', '$year', '$time', '$hour', '$minute', '$privatemsg');
	$replace = array($date1, $small_date1, $day1, $week1, $month1, $year1, $time1, $hour1, $minute1, $privatemsg);
	$tag = str_replace($to_find, $replace, $tag);
	# Some text
	$to_find = array('$posted_by', '$at', '$divider');
	$replace = array(t('posted_by'), t('at'), t('divider'));
	$tag = str_replace($to_find, $replace, $tag);
	if (isset($content['first']) && $content['first'] == '1') {$tag = str_replace('<li>', '<li class="first">', $tag);}
	# Edit
	if (LOGGED && isset($content) && isset($_SESSION[HURI][erukka('user_security')])) {
		if (isset($content['id']) && isset($content['key_security'])) {
			if ($uri[0] == 'page') {$fp = 2;} else {$fp = 1;}
			$edit_page = '<a href="'.pre_seao('admin', $admin_sef, false, true).pre_seao('action', 'edit');
			$edit_page.= pre_seao('tid', $content['id']).pre_seao('tks', erukka($content['key_security'], '', false)).$ukey;
			$edit_page.= pre_seao('fp', $fp).'">'.t('edit').'</a>';
			if (isset($content['admin_by']) && isset($content['only_author'])) {
				$gmlist = explode(',', $content['admin_by']);
				if ($gm != 1 && (!in_array($gx, $gmlist) || ($content['only_author'] == 1 && 
					in_array($gx, $gmlist) && load_value('user_id') != $content['author_id']))) {$edit_page = '';}
			}
		} else {$edit_page = '';}
		$to_find = array('$edit_page');
		$replace = array($edit_page);
		$tag = str_replace($to_find, $replace, $tag);
	} return $tag;
}

// SET TO ONE ERROR
function set_error($error = '404') {
	switch ($error) {
		case '401': header('HTTP/1.1 401 Unauthorized'); $msg = t('unauthorized'); 	$txt = t('error_401'); break;
		case '404': header('HTTP/1.1 404 Not Found'); 	 $msg = t('not_found'); 	$txt = t('error_404'); break;
		default: $msg = 'Error'; $txt = 'Error not specified';
	}
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
		<head>
			<title>Error: '.$msg.'</title>
			<meta http-equiv="refresh" content="0; url='.pre_seao('error', $error, false, true).'" />
		</head>
		<body>
			<p>'.$txt.'</p>
		</body>
	</html>';
	exit;
}

// SHOW ERROR INFORMATION
function show_error($error = '404') {
	switch ($error) {
		case '401': header('HTTP/1.1 401 Unauthorized');	echo '<h1>'.t('unauthorized').'</h1><p>'.t('error_401').'</p>'; break;
		case '404': header('HTTP/1.1 404 Not Found'); 		echo '<h1>'.t('not_found').'</h1><p>'.t('error_404').'</p>'; break;
	}
}

// PAGINATOR
function paginator($pageNum, $maxPage, $extend, $link, $page = 'pn') { $link .= $extend;
	if ($pageNum > 1) {
		$goTo = $link;
		$prev = (($pageNum-1) == 1 ? $goTo : $link.pre_seao($page, ($pageNum - 1))).'" title="'.t('page').' '.
			($pageNum - 1).'">&lt; '.t('previous_page').'</a> ';
		$first = $goTo.'" title="'.t('first_page').' '.t('page').'">&lt;&lt; '.t('first_page').'</a>';
	} else {
		$prev = '&lt; '.t('previous_page');
		$first = '&lt;&lt; '.t('first_page');
	}
	if ($pageNum < $maxPage) {
		$next = $link.pre_seao($page, ($pageNum + 1)).'" title="'.t('page').' '.($pageNum + 1).'">'.t('next_page').' &gt;</a> ';
		$last = $link.pre_seao($page, $maxPage).'" title="'.t('last_page').' '.t('page').'">'.t('last_page').' &gt;&gt;</a> ';
	} else {
		$next = t('next_page').' &gt; ';
		$last = t('last_page').' &gt;&gt;';
	}
	echo '<div class="paginator">'.$first.' '.$prev.' <strong>['.$pageNum.'</strong> / <strong>'.
		$maxPage.']</strong> '.$next.' '.$last.'</div>';
}

// FUNCTION str_ireplace to php4
function ireplace($needle, $str, $source) {
	if (!function_exists('str_ireplace')) {
		$value = preg_quote($needle, '/');
		return preg_replace("/$needle/i", $str, $source);
	} else return str_ireplace($needle, $str, $source);
}

// CLEAN COMMENTS FOR XSS ATTACKS WITH BBCodes
function showBBCodes($text) {
	$text = str_ireplace(array("\n", "<br />", "<br>", "<BR>"), '[br]', $text);
	$text = preg_replace("#[\<](p|div|iframe)(.*)[\>](.*)[\<]/(p|div|iframe)[\>]#siU", "[$1$2]$3[/$4]", $text);
	$text = ireplace(array('<','>','<script>','</script>'), array('&lt;','&gt;','[script]','[/script]'), $text);
	$text = preg_replace("#[\[](b|u|i)[\]](.*)[\[]/(b|u|i)[\]]#siU", "<$1>$2</$3>", $text);
	$text = preg_replace("#[\[]color[\=](\#([a-f0-9]{3,6}))(.*)[\]](.*)[\[][\/]color[\]]#siU", "<span style=\"color:#$2\">$4</span>", $text);
	$text = preg_replace("#[\[]color[\=](black|red|yellow|pink|green|orange|purple|blue|beige|brown|teal|navy|maroon|limegreen|white|cyan|)[\]](.*)[\[][\/]color[\]]#siU", 
		"<span style=\"color:$1\">$2</span>", $text);
	$text = preg_replace("#[\[]url[\=[\s]*javascript(.*)[\]](.*)[\[]/url[\]]#siU", "<a href=\"?error=404\">XSS detected</a>", $text);
	$text = preg_replace("#[\[]url[\=](.*)[\]](.*)[\[]/url[\]]#siU", "<a href=\"$1\" title=\"$2\">$2</a>", $text);
	$text = preg_replace("#[\[]img(.*)width[\=][\[]xq[\]](.*)[\[]xq[\]]\sheight[\=][\[]xq[\]](.*)[\[]xq[\]]](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))[\[][\/]img[\]]#siU", "<img src=\"$4\" width=\"$2\" height=\"$3\"/>", $text);
	$text = preg_replace("#[\[]img[\]](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))[\[][\/]img[\]]#siU", "<img src=\"$1\" />", $text);
	$text = preg_replace("#[\[]img(.*)width[\=][\[]xq[\]](.*)[\[]xq[\]][\]](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))[\[][\/]img[\]]#siU", "<img src=\"$3\" width=\"$2\" />", $text);
	$text = preg_replace("#[\[]img(.*)height[\=][\[]xq[\]](.*)[\[]xq[\]][\]](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))[\[][\/]img[\]]#siU", "<img src=\"$3\" height=\"$2\" />", $text);
	$text = str_replace(array('[br]'), array('<br />'), $text);
	$text = preg_replace("#[\[](a|p|div|iframe)(.*)[\]](.*)[\[]/(a|p|div|iframe)[\]]#siU", "<$1$2>$3</$4>", $text);
	return $text;
}

// PREPARE TEXT TO MYSQL
function clean_mysql($text) {
	if ((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || 
		(ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase')) != "off"))) {
			$text = stripslashes(addslashes($text));
			$text = str_replace('\\\"', '"', $text);
	} else { // If use another RTE you must clean text before save text, ebookrte doesn`t need
		$text = urldecode($text);
		$text = str_replace('\\\"', '"', $text);
		/*$find = array('<', '>');
		$replace = array('&lt;', '&gt;');
		$text = str_replace($find, $replace, $text);*/
	} return $text;
}

// SHOW COMMENTS
function show_comment($page_id, $ptype, $reply, $level, $num = '', $total_comments, $current, $perpage, $commentable = false ) {
	global $get, $uri, $acontent; $sub = ''; $root = webroot();
	$id = isset($get['id']) ? drukka($get['id']) : null;
	if (isAllowed('comments_edit')) {$sub = "a.key_security AS comks, ";}
	if (isAllowed('comments_delete')) {$sub .= "a.delkey AS comdel, ";}
	$order = load_cfg('comments_order');
	$query = "SELECT a.id AS comment_id, a.name AS username, a.url AS web_link, a.comment, a.avatar_email AS avatar, 
		a.date_reg, a.report_abuse, a.ptype, $sub count( DISTINCT b.id ) AS total 
		FROM ".PREFIX."comments AS a
		LEFT OUTER JOIN ".PREFIX."comments AS b ON b.reply_id = a.id AND b.approved = 1 
		WHERE a.reply_id = ? AND a.approved = 1 AND a.pcontent_id = ? AND a.ptype = ? AND a.spam = 0 AND a.block_email = 0
		GROUP BY a.id 
		ORDER BY a.id ".$order;
	$query .= ' LIMIT '.($current - 1) * $perpage.','.$perpage;
	$k = ($current-1) * $perpage + 1;
	if ($result = db() -> prepare($query)){ $total = 1;
		$result = dbbind($result, array($reply, $page_id, $ptype), 'iii');
		$id = isset($get['id']) ? drukka($get['id']) : '';
		# TAGS
		$tbody = tags('comments','tbody');
		$body = tags('comments','body');
		$bbody = tags('comments','bbody');
		while ($r = dbfetch($result, true)) {
			$n = !empty($num) ? $num.'-'.$k : $k;
			$r['num'] = $n; $r['level'] = $level;
			echo format_tags($acontent, $tbody);
			$r['comments'] = $ptype == 0 ? $acontent['comments'] : $commentable;
			echo format_tags($r, $body);
			if (isset($get['cf']) && $get['cf'] == $n && $id == $r['comment_id']) {comment_form($ptype);}
			if ($r['total'] > 0) {
				show_comment($page_id, $ptype, $r['comment_id'], $level+1, $n, $total_comments, $current, $perpage, $commentable);
			} $total = $total + $r['total'];
			$k = $k + 1; $total = $total+1;
			echo format_tags($acontent, $bbody);
			# REPORT ERROR OR SPAM BY USER
			if (isset($get['ra']) && $get['ra'] == 1 && $r['comment_id'] == $id) {
				$comment = isset($get['id']) ? '#comment'.$n : '';
				$rqwr = "SELECT id FROM ".PREFIX."comments 
					WHERE id = ? AND report_abuse = 0 AND approved = 1 AND spam = 0 AND block_email = 0";
				if ($rx = db() -> prepare($rqwr)) {
					$rx = dbbind($rx, array($id), 'i');
					$data = dbfetch($rx, true);
					if ($data) {
						$sql =  "UPDATE ".PREFIX."comments SET report_abuse = ? WHERE id = ?";
						if ($q = db() -> prepare($sql)){ $q = dbbind($q, array('1', $id), 'ii'); unset($q);}
						if (isset($uri[0])) {
							if (isset($get['page'])) {$url = pre_seao('page', $get['page'], false, true);}
							else {
								$plugin = retrieve_mysql("plugins", "sef_file", "'$uri[0]'", "id", "");
								$sid = isset($plugin['id']) ? $plugin['id'] : 0;
								$url = pre_seao($uri[0], $get[$uri[0]], false, true);
								for ($i=1; $i<count($uri); $i++) {
									if (isset($uri[$i]) && $uri[$i] != 'id' && $uri[$i] != 'ra') {
										$url .= pre_seao($uri[$i], $get[$uri[$i]]);
									}
								}
							} $url .= $comment;
						} echo '<meta http-equiv="refresh" content="0; url='.$url.'">';
					} unset($rx);
				}
			}
		}
	} unset($result);
}

// YOU NEED TO LOGIN TO COMMENT
function needto_Login() { global $get, $uri;
	if (load_cfg('comments_logged') === '1' && !LOGGED) {
		$link = pre_seao('login', 'true', false, true);
		if (isset($get['page'])) {$link .= pre_seao('parent',$get['page']);}
		else {
			if (!empty($uri[0]) && isset($get[$uri[0]])) {$link .= pre_seao($uri[0], $get[$uri[0]]);}
			if (isset($get['action'])) {$link .= pre_seao('action', $get['action']);}
			if (isset($get['tid'])) {$link .= pre_seao('tid', $get['tid']);}
			if (isset($get['vkey'])) {$link .= pre_seao('vkey', $get['vkey']);}
			if (isset($get['lang'])) {$link .= pre_seao('lang', $get['lang']);}
		}
		echo '<p class="center"><strong>'.t('comment1').'</strong>. ';
		echo '<a href="'.$link.'">'.t('sign_in').'</a> '.t('comment2').'. </p>';
		return true;
	} return false;
}

// COMMENT FORM
function comment_form($ptype = 0) { global $get, $acontent, $uri;
	if (needto_Login()) {return;}
	if ($ptype == 0) {$page = isset($get['page']) ? $get['page'] : $acontent['seftitle'];} else {$page = '';}
	$required = strtolower(t('required'));
	$comment_page = isset($get['comment_page']) ? pre_seao('comment_page', $get['comment_page']) : '';
	if ($ptype == 0) {
		$sid = 0;
		$action = pre_seao('page', $page, false, true).load_value('durl').$comment_page;
	} else {
		$plugin = retrieve_mysql("plugins", "sef_file", "'$uri[0]'", "id", "");
		$sid = isset($plugin['id']) ? $plugin['id'] : 0;
		if (isset($uri[0])) {$action .= pre_seao($uri[0], $get[$uri[0]], false, true);}
		for ($i=1; $i<count($uri); $i++) {if (isset($uri[$i])) {$action .= pre_seao($uri[$i], $get[$uri[$i]]);}}
		$action .= load_value('durl');
	}
	$charset = load_cfg('charset'); $cancel = '';
	if (LOGGED) {$akey = drukka1($_SESSION[HURI]['akey']);}
	if (isset($_SESSION['comment'][md5(POST_ID.'name')])) {$name = drukka($_SESSION['comment'][md5(POST_ID.'name')]);} 
		else {$name = '';}
	if (isset($_SESSION['comment'][md5(POST_ID.'email')])) {$email = $_SESSION['comment'][md5(POST_ID.'email')];} else {$email = '';}
	if (isset($_SESSION['comment'][md5(POST_ID.'website')])) {$website = $_SESSION['comment'][md5(POST_ID.'website')];} 
		else {$website = '';}
	unset($_SESSION['comment'][md5(POST_ID.'name')]);
	unset($_SESSION['comment'][md5(POST_ID.'email')]);
	unset($_SESSION['comment'][md5(POST_ID.'website')]);
	if (isset($get['cf'])) {
		$page_number = isset($get['comment_page']) ? pre_seao('comment_page', $get['comment_page']) : '';
		if ($ptype == 0) {$link = pre_seao('page', $get['page'], false, true).$page_number.'#comment'.$get['cf'];}
		else {
			for ($i = 0; $i < count($uri); $i++) {
				if (isset($uri[$i]) && isset($get[$uri[$i]]) && !empty($uri[$i]) && $uri[$i] != 'cf' && !empty($get[$uri[$i]])) {
					if ($i == 0) {$link = pre_seao($uri[$i], $get[$uri[$i]], false, true);}
					else {$link .= pre_seao($uri[$i], $get[$uri[$i]]);}
					
				}
			}
		}
		$cancel = '<p class="reply"><a href="'.$link.'">'.t('cancel_reply').'</a></p>';
	}
	# FORM
	echo '<form method="post" action="'.$action.'" id="commentform" name="commentform" accept-charset="'.$charset.'">';
	echo '<fieldset><h1>'.t('leave_comment').'</h1><br />'.$cancel;
	if (!LOGGED) {
		echo '<label for="name">'.t('name').' ('.$required.')</label>';
		echo '<input type="text" name="'.erukka('name').'" id="name" value="'.$name.'">';
		echo '<label for="email">'.t('email_notpublish').' ('.$required.')</label>';
		echo '<input type="text" name="'.erukka('email').'" id="email" value="'.$email.'">';
		echo '<label for="website">'.t('website').'</label>';
		echo '<input type="text" name="'.erukka('website').'" id="website" value="'.$website.'">';
	} else {
		echo '<input type="hidden" name="'.erukka('name').'" value="'.rdecrypt1($_SESSION[HURI]['realname']).'">';
		echo '<input type="hidden" name="'.erukka('email').'" value="'.drukka($_SESSION[HURI]['avatar'], $akey).'">';
		echo '<input type="hidden" name="'.erukka('website').'" value="'.drukka($_SESSION[HURI]['website']).'">';
	}
	echo '<textarea class="text" name="txt1" cols="1" rows="1"></textarea>';
	echo '<p class="msg_bbcode"><strong>'.t('use_bbcode').': </strong> [b][u][i][url][color][img]</p>';
	echo '<input type="hidden" name="'.erukka('ptype').'" value="'.$ptype.'">';
	echo '<input type="hidden" name="'.erukka('sid').'" value="'.$sid.'">';
	echo '<input type="submit" value="'.t('submit').'" class="buttons"></fieldset>';
	fightSpamRSA();
	echo '</form>';
}

// COMMENTS
function comment($page_id, $ptype, $cplugin) { global $get, $uri, $acontent;
	if (!isset($_SESSION[HURI][erukka('try_hack')]) && !$_POST) {
		$perpage = load_cfg('comments_limit');
		$query = "SELECT count(DISTINCT id) AS total 
			FROM ".PREFIX."comments 
			WHERE reply_id = 0 AND approved = 1 AND pcontent_id = $page_id AND ptype = $ptype AND spam = 0 AND block_email = 0";
		if ($result = db() -> query($query)) {while ($r = dbfetch($result)) {$total = $r['total'];} unset($result);}
		if (!isset($acontent)) {$acontent['total_comments'] = $total; $acontent['ptype'] = $ptype;}
		$total_comments = $perpage != 0 ? ceil($total/$perpage) : 0;
		$cp = isset($get['comment_page']) ? $get['comment_page'] : null;
		$current = isset($cp) && is_numeric($cp) ? $cp : (empty($cp) ? 1 : 0);
		if ($ptype == 0) {$commentable = isset($acontent['comments']) && ($acontent['comments'] == '1') ? 1 : 0;}
		else {$commentable = $cplugin;}
		if ($total > 0) {
			$comments_top = tags('comments', 'top');
			echo format_tags($acontent, $comments_top);
			show_comment($page_id, $ptype, 0, 0, '', $total_comments, $current, $perpage, $commentable);
			$comments_foot = tags('comments', 'bottom');
			echo format_tags($acontent, $comments_foot);
			$paginator_link = '<a href="'.pre_seao($uri[0], $get[$uri[0]], false, true);
			if ($total_comments>1) {paginator($current, $total_comments, '', $paginator_link, 'comment_page');}
		} if (!isset($get['cf']) && $commentable) {comment_form($ptype);} return;
		
	} else if ($_POST && checkSpamRSA()) {
		if (LOGGED) {$akey = drukka1($_SESSION[HURI]['akey']);}
		$name = cleanTxt($_POST[erukka('name')]);
		$_name = rencrypt1($name); $name = erukka($name);
		$ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP');
		$sid = isset($_POST[erukka('sid')]) ? clean($_POST[erukka('sid')]) : 0;
		$website = $_POST[erukka('website')];
		$website = (strlen($website) > 10 && preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', 
			$website)) ? $website : null;
		$email = $_POST[erukka('email')];
		$email = isEmailValid($email);
		$text = preg_replace("#(\n\r){2,}#sU", "", $_POST['txt1']);
		$text = str_replace(array("\n", "<br />", "<br>"), '[br]', $text);
		$text = str_replace("\r", "", $text);
		$text = preg_replace("#(\[br\]){1,}(.+?)#sU", "$2", $text);
		$text = clean_mysql($text);
		$_email = erukka($email, $akey, false);
		# VERIFY SPAM
		$verify_spam = retrieve_mysql("comments", "spam", 1, "id"," AND comment = '$text'");
		$block_user = retrieve_mysql("comments", "name", "'$name'", "id"," AND block_user = 1");
		$block_email = retrieve_mysql("comments", "avatar_email", "'$_email'", "id", " AND block_email = 1");
		# FIGHT SPAM
		if ($verify_spam || $block_user || $block_email) {
			echo '<div class="msgerror">';
			if ($verify_spam || !$ghost) {echo '<p>'.t('spambot_detected').'</p>';}
			if ($block_user || $block_email) {echo '<h1>'.t('error').'</h1><p>'.t('com_not_permited').'</p>';}
			echo '</div>';
			$date = date('d-m-Y H:i:s');
			$txt = t('try_hack').': '.$ip.' ('.$date.')'.PHP_EOL;
			if (file_exists("comments.txt")) {
				$current = file_get_contents("comments.txt");
				$current .= $txt;
				file_put_contents("comments.txt", $current);
				unset($current);
			} else {file_put_contents("comments.txt", $txt, FILE_APPEND | LOCK_EX);}
			$_SESSION[HURI][erukka('try_hack')] = 1; return;
		} $error = '';
		if (empty($name)) {$error .= '<li>'.t('empty_name').'</li>';}
		if (empty($email)) {$error .= '<li>'.t('empty_mail').'</li>';}
		if (empty($text)) {$error .= '<li>'.t('empty_text').'</li>';}
		if (!empty($error)) {
			echo '<div class="msgerror"><h2>'.t('error').'</h2><ul>'.$error.'</ul></div>';
			echo '<meta http-equiv="refresh" content="3; url='.pre_seao('page', $get['page'], false, true).'">';
			$_SESSION['comment'][md5(POST_ID.'name')] = $name;
			$_SESSION['comment'][md5(POST_ID.'email')] = $email;
			$_SESSION['comment'][md5(POST_ID.'website')] = $website;
			$_SESSION['comment'][md5(POST_ID.'text')] = $text;
			return;
		} else {
			$user_id = load_value('user_id');
			$ptype = isset($_POST[erukka('ptype')]) ? $_POST[erukka('ptype')] : 0;
			$text = str_replace('/\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-/g', '', $text);
			$text = clean($text); unset($_SESSION['comment']);
			$ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP');
			$ip_address = erukka($ip, '', false); $date = date('Y-m-d H:i:s');
			$ks = genKey('key_security'); $delkey = genKey('del_key');
			$approved = load_cfg('auto_approve_msg') || load_value('user_gm') == '1' ? 1 : 0;
			$avatar = erukka($email, '', false);
			$website = erukka($website, '', false);
			if (load_cfg('auto_approve_msg')=='0' && load_value('user_gm') !== '1') {
				echo '<div class="ebook_admin"><div class="msginfo">';
				echo '<h3>'.t('comment').'</h3><p>'.t('comwap').'</p>';
				echo '</div></div>';
			}
			$reply_id = isset($_POST[erukka('id')]) ? drukka($_POST[erukka('id')]) : 0;
			$query = "INSERT INTO ".PREFIX."comments 
				(plug_id, pcontent_id, ptype, reply_id, user_id, user_ip, name, avatar_email, url, comment, date_reg, approved, key_security, delkey)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			if ($sql = db() -> prepare($query)) {
				$sql = dbbind($sql, array($sid, $page_id, $ptype, $reply_id, $user_id, $ip_address, $name, $avatar, $website, $text, $date, $approved, $ks, $delkey), 'iiiiissssssiss');
				unset($sql);
			} $comment_page = isset($get['comment_page']) ? pre_seao('comment_page', $get['comment_page']) : '';
		}
		if ($ptype == 0) {
			echo '<meta http-equiv="refresh" content="2; url='.pre_seao('page', $get['page'], false, true).$comment_page.'">';
		} else {
			$link = webroot();
			if (isset($uri[0])) {$link .= pre_seao($uri[0], $get[$uri[0]], false);}
			for ($i=1; $i<count($uri)-1; $i++) {
				if (isset($uri[$i])) {$link .= pre_seao($uri[$i], $get[$uri[$i]]);}
			} echo '<meta http-equiv="refresh" content="2; url='.$link.'">';
		}
	} else {
		if ($_POST) {$_SESSION[HURI][erukka('try_hack')] = 1;}
		echo '<div class="ebook_admin"><div class="msgerror"><h3>'.t('hack_detect').'</h3></div></div>';
	}
}

// LOGIN FORM
function login_form(){ global $get, $uri;
	if (!LOGGED && $get['login'] == 'true' &&
		(!isset($_SESSION[HURI][erukka('tries')]) || $_SESSION[HURI][erukka('tries')] < 3)) {
		$url = pre_seao('login', 'verify', false, true).load_value('durl');
		$register = '<a href="'.pre_seao('register', 'true', false, true).'">'.t('register').'</a>';
		if (load_cfg('registration') === '0') {$register = '';}
		echo '<div class="section_login"><div class="form_login">';
			echo '<div class="login_title">';
				echo '<h1>'.t('login_screen').'</h1>';
				echo '<span>'.t('login_details').(!empty($register) ? ' / '.$register : '').'</span>';
			echo '</div>';
			echo '<form method="post" action="'.$url;
			echo '" id="login" accept-charset="'.load_cfg('charset').'" name="login" autocomplete="off" >';
				echo '<textarea class="private" name="private" cols="1" rows="1"></textarea>';
				echo '<div class="login_info">';
					echo '<div class="info_user">';
						echo '<label for="username">'.t('username').'</label>';
						echo '<input type="text" name="'.erukka('username').'" ';
						echo 'id="username" value="" maxlength = "20">';
					echo '</div>';
					echo '<div class="info_pass">';
						echo '<label for="pass">'.t('password').'</label>';
						echo '<input type="password" name="'.erukka('pass').'" id="pass" value="">';
					echo '</div>';
				echo '</div>';
				echo '<div class="login_foot">';
					echo '<input type="submit" value="'.t('login').'" class="btLogin" ';
					echo 'name="'.erukka('submit').'" >';
				echo '</div>';
				fightSpamRSA();
			echo '</form>';
		echo '</div></div>';
	}
}

// LOGIN FORM
function login() { global $get, $uri, $last, $lget, $luri;
	$ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP'); $ipx = erukka($ip, '', false);
	if (!LOGGED) {
		if (isset($get['err']) && $get['err'] == '2') {
			echo '<div class="msgerror">'.t('already_logged').'</div>'; return;
		}
		# NO HACKING PLEASE
		if ($_SESSION && isset($_SESSION[HURI][erukka('try_hack')]) && $_SESSION[HURI][erukka('try_hack')] != 0) {
			echo '<div class="msgerror"><h3>'.t('hack_detect').'</h3></div>'; return;
		}
		$browser_id = get_hsa();
		$fail_date = date('Y-m-d H:i:s'); $day = date('Y-m-d');
		# FAIL 3 TIMES
		if (isset($_SESSION[HURI][erukka('tries')]) && $_SESSION[HURI][erukka('tries')] > 2) {
			$data = retrieve_mysql("logs", "guest_ip", "'$ipx'", 
				"id, fail, fail_date, log_date", " AND log_date = '$day' AND browser_id = '$browser_id'");
			$wait_time = isset($data['fail_date']) ? strtotime("now") - strtotime($data['fail_date']) : 0;
			if ($wait_time >= 0) {
				$fail = 0; $_SESSION[HURI][erukka('tries')] = $fail; $id = $data['id'];
				$sql = "UPDATE ".PREFIX."logs SET fail = ?, fail_date = ? 
					WHERE id = ? AND log_date = ? AND browser_id = ?";
				if ($qx = db() -> prepare($sql)) {
					$qx = dbbind($qx, array($fail, $fail_date, $id, $day, $browser_id), 'isiss');
					unset($qx);
				}
			} else {
				echo '<h1>'.t('login').'</h1><div class="msgerror"><h3>';
				echo t('error_fail3').'</h3><p>'.t('wait_session_out').'</p></div>'; return;
			}
		}
		# LOGIN FORM
		login_form();
		# SUBMIT
		if ($_POST && $_POST[erukka('submit')] == t('login') && checkSpamRSA()) {
			$username = clean($_POST[erukka('username')]); $username = rencrypt1($username);
			$password = $_POST[erukka('pass')]; $pw = erukka1(md5($password));
			$fields = 'id, realname, gmember, avatar, website, last_login, last_logoff, first_login';
			$fields.= ', total_timedate, ask_sp, special_num, akey, key_security, user_active';
			$user = retrieve_mysql('users', 'username', "'".$username."'", $fields, " AND password = '$pw'");
			if ($user) {
				if ($user['user_active'] !== '2') {
					echo '<h1>'.t('login').'</h1>';
					if ($user['user_active'] == '0') {echo '<div class="msgwarning">'.t('wait_activation').'</div>';}
					if ($user['user_active'] == '1') {echo '<div class="msgwarning">'.t('blocked').'</div>';}
					if ($user['user_active'] == '3') {echo '<div class="msgwarning">'.$t('fspam').'</div>';}
					echo '<meta http-equiv="refresh" content="3; url='.pre_seao('login', 'true', false, true).'">'; return;
				}
				if ($get['login'] == 'verify' && !isset($get['err'])) {
					echo '<h1>'.t('login').'</h1><div class="msgsuccess">'.t('verify').'</div>';
				} $parent = isset($_POST['parent']) ? clean($_POST['parent']) : null;
				$now = strtotime("now"); $id = $user['id'];
				$last_login = strtotime($user['last_login']);
				$last_logoff = strtotime($user['last_logoff']);
				$total = $user['total_timedate']+ceil(($last_logoff - $last_login)/60);
				$time_off = date('Y-m-d H:i:s', strtotime('+'.TIME_OUT.' seconds'));
				$current_date = date('Y-m-d H:i:s'); $post_ipa = clean($_POST['ip_address']);
				if ($now > $last_login && $last_login < $last_logoff && $now < $last_logoff) {
					header('Location: '.pre_seao('login', 'true', false, true).pre_seao('err','2')); return;
				}
				$codeKS = genKey('key_security'); $delKS = genKey('del_key'); $sid = load_value('sid');
				$_SESSION[HURI]['user_id'] = erukka($user['id'], '', false);
				$_SESSION[HURI]['realname'] = $user['realname'];
				$_SESSION[HURI]['avatar'] = $user['avatar'];
				$_SESSION[HURI]['website'] = $user['website'];
				$_SESSION[HURI][sha1(POST_ID.$sid)] = checkLogin();
				$_SESSION[HURI][erukka('user_security')] = erukka($codeKS, '', false);
				$_SESSION[HURI][erukka('group_member')] = erukka($user['gmember'], '', false);
				$_SESSION[HURI]['ask'] = !empty($user['special_num']) && $user['ask_sp'] ? 1 : 0;
				$_SESSION[HURI]['pin'] = !empty($user['special_num']) && $user['ask_sp'] ? 0 : 1;
				$_SESSION[HURI]['akey'] = erukka1($user['akey']);
				$sql = "UPDATE ".PREFIX."users SET last_login = ?, last_logoff = ?, user_ip = ?, browser_id = ?,
					total_timedate = ?, key_security = ?, del_key = ? WHERE id =?";
				if ($qx = db() -> prepare($sql)) {
					$qx = dbbind($qx, array($current_date, $time_off, $post_ipa, $browser_id, $total, $codeKS, 
					$delKS, $id), 'sssssssi');
					$execute = dbfetch($qx, true);
					unset($qx, $sql);
				}
				if (defined('use_https') && use_https == true) {$http = str_replace('http://', 'https://', $http);}
				$ukey = pre_seao('ukey', $_SESSION[HURI][erukka('user_security')]);
				if ($user['first_login'] == '1') {
					$_SESSION[HURI]['first'] = 1;
					echo '<meta http-equiv="refresh" content="3; url=';
					echo pre_seao('admin', 'myfirsttime', false, true, true).pre_seao('action', 'edit').$ukey.'">';
				} else if (isset($parent) && !empty($parent)) {
					echo '<meta http-equiv="refresh" content="1; url='.pre_seao('page', $parent, false, true).'">';
					return;
				} else if (isset($luri[1]) && isset($last[$luri[1]]) && $last[$luri[1]] == 'view') {
					$plugin = retrieve_mysql("plugins", "sef_file", "'$luri[1]'", "id", "");
					if ($plugin) {
						$link = pre_seao($luri[1], $lget[1], false, true);
						for ($i = 2; $i < count($luri); $i++){$link .= pre_seao($luri[$i], $lget[$i]);}
						echo '<meta http-equiv="refresh" content="1; url='.$link.$ukey.'">';
					} else {$link = webroot(); echo '<meta http-equiv="refresh" content="1; url='.$link.$ukey.'">';}
				} else {
					$link = $user['gmember'] == 1 || isAllowed('root_access') ? 'root' : 'personal_data';
					echo '<meta http-equiv="refresh" content="1; url='.pre_seao('admin',$link, false, true, true);
					echo pre_seao('ukey', $_SESSION[HURI][erukka('user_security')]).'">';
				} return;
			} else if ($get['login'] == 'verify') {
				# BAD USER PUT IN logs
				echo '<h1>'.t('login').'</h1>';
				$message = '';
				if (empty($username)){$message = '<li>'.t('user_empty').'</li>';}
				if (empty($password)){$message.= '<li>'.t('pass_empty').'</li>';}
				if (empty($username) || empty($password)) {echo '<div class="msgerror"><ul>'.$message.'</ul></div>';}
				if (!empty($username) && !empty($password)) {
					echo '<div class="msgerror">'.t('bad_login').'</div>';
					$query = "SELECT id, fail, fail_date, log_date FROM ".PREFIX."logs 
						WHERE guest_ip = ? AND log_date = ? AND browser_id = ?";
					if ($result = db() -> prepare($query)) {
						$result = dbbind($result, array($ipx, $day, $browser_id), 'sss');
						$data = dbfetch($result, true); unset($result);
					}
					if ($data) {
						$fail = $data ? $data['fail']+1 : 0; $id = $data['id'];
						$fail_date = date('Y-m-d H:i:s', strtotime('now+'.TIME_OUT.' seconds'));
						$_SESSION[HURI][erukka('tries')] = $fail;
						$sql = "UPDATE ".PREFIX."logs SET fail = ?, fail_date = ? 
							WHERE id = ? AND log_date = ? AND browser_id = ?";
						if ($qx = db() -> prepare($sql)) {
							$qx = dbbind($qx, array($fail, $fail_date, $id, $day, $browser_id), 'isiss');
						}
					} else {
						$_SESSION[HURI][erukka('tries')] = 1;
						$sql = "INSERT INTO ".PREFIX."logs (guest_ip,browser_id,fail,fail_date,log_date) 
							VALUES (?,?,?,?,?);";
						if ($qx = db() -> prepare($sql)) {
							$qx = dbbind($qx, array($ipx, $browser_id, '1', $fail_date, $day), 'ssiss');
						}
					} unset($qx);
				}
			} echo '<meta http-equiv="refresh" content="2; url='.pre_seao('login', 'true', false, true).'">';
		} else if ($_POST){$_SESSION[HURI][md5(POST_ID.'try_hack')] = true;}
	} if (LOGGED) {echo '<meta http-equiv="refresh" content="2; url='.pre_seao('admin', 'root', false, true, true).'">';}
}

// REGISTRATION FORM
function register_form() { global $get, $acontent;
	if (LOGGED) {echo '<meta http-equiv="refresh" content="0; url='.webroot().'">'; return;}
	if (load_cfg('registration') === '0') {return;}
	# NO HACKING PLEASE
	if ($_SESSION && isset($_SESSION[HURI][erukka('try_hack')]) && $_SESSION[HURI][erukka('try_hack')] != 0) {
		echo '<div class="msgerror"><h3>'.t('hack_detect').'</h3></div>'; return;
	}
	if ((isset($_SESSION[HURI][erukka('tries')]) && $_SESSION[HURI][erukka('tries')] > 2) || 
	isset($_SESSION[HURI][md5(POST_ID.'not_allowed')])) {
		echo '<h1>'.t('register').'</h1>';
		echo '<div class="msgerror"><h3>'.t('error_fail3').'</h3><p>'.t('wait_session_out').'</p></div>'; return;
	}
	if (!$_POST) {
		$charset = load_cfg('charset');
		$realname = isset($_SESSION['tmp']['realname']) ? $_SESSION['tmp']['realname'] : '';
		$website = isset($_SESSION['tmp']['website']) ? $_SESSION['tmp']['website'] : '';
		$email = isset($_SESSION['tmp']['email']) ? $_SESSION['tmp']['email'] : '';
		unset($_SESSION['tmp']);
		$url = pre_seao('register', 'true', false, true).load_value('durl');
		echo '<form method="post" action="'.$url.'" id="register" name="register" accept-charset="'.$charset.'">';
		echo '<fieldset>';
		echo '<h1>'.t('registration').'</h1>';
		$required = strtolower(t('required'));
		echo '<label for="username">'.t('username').' ('.$required.')</label>';
		echo '<input type="text" name="'.erukka('username').'" id="username" value=""><br />';
		echo '<label for="realname">'.t('realname').' ('.$required.')</label>';
		echo '<input type="text" name="realname" id="realname" value="'.$realname.'"><br />';
		echo '<label for="email">'.t('email_notpublish').' ('.$required.')</label>';
		echo '<input type="text" name="email" id="email" value="'.$email.'"><br />';
		echo '<label for="website">'.t('website').'</label>';
		echo '<input type="text" name="website" id="website" value="'.$website.'"><br />';
		echo '<label for="pass1">'.t('password').'</label>';
		echo '<input type="password" name="'.erukka('pass1').'" id="pass1" value=""><br />';
		echo '<label for="pass2">'.t('password3').'</label>';
		echo '<input type="password" name="'.erukka('pass2').'" id="pass2" value=""><br /><br />';
		echo '<input type="submit" value="'.t('register').'" class="buttons"></fieldset>';
		fightSpamRSA();
		echo '</form>';
	} else if ($_POST) {submit_register();}
}

// SUBMIT REGISTER FORM
function submit_register() { global $get, $acontent;
	if ($_POST && checkSpamRSA()) { $error = '';
		$username = cleanTxt($_POST[erukka('username')]);
		$username = rencrypt1(strtolower($username));
		$realname = cleanTxt($_POST['realname']);
		$_SESSION['tmp']['realname'] = $realname;
		$realname = rencrypt1($realname);
		$website = $_POST['website'];
		$website = (strlen($website) > 11 && 
			preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $website)) ? $website : null;
		$_SESSION['tmp']['website'] = $website;
		$email = $_POST['email']; $_SESSION['tmp']['email'] = $email;
		$email = isEmailValid($email);
		$pass1 = $_POST[erukka('pass1')];
		$pass2 = $_POST[erukka('pass2')];
		if (empty($username)) {$error .= '<li>'.t('empty_name').'</li>';}
		if (empty($realname)) {$error .= '<li>'.t('empty_realname').'</li>';}
		if (empty($email)) {$error .= '<li>'.t('empty_mail').'</li>';}
		if ($pass1 !== $pass2) {$error .= '<li>'.t('pass_notmatch').'</li>';}
		if (empty($pass1) && empty($pass2)) {$error .= '<li>'.t('pass_empty').'</li>';}
		if (!empty($error)) {
			echo '<h1>'.t('register').'</h1>';
			echo '<div class="msgerror"><h3>'.t('error').'</h3>';
			echo '<ul>'.$error.'</ul></div>';
			echo '<meta http-equiv="refresh" content="3; url='.pre_seao('register', 'true', false, true).'">';
			return;
		} else {
			unset($_SESSION['tmp']);
			$akey = gen_PKey(64);
			$reg = rencrypt($email, $akey);
			$email = erukka($email, $akey, false);
			$website = erukka($website, '', false);
			$user_active = load_cfg('user_active');
			$user_ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP');
			$user_ip = erukka($user_ip, '', false);
			$key_security = genKey('key_security'); $del_security = genKey('del_key');
			$validation_code = genKey('code');
			$regdate = date('Y-m-d');
			$gmember = load_cfg('user_member');
			$password = erukka1(md5($pass1));
			$query = "INSERT INTO ".PREFIX."users 
				(user_active, user_ip, username, realname, avatar, password, gmember, emailreg, email, website, regdate, vcode, akey, key_security, del_key) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			if ($result = db() -> prepare($query)) {
				$result = dbbind($result, array($user_active,$user_ip,$username,$realname,$email,$password,$gmember,$reg,$email,$website,$regdate,$validation_code,$akey,$key_security,$del_security), 'isssssissssssss');
				$data = dbfetch($result, true);
			} unset($result, $data);
		}
		echo '<div class="msgsuccess">'.t('register_sucess').'</div>';
		echo '<meta http-equiv="refresh" content="3; url='.pre_seao('login', 'true', false, true).'">';
	} else
	if ($_POST){echo '<div class="msgerror"><h3>'.t('error').'</h3><p>'.t('spambot_detected').'</p>';
		$_SESSION[HURI][md5(POST_ID.'try_hack')] = true;
	}
}

// PERSONAL MESSAGE FORM
function pmessageForm() { global $get;
	$action = pre_seao('message', 'true', false, true);
	$action.= (isset($get['pageid']) ? pre_seao('pageid', $get['pageid']) : '').load_value('durl');
	$charset = load_cfg('charset'); $required = strtolower(t('required'));
	echo '<form method="post" action="'.$action.'" id="commentform" name="msgform" accept-charset="'.$charset.'">';
	echo '<fieldset><h1>'.t('new_pmsg').'</h1><br />';
	if (!LOGGED) {
		$name = isset($_SESSION['tmp']['name']) ? $_SESSION['tmp']['name'] : '';
		$subject = isset($_SESSION['tmp']['subject']) ? $_SESSION['tmp']['subject'] : '';
		$email = isset($_SESSION['tmp']['email']) ? $_SESSION['tmp']['email'] : '';
		$xname = $_POST && empty($name) ? '<span class="red">'.$required.'</span>' : $required;
		echo '<label for="name">'.t('name').' ('.$xname.')</label>';
		echo '<input type="text" name="name" id="name" value="'.$name.'" autocomplete="off">';
		$xsubject = $_POST && empty($subject) ? '<span class="red">'.$required.'</span>' : $required;
		echo '<label for="subject">'.t('subject').' ('.$xsubject.')</label>';
		echo '<input type="text" name="subject" id="subject" value="'.$subject.'" autocomplete="off" maxlength = "20">';
		$xemail = $_POST && empty($email) ? '<span class="red">'.$required.'</span>' : $required;
		echo '<label for="email">'.t('email').' ('.$xemail.')</label>';
		echo '<input type="text" name="email" id="email" value="'.$email.'" autocomplete="off">';
	} else {
		$akey = drukka1($_SESSION[HURI]['akey']);
		echo '<input type="hidden" name="name" value="'.rdecrypt1($_SESSION[HURI]['realname']).'">';
		echo '<input type="hidden" name="email" value="'.drukka($_SESSION[HURI]['avatar'], $akey).'">';
		$xsubject = $_POST && empty($subject) ? '<span class="red">'.$required.'</span>' : $required;
		echo '<label for="subject">'.t('subject').' ('.$xsubject.')</label>';
		echo '<input type="text" name="subject" id="subject" value="'.$subject.'" autocomplete="off" maxlength = "20">';
	}
	$text = isset($_SESSION['tmp']['text']) ? $_SESSION['tmp']['text'] : '';
	$xtext = $_POST && empty($text) ? '<span class="red">'.$required.'</span>' : $required;
	echo '<label for="text">'.t('message').' ('.$xtext.')</label><textarea class="text" name="txt1" cols="1" rows="1">'.$text.'</textarea>';
	echo '<p class="msg_bbcode"><strong>'.t('use_bbcode').': </strong> [b][u][i][url][color][img]</p>';
	if (isset($get['pageid'])) {echo '<input type="hidden" name="page" value="'.$get['pageid'].'">';}
	echo '<input type="submit" value="'.t('submit').'" class="buttons"></fieldset>';
	fightSpamRSA();
	echo '</form>';
	unset($_SESSION['tmp']);
	
}
// PERSONAL MESSAGE
function pmessage($ptype = 0, $author = 1, $gmember = 1) { global $get;
	if ($_POST && checkSpamRSA()) {
		$name = cleanTxt($_POST['name']); $_SESSION['tmp']['name'] = $name;
		$username = rencrypt1(strtolower($name));
		$subject = clean($_POST['subject']); $_SESSION['tmp']['subject'] = $subject;
		$email = $_POST['email'];
		$email = isEmailValid($email); $_SESSION['tmp']['email'] = $email;
		$_email = erukka($email, '', false);
		if (isset($_POST['txt1']) && !empty($_POST['txt1'])) {
			$text = preg_replace("#(\n\r){2,}#sU", "", $_POST['txt1']);
			$text = str_replace(array("\n", "<br />", "<br>"), '[br]', $text);
			$text = str_replace("\r", "", $text);
			$text = preg_replace("#(\[br\]){1,}(.+?)#sU", "$2", $text);
			$text = clean_mysql($text);
			$_SESSION['tmp']['text'] = $text;
		} else {$text = '';}
		if (empty($name) || empty($email) || empty($text) || empty($subject)) {pmessageForm(); return;}
		$userID = LOGGED ? drukka($_SESSION[HURI]['user_id']) : 0;
		$author_id = isset($content['author_id']) ? $content['author_id'] : $author;
		$members = $gmember;
		$pid = isset($get['pageid']) ? $get['pageid'] : 0;
		$page_id = isset($get['pageid']) ? drukka1($get['pageid']) : 0;
		$lang_id = load_value('lang');
		$date_sent = date('d-m-Y');
		$unread = 1;
		$ks = genKey('key_security'); $delkey = genKey('del_key');
		$query = "INSERT INTO ".PREFIX."pmessages 
			(ptype, user_id, suser_id, gmember, page_id, lang_id, date_sent, name, email, subject, message, unread, key_security, delkey) 
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
		if ($result = db() -> prepare($query)) {
			$result = dbbind($result, array($ptype, $userID, $author_id, $members, $page_id, $lang_id, $date_sent, $username, $_email, $subject, $text, $unread, $ks, $delkey), 'iiiiiisssssiss');
			$data = dbfetch($result, true);
			echo '<div class="msgsuccess">'.t('pmsg_sent').'</div>';
			if ($page_id != 0) {
				$data = retrieve_mysql("pages", "id", $page_id, "id, seftitle","");
				if ($data) {
					echo '<meta http-equiv="refresh" content="3; url='.pre_seao('page', $data['seftitle'], false, true).'">';
				}
			}
		} unset($result, $data, $_SESSION['tmp']);
	} else {pmessageForm();}
}

// EXECUTE FUNCTION
function tag_func($fulltext) {
	if (empty($fulltext)) {return;}
	if (strpos($fulltext,'[func]') !== false && function_exists('call_user_func_array')) {
		$text = str_replace('[func]', 'execute_function([|', $fulltext);
		$text = str_replace('[/func]', '|]', $text); $txt = '';
		$text = explode('execute_function(', $text);
		for ($i = 0; $i < count($text); $i++) {
			if (strpos($text[$i],'[|') !== false) {
				$func = substr($text[$i], 2, strpos($text[$i], '|]')-2);
				$txt1 = substr($text[$i], strpos($text[$i], '|]')+2);
				$func = str_replace('()', '', $func);
				$func = str_replace('(', ';', $func);
				$func = str_replace(')', '', $func);
				if (strpos($func, 'echo \'') === 0) {
					$func = str_replace(';', '', $func);
					$func = str_replace('\'', '', $func);
					$func = str_replace('echo', 'echo;', $func);
				}
				$afunc = explode(';', $func);
				if (function_exists($afunc[0])) {
					ob_start();
					if (isset($afunc[1])) {$result = call_user_func_array($afunc[0], explode(',', $afunc[1]));}
					else {$result = call_user_func_array($afunc[0], array());}
					if (strtolower($afunc[0]) == 'echo') {echo $afunc[1];}
					$obget = ob_get_clean();
					if (empty($obget)) {$txt .= $result.$txt1;} else {$txt .= $obget.$txt1;}
				} else {$txt .= 'FUNCTION (<strong>'.$afunc[0].'</strong>) does not exist<br />'.$txt1;}
			} else {$txt .= $text[$i];}
		} $fulltext = $txt; $txt = '';
	} return $fulltext;
}

// UPDATE TIME LOGIN
if (LOGGED) {
	$id = $_SESSION[HURI]['user_id']; $now = strtotime("now"); $double = intval(TIME_OUT + TIME_OUT);
	$time_login = date('Y-m-d H:i:s', strtotime('+'.TIME_OUT.' seconds'));
	$max_timeout = date('Y-m-d H:i:s', strtotime('+'.$double.' seconds'));
	$ip = getInfo('YOUR_IP') == '::1' ? '127.0.0.1' : getInfo('YOUR_IP');
	$query = "SELECT browser_id, last_logoff FROM ".PREFIX."users WHERE user_ip = ? AND browser_id = ? ";
	if ($result = db() -> prepare($query)) {
		$result = dbbind($result, array($ip, get_hsa()), 'ss');
		$data = dbfetch($result, true);
		if ($data) {
			$qwr = "UPDATE ".PREFIX."users SET last_logoff = ? WHERE id = ?";
			$sql = db() -> prepare($qwr);
			$sql -> execute(array($time_login, $id));
			unset($sql, $data);
		} unset($result);
	}
}

// GET HERITED CSS FROM CONTENT
function get_css($css) { global $acontent;
	if (isset($acontent['pcsstype']) && !empty($acontent['pcsstype'])) {
		$page = isset($acontent['pcsstype']) && !empty($acontent['pcsstype']) ? $acontent['pcsstype']: $css;
	} else if (isset($acontent['csstype']) && !empty($acontent['csstype'])) {
		$page = isset($acontent['csstype']) && !empty($acontent['csstype']) ? $acontent['csstype']: $css;
	} else {$page = $css;}
	return $page;
}

// CALL PLUGIN IN PAGE
function check_pageplugin($acontent) {
	if (isset($acontent['sec_type']) && isset($acontent['plugin_id']) && $acontent['sec_type'] == 1) {
		$plugin = retrieve_mysql("plugins", "id", $acontent['plugin_id'], 
			"admin_func,public_func,admin_by"," AND auto_load = 1 AND installed = 1");
		if (isset($plugin['public_func'])) {
			if (function_exists($plugin['public_func'])) {
				$func = $plugin['public_func'];
				ob_start();
				$result = call_user_func_array($func, array('list'));
				$obget = ob_get_clean();
				if (empty($obget)) {echo $result;} else {echo $obget;}
			}
		}
	}
}

// CENTER
function center() { global $get, $uri, $acontent, $t;
	$group_member = load_value('user_gm');
	switch ($uri[0]) {
		case 'login' : login(); break;
		case 'register' : register_form(); break;
		case 'logout' :
			$id = isset($_SESSION[HURI]['user_id']) ? drukka($_SESSION[HURI]['user_id']) : '';
			$code = genKey('code'); $now = date('Y-m-d H:i:s');
			if (isset($_SESSION[HURI]['user_id'])) {
				$query = "UPDATE ".PREFIX."users SET last_logoff = ?, key_security = ? WHERE id = ?";
				if ($result = db() -> prepare($query)) {
					$result = dbbind($result, array($now, $code, $id), 'ssi');
				}
			}
			echo '<h1>'.t('logout').'</h1>';
			echo '<meta http-equiv="refresh" content="0; url='.webroot().'">';
			unset($_SESSION[HURI]);
			break;
		case 'search' : search(); break;
		case 'admin' : if (LOGGED && function_exists('admin_center')) {
			$open_tags = isset($t['admin_open']) ? tags('admin', 'open') : '';
			$close_tags = isset($t['admin_open']) ? tags('admin', 'close'): '';
			echo format_tags($acontent, $open_tags);
			admin_center();} else {set_error();}
			echo format_tags($acontent, $close_tags);
		break;
		case 'message': pmessage(); break;
		case 'page': case 'show' :
			if (!$acontent) {set_error(); return;}
			# TAGS
			$page = get_css($uri[0]);
			$cssopen_tags = tags($page, 'cssopen');
			$cssclose_tags = tags($page, 'cssclose');
			$top_tags = tags($page, 'topline');
			if (!is_bool($top_tags) && $page != 'page' && empty($top_tags)) {$top_tags = tags('page', 'topline');}
			$body_tags = tags($page, 'bodyline');
			if (!is_bool($body_tags) && $page != 'page' && empty($body_tags)) {$body_tags = tags('page', 'bodyline');}
			$foot_tags = tags($page, 'footline');
			if (!is_bool($foot_tags) && $page != 'page' && empty($foot_tags)) {$foot_tags = tags('page', 'footline');}
			if (!empty($cssopen_tags)) {$cssopen_tags = tag_func($cssopen_tags);}
			if (!empty($cssclose_tags)) {$cssclose_tags = tag_func($cssclose_tags);}
			if (!empty($top_tags)) {$top_tags = tag_func($top_tags);}
			if (!empty($body_tags)) {$body_tags = tag_func($body_tags);}
			if (!empty($foot_tags)) {$foot_tags = tag_func($foot_tags);}
			$top_tags = isset($acontent['showtitle']) && $acontent['showtitle'] == 1 ? $top_tags : '';
			# OPEN CSS TAG
			if (!empty($cssopen_tags)) {echo format_tags($acontent, $cssopen_tags);}
			# CONTENT TOP PAGE
			if (!empty($top_tags)) {echo format_tags($acontent, $top_tags);}
			# CONTENT TEXT
			if (!empty($acontent['texto1']) || $page != 'page') {echo format_tags($acontent, $body_tags);}
			check_pageplugin($acontent);
			# BOTTOM CONTENT
			if (!empty($foot_tags)) {echo format_tags($acontent, $foot_tags);}
			# COMMENTS
			$commentable = isset($acontent['comments']) && ($acontent['comments'] == '1' || 
				$acontent['comments'] == '2') ? 1 : 0;
			if ($commentable) {comment($acontent['id'], 0, $commentable);}
			# CLOSE CSS TAG
			if (!empty($cssclose_tags)) {echo format_tags($acontent, $cssclose_tags);}
		break;
		# ERROR
		case 'error': show_error(); break;
		default:
			# HOME
			if (empty($uri[0]) && !$get['error'] && $get['error'] != '404' && isset($acontent)) {
				if (!$acontent) {echo t('no_content'); return;}
				$page = get_css('page'); if (empty($page)) {$page = 'page';}
				$cssopen_tags = tags($page, 'cssopen');
				$cssclose_tags = tags($page, 'cssclose');
				$top_tags = tags($page, 'topline');
				if (!is_bool($top_tags) && $page != 'page' && empty($top_tags)) {
					$top_tags = tags($page, 'topline');
				} $body_tags = tags($page, 'bodyline');
				if (!is_bool($body_tags) && $page != 'page' && empty($body_tags)) {
					$body_tags = tags($page, 'bodyline');
				} 
				$foot_tags = tags($page, 'footline');
				if (!is_bool($foot_tags) && $page != 'page' && empty($foot_tags)) {
					$foot_tags = tags($page, 'footline');
					echo $foot_tags;
				}
				if (!empty($cssopen_tags)) {$cssopen_tags = tag_func($cssopen_tags);}
				if (!empty($cssclose_tags)) {$cssclose_tags = tag_func($cssclose_tags);}
				if (!empty($top_tags)) {$top_tags = tag_func($top_tags);}
				if (!empty($body_tags)) {$body_tags = tag_func($body_tags);}
				if (!empty($foot_tags)) {$foot_tags = tag_func($foot_tags);}
				$top_tags = isset($acontent['showtitle']) && $acontent['showtitle'] == 1 ? $top_tags : '';
				# OPEN CSS TAG
				if (!empty($cssopen_tags)) {echo format_tags($acontent, $cssopen_tags);}
				# CONTENT : TOP+BODY
				if (!empty($top_tags)) {echo format_tags($acontent, $top_tags);}
				if (!empty($body_tags)) {echo format_tags($acontent, $body_tags);}
				check_pageplugin($acontent);
				# BOTTOM CONTENT
				if (!empty($foot_tags)) {echo format_tags($acontent, $foot_tags);}
				$commentable = isset($acontent['comments']) && ($acontent['comments'] == '1' || 
					$acontent['comments'] == '2') ? 1 : 0;
				if ($commentable) {comment($acontent['id'], 0, $commentable);}
				# CLOSE CSS TAG
				if (!empty($cssclose_tags)) {echo format_tags($acontent, $cssclose_tags);}
				break;
			# PLUGIN / ADDON
			} else if (function_exists($uri[0])) {
				ob_start();
				$command = $get[$uri[0]];
				for ($i=1; $i < count($get); $i++){$command .= ','.$uri[$i].','.$get[$uri[$i]];}
				$commands = explode(',', $command);
				$result = call_user_func_array($uri[0], $commands);
				$obget = ob_get_clean();
				if (empty($obget)) {echo $result;} else {echo $obget;}
				break;
			} else if (!isset($content)) {
				# PLUGIN
				if (!isset($uri[0])) {set_error(); exit;}
				$plugin = retrieve_mysql("plugins", "sef_file", '"'.$uri[0].'"', 
					"admin_func,public_func,admin_by"," AND auto_load = 1 AND installed = 1");
				if ($plugin) {
					$admin = $get[$uri[0]];
					if ($admin == 'admin' && LOGGED && function_exists($plugin['admin_func']) && 
						isGroupAllowed($plugin)) {
						if ($_POST){$commands = isset($_POST['tb']) ? array($_POST['tb']) : array('list');}
						else {$commands = isset($get['tb']) ? array($get['tb']) : array('list');}
						ob_start();
						$result = call_user_func_array($plugin['admin_func'], $commands);
						$obget = ob_get_clean();
						if (empty($obget)) {echo $result;} else {echo $obget;}
					} else
					# LIST
					if ($admin == 'list' && function_exists('public_'.$uri[0])) {
						ob_start();
						$result = call_user_func_array('public_'.$uri[0], array('list'));
						$obget = ob_get_clean();
						if (empty($obget)) {echo $result;} else {echo $obget;}
					} else
					# VIEW
					if ($admin == 'view' && function_exists('view_'.$uri[0])) {
						ob_start();
						$result = call_user_func_array('view_'.$uri[0], array('list'));
						$obget = ob_get_clean();
						if (empty($obget)) {echo $result;} else {echo $obget;}
					} else {set_error(); exit;}
					break;
				} else {
				# NO PLUGIN
					echo '<h1>'.t('warning').'</h1><p>'.t('no_content').'</p>'; set_error(); return;}
					if (!empty($uri[0]) && !$get['error'] && $get['error']!='404') {
						set_error();
						echo '<meta http-equiv="refresh" content="0; url='.pre_seao('error', '404', false, true).'">';
						header('Location: '.pre_seao('error', '404', false, true));
					}
			} break;
	}
}

// SAVE PLUGIN DATA
function save_plugin($plugin_name, $fields, $values) {
	$var = explode(',', $fields);
	$val = explode(',', $values);
	for ($i=0; $i<count($fields); $i++) {
		if ($i==0) {$array = $var[$i]. " = '".$val[$i]."' ";}
		else {$array .= $var[$i]. " = '".$val[$i]."' ";}
	}
	$query = "UPDATE ".PREFIX."plugins SET $array WHERE sef_file = '$plugin_name'";
	if ($result = db() -> query($query)) {
		while ($r = dbfetch($result)) {}
		unset($result);
	}
}

// VERIFY EMAIL IS VALID OR NOT
function isEmailValid($email) {
	$email = htmlentities(trim($email), ENT_NOQUOTES);
	$result = (strlen($email) > 7 && 
		preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i' , $email)) ? 
	cleanTxt($email) : null;
	if ($result && function_exists('filter_var')) {
		$result = filter_var($result, FILTER_VALIDATE_EMAIL) === false ? null : $result;
	} return $result;
}

// PREPARE TEXT FOR HTML-EMAIL
function format_html($text) {
	if (get_magic_quotes_gpc()) {$text = stripslashes($text);}
	$text = str_replace(array("\\r\\n", "\r\n", "\n", "\r"), "<br />", $text);
	$text = str_replace('\"', '"',$text);
	return($text);
}

// CALL PUBLIC PLUGIN
function callPlugin($name, $args = array()) {
	if (function_exists($name)){call_user_func_array($name,$args);}
}

// LOAD PLUGIN DATA
function load_plugin($plugin_name, $fields) {
	$webconf = array();
	$query = "SELECT $fields FROM ".PREFIX."plugins WHERE sef_file = '$plugin_name'";
	if ($result = db() -> query($query)) {
		$var = explode(',', $fields);
		while ($r = dbfetch($result)) {for ($i=0; $i<count($var); $i++){$webconf[$var[$i]] = $r[$var[$i]];}}
		unset($result);
	} return $webconf;
}

?>