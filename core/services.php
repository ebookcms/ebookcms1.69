<?php
/* ------------------------------------------------------------------------------
	
	eBookCMS 1.69
	Stable Release date: September 16, 2015
	Copyright (c) Rui Mendes
	eBookCMS is licensed under a "Creative Commons License"
	
 ------------------------------------------------------------------------------ */

	$pagename = substr($_SERVER['SCRIPT_NAME'], strripos($_SERVER['SCRIPT_NAME'], '/')+1);
	if ($pagename == 'services.php') {die('Bye bye');}
	if (!defined('SECURE_ID') && SECURE_ID == '1234') {die('ACCESS DENIED');}


// DELETE IMAGE
function admin_delete_image($image1, $image2) {
	if (!LOGGED && isAllowed('uplimg_access')) {echo '<meta http-equiv="refresh" content="0; url='.webroot().'">'; return;}
	if (file_exists($image1)) {unlink($image1);}
	if (file_exists($image2)) {unlink($image2);}
	exit;
}

// SHOW IMAGES INSIDE A DIV
function admin_showimage($selection, $path, $options) { global $get, $uri;
	$real_path = $path.'/'.$selection;
	if (isset($get['o'])) {
		$url = explode('/',$get['o']);
		$url1 = explode('|', $url[0]);
		$url2 = explode('|', $url[1]);
		$found = in_array('sl', $url1) ? true : false;
		$link = pre_seao($url1[0], $url2[0], false, true);
		$link =  str_replace('ebookcms.php/', '', $link);
		$link =  str_replace('ebookcms.php?', '?', $link);
		$add = !isset($get['sl']) ? pre_seao('sl', '1') : '';
		for ($i=1; $i<count($url1); $i++) {
			$link .= pre_seao($url1[$i], $url2[$i]);
			if ($i==2 && !$found) {$link .= $add;}
		}
	}
	echo '<div class="show_img">';
	if (!LOGGED && isAllowed('uplimg_access')) {echo t('not_allowed').'</div>'; exit;}
	if (is_dir($real_path)) { $images = '';
		$handle = opendir($real_path);
		$extensions = array('.jpg', '.bmp', '.gif', '.png');
		while (($file = readdir($handle)) == true) {
			if ($file != '.' && $file != '..' && $file != 'thumbs') {
				clearstatcache();
				$ext = strrchr($file, '.');
				if (in_array($ext, $extensions)) {
					if (file_exists($real_path.'/thumbs/'.$file)) {
						$images = $images.'<li>
							<div class="si_title">
								<h4>'.$file.'</h4>
							</div>
							<div class="si_img">
								<img src="'.$real_path.'/thumbs/'.$file.'" />
							</div>
							<div class="si_foot">
								<p><a href="'.$link.'" onClick="var r = confirm(\''.t('delete_img').'?\'); if (r) {deleteimage(\''.$file.'\',\''.$real_path.'\');}" 
									target="_self">'.t('delete').'</a></p>
							</div>
						</li>';
					} else {$images = $images.'<li>
						<div class="si_title">
							<h4>'.$file.'</h4>
						</div>
						<div class="si_img">
							<img src="'.$real_path.'/'.$file.'" />
						</div>
						<div class="si_foot">
							<p><a href="'.$link.'" onClick="var r = confirm(\''.t('delete_img').'?\'); if (r) {deleteimage(\''.$file.'\',\''.$real_path.'\');}" 
									target="_self">'.t('delete').'</a></p>
						</div>
					</li>';}
				}
			}
		} closedir($handle);
		if (!empty($images)) {echo '<ul>'.$images.'</ul><br />';} else {echo t('no_image');}
	} else if (empty($selection)) {echo t('select_imgs');}
	echo '</div>';
	if (!empty($images)) {return;}
}

// SHOW IMAGES INSIDE A DIV
function admin_showgal($real_path) {// global $get, $uri;
	/*$real_path = $path.'/'.$selection;
	if (isset($get['o'])) {
		$url = explode('/',$get['o']);
		$url1 = explode('|', $url[0]);
		$url2 = explode('|', $url[1]);
		$found = in_array('sl', $url1) ? true : false;
		$link = pre_seao($url1[0], $url2[0], false, true);
		$link =  str_replace('ebookcms.php/', '', $link);
		$link =  str_replace('ebookcms.php?', '?', $link);
		$add = !isset($get['sl']) ? pre_seao('sl', '1') : '';
		for ($i=1; $i<count($url1); $i++) {
			$link .= pre_seao($url1[$i], $url2[$i]);
			if ($i==2 && !$found) {$link .= $add;}
		}
	}*/
	echo '<div class="show_img">';
	if (is_dir($real_path)) { $images = '';
		$handle = opendir($real_path);
		$extensions = array('.jpg', '.bmp', '.gif', '.png');
		while (($file = readdir($handle)) == true) {
			if ($file != '.' && $file != '..' && $file != 'thumbs') {
				clearstatcache();
				$ext = strrchr($file, '.');
				if (in_array($ext, $extensions)) {
					if (file_exists($real_path.'/'.$file)) {
						$images = $images.'<li>
							<div class="si_img">
								<img src="'.$real_path.'/'.$file.'" />
							</div>
						</li>';
					 }
				}
			}
		} closedir($handle);
		if (!empty($images)) {echo '<ul>'.$images.'</ul><br />';} else {echo t('no_image');}
	} else if (empty($selection)) {echo t('select_imgs');}
	echo '</div>';
	if (!empty($images)) {return;}
}

// MAIN SERVICES
function services() { global $get, $uri;
	if (isset($uri[0]) && $uri[0] == 'services') {
		$func_name = isset($get[$uri[0]]) ? 'admin_'.$get[$uri[0]] : '';
		if (!empty($func_name) && function_exists($func_name)) {
			for ($i = 1; $i < count($uri); $i++) {$args[$uri[$i]] = $get[$uri[$i]];}
			call_user_func_array($func_name,$args);
		} else {echo t('servnf');}
	}
}
?>