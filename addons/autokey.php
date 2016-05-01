<?php

/*------------------------------------------------------------------------------

	eBookCMS 1.68 - AUTO GENERATE YOUR_KEY
	Stable Release date: September 16, 2015
	Copyright (C) Rui Mendes
	eBookCMS is licensed under a "Creative Commons License"

------------------------------------------------------------------------------*/
if (defined('YOUR_KEY') && YOUR_KEY == "aPO7IU8Yz6xTREWQc9ASDvFGHJb4KLnZXmCVB3NM5lkjhgfdsqwerty01u2iop-_") {

	# GENERATE KEYS
	$def = "aPO7IU8Yz6xTREWQc9ASDvFGHJb4KLnZXmCVB3NM5lkjhgfdsqwerty01u2iop-_";
	$pkey = gen_PKey(64);
	
	// CHANGE DEFAULT YOUR_KEY WITH NEW (AUTO) 
	$myfile = fopen("config.php", "r") or die("Unable to open config file!");
	$newfile = fopen('cfg.php', 'w+') or die("Unable to create cfg file!");
	$found = false;
	
	// Output one line until end-of-file
	while(!feof($myfile)) {
		$txt = fgets($myfile);
		if (preg_match("#aPO7IU8Yz6xTREWQc9ASDvFGHJb4KLnZXmCVB3NM5lkjhgfdsqwerty01u2iop-_#", $txt)) {
			$txt = str_replace($def, $pkey, $txt);
			$found = true;
		} fwrite($newfile, $txt, strlen($txt));
	}
	fclose($myfile);
	fclose($newfile);
	
	if ($found) {
		unlink('config.php');
		rename('cfg.php', 'config.php');
		echo '<meta http-equiv="refresh" content="0; url='.webroot().'">';
		exit;
	}
}
unlink('addons/autokey.php');


?>