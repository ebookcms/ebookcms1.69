<?php

if (!defined('SECURE_ID') && SECURE_ID == '1234') {die('ACCESS DENIED');}
if (!LOGGED) {exit;}

# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************
#
#							Application Programming Interface eBookCMS
#							Version for eBookCMS 1.69
#							Revision : 0
#							Stable Release date: September 16, 2015
#							Copyright (c) by Rui Mendes
#							API eBookCMS is licensed under a "Creative Commons License"
#
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************

// CHECK RIGHTS
function check_rights($table, $id, $tks, $field, $action, $option = 0) { global $get;
	if (!isset($id) && $action == 'new') {return false;}
	$uid = load_value('user_id');
	$gm = load_value('user_gm'); if (strlen($gm) == 1) {$gx = '0'.$gm;}
	if ($action == 'save') {
		if (isset($_POST['id'])){$id = clean($_POST['id']); $id = drukka($id);}
		if (isset($_POST) && isset($_POST['tks'])) {$tks = clean($_POST[erukka('tks')]);}
		else if (isset($get['tks'])) {$tks = $get['tks'];}
	}
	$tks = $action != 'new' ? drukka($tks) : "";
	$ext = $action != 'new' ? " AND key_security = '$tks'" : "";
	$id = $action == 'new' ? drukka($id) : $id;
	if ($table == 'pages' && $id && $action != 'new') {
		$hdata = retrieve_mysql($table, "id", $id, 'ptype,section_id'," AND key_security = '$tks'");
		if (!$hdata) {return false;}
		$id = $hdata['section_id']; $ext = '';
		$table = $hdata['ptype'] == 'sections'; unset($hdata);
	}
	$data = retrieve_mysql($table, "id", $id, $field, $ext);
	$list = explode(',', $data[$field]);
	$allowed = in_array($gm, $list) || ($option == 1 && in_array('00', $list)) || $gm == 1 ? true : false;
	unset($data); return $allowed;
}

// VERIFY RIGHTS
function verify_admin($table, $id) { global $get; $hereited = false;
	if (isset($_POST['id'])) {$id = drukka(clean($_POST['id']));}
	switch ($table) {
		case 'pages': case 'cpages': $hereited = true;
			if (isset($get['action']) && $get['action'] != 'new') {
				$hdata = retrieve_mysql('pages', "id", $id, "section_id", "");
				if ($hdata['section_id']) {$id = $hdata['section_id'];} unset($hdata);
			} else {$id = drukka($id);}
			$table = 'sections'; break;
	}
	$data = retrieve_mysql($table, "id", $id, "admin_by,only_author","");
	if ($data && isGroupAllowed($data)) {$hereited = true;}
	return $hereited;
}

// FILTER BY
function table_filterBy($form, $func) { global $get, $uri;
	if (isset($form[$func])) { echo '<p>';
		$pn = !empty($get['pn']) ? pre_seao('pn', $get['pn']) : '';
		$ukey = $get && isset($get['ukey']) ? pre_seao('ukey', $_SESSION[HURI][erukka('user_security')]) : '';
		$filter = explode(',', $form[$func]);
		$all = isset($filter[5]) ? t($filter[5]) : t('all');
		$list = explode(',', t($filter[4]));
		$link  = pre_seao($uri[0], $get[$uri[0]], false, true);
		$link .= isset($get['action']) ? pre_seao('action', $get['action']) : '';
		if (isset($filter[7])) {$filter0 = isset($get[$filter[2]]) && $get[$filter[2]] != $filter[7] ?
			' <a href="'.$link.pre_seao($filter[2], strval($filter[7])).$ukey.'">'.$all.'</a>' : t($filter[5]);
		} else {$filter0 = isset($get[$filter[2]]) ? ' <a href="'.$link.$ukey.'">'.$all.'</a>' : $all;}
		echo '<b>'.t($filter[3]).'</b>: '.$filter0;
		$query  = "SELECT DISTINCT (".$filter[1].") FROM ".PREFIX.$filter[0];
		$query .= isset($filter[6]) && !empty($filter[6]) ? " WHERE ".$filter[5] : "";
		if ($result = db() -> query($query)) { $i = 0;
			while($r = dbfetch($result, false)) {
				$fg = $r[$filter[1]];
				if (isset($get[$filter[2]]) && intval($get[$filter[2]]) == $r[$filter[1]] && isset($filter[7])) {echo ' - '.$list[$i];}
				else if (isset($get[$filter[2]]) && $get[$filter[2]] == $r[$filter[1]]) {echo ' - '.$list[$r[$filter[1]]];}
				else {
					echo ' - <a href="'.$link.pre_seao($filter[2], strval($fg)).$pn.$ukey.'">';
					echo $list[$r[$filter[1]]].'</a>';
				} $i++;
			} unset($result);
		} echo '</p>';
	}
}

// CORRECT URL
function fixUrl($url) {
	if (HTACCESS) {
		$url = preg_replace("#(\?page\=)(\w+?)#siU", "page/$2/", $url);
	} return $url;
}

// GENERATE SEF TITLE
function genSEF($string) {
	foreach (t('special_chars') as $key => $value) {$string = preg_replace('/'.$key.'/', $value, $string);}
	$string = str_replace(' ', '-', $string);
	$string = preg_replace('/[^0-9a-zA-Z-_]/', '', $string);
	$string = str_replace('-', ' ', $string);
	$string = preg_replace('/^\s+|\s+$/', '', $string);
	$string = preg_replace('/\s+/', ' ', $string);
	$string = str_replace(' ', '-', $string);
	return strtolower($string);
}

// LOAD ADMIN IMAGES
function load_icon($var) {
	switch($var) {
		case 'img_edit' : $img = 'edit.png'; break;
		case 'img_browse' : $img = 'folder.png'; break;
		case 'img_admin' : $img = 'browse.png'; break;
		case 'img_install' : $img = 'install.png'; break;
		case 'img_clean' : $img = 'clean.png'; break;
		case 'img_pages' : $img = 'pages.png'; break;
		case 'img_delete' : $img = 'delete.png'; break;
		case 'img_noaccessd' : $img = 'noaccess.png'; break;
		case 'img_up': $img = 'up.png'; break;
		case 'img_minus': $img = 'minus.png'; break;
		case 'img_down': $img = 'down.png'; break;
		case 'img_upload' : $img = 'upload.png'; break;
		case 'img_exclamation' : $img = 'exclamation.png'; break;
	} return 'js/ebookcms/'.$img;
}

// RETURN ARRAY LIST
function getDataArray($table, $where, $id, $field, $delete, $recall) {
	$where1 = !empty($where) ? " WHERE ".$where.';' : ';';
	$where1 = str_replace('%id', $id, $where1);
	$sql = "SELECT id, $field FROM ".PREFIX.$table.$where1;
	if ($res1 = db() -> query($sql)) {
		while ($r = dbfetch($res1)) {
			$key = $r[$field];
			$delete[$key] .= empty($delete[$key]) ? $r['id'] : ','.$r['id'];
			if (isset($recall[$key]) && $recall[$key] == 'true') {
				$delete = getDataArray($table, $where, $r['id'], $field, $delete, $recall);
			}
		} unset($res1);
	} return $delete;
}

// DELETE HERITED DATA
function delete_herited($table, $where, $id, $field, $commands, $herited = '') { global $get;
	if ($get['action'] != 'delete') {return;}
	$where1 = !empty($where) ? " WHERE ".$where.';' : ';';
	$where1 = str_replace('%id', $id, $where1);
	if (isset($field) && !empty($field) && isset($commands) && !empty($commands)) {
		$lcontrol = explode('|', $commands);
		for ($i = 0; $i < count($lcontrol); $i++) {
			$key = substr($lcontrol[$i], 0, strpos($lcontrol[$i], '@'));
			$control[$key] = substr($lcontrol[$i], strpos($lcontrol[$i], '@')+1);
			$xcontrol[$key] = substr($lcontrol[$i], strrpos($lcontrol[$i], '@')+1);
			$delete[$key] = empty($herited) ? '' : $herited[$key];
		}
		$delete = getDataArray($table, $where, $id, $field, $delete, $xcontrol);
		foreach ($control as $key => $value) {
			$mlist = explode(',', $delete[$key]);
			$mcontrol = explode('@', $value); $sqx = '';
			for ($i=0; $i < count($mlist); $i++) {
				$sqx .= $i == 0 ? '('.$mcontrol[1].' = '.$mlist[$i] : ' OR '.$mcontrol[1].' = '.$mlist[$i];
			} $sqx .= ')';
			$mcontrol[2] = str_replace('%content', $sqx, $mcontrol[2]);
			$qw1 = "DELETE FROM ".PREFIX.$mcontrol[0];
			if (!empty($mcontrol[2])) {$qw1 .= " WHERE ".$mcontrol[2].';';}
			if ($res2 = db() -> query($qw1)) {$rr = dbfetch($res1);}
		}
	}
	$query = "DELETE FROM ".PREFIX.$table.$where1;
	if ($result = db() -> query($query)) {$r = dbfetch($result);}
	unset($result);
}

// GENERATE TAGS FROM TEXT
function generateTags($text, $leng = 5) {
	$text = genSEF($text); $text = str_replace('-', ' ', $text);
	$records = array_count_values(str_word_count($text, 1)); $count = 0;
	$list = array(); $max = 1; $tags = ''; $total = 0; $miss = 0;
	foreach($records as $key => $record) {
		if (intval($record) > 1  && strlen($key)>4) {
			$result[$key] = $record;
			if (empty($list['txt'][$record])) {
				$list['txt'][strtolower($record)] = $key;
				$list['count'][$record] = 1;}
			else {$list['txt'][$record] .= ','.$key; $list['count'][$record]++;}
			if ($max < $record) {$max = $record;}
			$count++;
		}
	}
	for ($i = $max; $i>0; $i--) {
		$num = !empty($list['count'][$i]) ? $list['count'][$i] : 0;
		if ($total + $num < $leng) {
			if (!empty($list['txt'][$i])){$tags = empty($tags) ? $list['txt'][$i] : $tags.','.$list['txt'][$i];}
			$total = $total + $num;
		} else {
			$miss = $leng - $total;
			$sub = explode(',',$list['txt'][$i]);
			for ($j=0; $j<$miss; $j++) {$tags .= ','.$sub[$j];}
			break;
		}
	} unset($list, $sub);
	return $tags;
}

// CREATE A FOLDER USING SERVER PATH
function make_safe_folder($folder){
	$root = getInfo('ROOT').getPHPName();
	$folder = str_replace($root, '', $folder);
	if (DIRECTORY_SEPARATOR=='\\') $folder = str_replace('/', '\\', $folder);
	$folders = explode(DIRECTORY_SEPARATOR , $folder); $path = '.';
    for ($i = 0; $i < count($folders); $i++) {
        $path .= DIRECTORY_SEPARATOR.$folders[$i];
        if (!is_dir($path) && !mkdir($path, 0777, true)) {} else chmod($path, 0777);
    } return true;
}

// DELETE A FOLDER
function delete_safe_folder($folder){
	$dh = opendir($dir);
	if ($dh) {
		while($file = readdir($dh)) {
			if (!in_array($file, array('.', '..'))) {
				if (is_file($dir.$file)) {unlink($dir.$file);}
				else if (is_dir($dir.$file)) {rmdir_files($dir.$file);}
			}
		} rmdir($dir);
	} closedir($dh);
}

// FORMS
function create_form($form) { global $get, $uri, $last, $lget, $luri, $t;
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									C  O  M  M  O  N
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************
	$action = clean($get['action']);
	$taction = isset($form['taction']) ? $form['taction'] : $form['action'];
	$uid = load_value('user_id'); $lang_id = load_value('lang');
	$ukey = $get && isset($get['ukey']) ? pre_seao('ukey', $_SESSION[HURI][erukka('user_security')]) : '';
	$xukey = $get && isset($get['ukey']) ? '&ukey='.$_SESSION[HURI][erukka('user_security')] : '';
	if (isset($form['akey'])) {$akey = drukka($form['akey']);}
	else {$akey = isset($_POST['akey']) ? drukka(clean($_POST['akey'])) : drukka1($_SESSION[HURI]['akey']);}
	$lang = isset($get['lang']) ? pre_seao('lang', $get['lang']) : '';
	$title = isset($form['title']) ? t($form['title']) : t($taction.'_title');
	if (isset($get['option']) && !empty($title)) {$title .= ' - '.t($get['option']);}
	$title = !empty($title) ? '<h1 class="h1_title">'.$title.'</h1>' : '';
	if ($uri[0] != 'admin') {
		$plugin = retrieve_mysql("plugins", "sef_file", '"'.$uri[0].'"', "id"," AND auto_load = 1 AND installed = 1");
		if (!$plugin) {echo '<meta http-equiv="refresh" content="0; url='.pre_seao('error', '404', false, true).'">'; return;}
		$uadmin = $uri[0]; $gadmin = 'admin';
	} else {$uadmin = 'admin'; $gadmin = $form['action'];}
	$op = isset($get['option']) ? pre_seao('option', $get['option']) : '';
	$tb = isset($get['tb']) ? pre_seao('tb', $get['tb']) : '';
	$here = '<a href="'.pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'new').$op.$tb.$lang.$ukey.'">'.t('here').'</a>';
	$subtitle1 = $action == 'list' && isset($form['subtitle1']) && isset($form['edit_rights']) && 
		$form['edit_rights'] == 1 ? str_replace('$here', $here, $form['subtitle1']) : '';
	if (isset($form['limit']) && isset($get['admin'])) {
		$pcount = retrieve_mysql($get['admin'], "", "", "count(id) AS total","");
		$total_fields = isset($pcount) ? $pcount['total'] : 0;
		if ($total_fields > 0 && $total_fields >= $form['limit']) {$subtitle1 = '';}
	}
	$subtitle = !empty($subtitle1) ? '<h3>'.$subtitle1.'</h3>' : '';
	if (!empty($title) || !empty($subtitle)) {
		if (HTML5 && !empty($subtitle1)) {echo '<header>'; if (!empty($subtitle1)) {echo '<hgroup>';}}
		if ($action != 'content') {echo $title.$subtitle1;}
		if (HTML5 && !empty($subtitle1)) {echo '</hgroup>';}
		if ($action != 'moveup' && $action != 'movedown') {
			$txt1 = in_array($taction.'_'.$action, $t) ? t($taction.'_'.$action) : '';
			$txt1 = str_replace('$here', $here, $txt1);
			if (!empty($txt1) && isset($form['subtitle2']) && $action == 'list') {echo '<p>'.$txt1.'</p>';}
		}
		if (HTML5 && !empty($subtitle1)) {echo '</header>';}
		if (($uri[0] == 'admin' && $get['admin'] != $form['action'] && !isset($get['option'])) || !isset($get['ukey']) ||	(isset($get['ukey']) 
		&& drukka($get['ukey']) != drukka($_SESSION[HURI][erukka('user_security')]))) {
			echo '<meta http-equiv="refresh" content="0; url='.pre_seao('error', '404', false, true).'">'; return;
		}
	}
	# BACK VARIABLES
	$back_admin = ''; $back_action = ''; $back_tks = ''; $back_tid = ''; $back_other = '';
	$back_list = isset($form['back_list']) ? explode(',', $form['back_list']) : false;
	$back_to = isset($form['back_to']) ? explode(',', $form['back_to']) : false;
	$back_id = isset($form['back_id']) ? explode(',', $form['back_id']) : false;
	$back_sid = isset($form['back_sid']) ? explode(',', $form['back_sid']) : false;
	# SAVE AND CANCEL
	if ($action == 'save') {
		$fp = isset($_POST['fp']) ? clean($_POST['fp']) : ''; $fp = drukka($fp);
		$fg = isset($_POST['fg']) ? clean($_POST['fg']) : '';
		$pn = isset($_POST['pn']) ? clean($_POST['pn']) : '';
		$gm = isset($_POST['gm']) ? clean($_POST['gm']) : '';
		$lget = isset($_POST[erukka('get')]) ? explode(',', clean($_POST[erukka('get')])) : array('');
		$luri = isset($_POST[erukka('uri')]) ? explode(',', clean($_POST[erukka('uri')])) : array('');
		if (isset($lget)) {for ($i=0; $i < count($luri); $i++) {$last[$luri[$i]] = isset($lget[$i]) ? $lget[$i]: '';}}
	} else {
		$fp = isset($get['fp']) ? $get['fp'] : '';
		$fg = isset($get['fg']) ? $get['fg'] : '';
		$pn = isset($get['pn']) ? $get['pn'] : '';
		$gm = isset($get['gm']) ? $get['gm'] : '';
	}
	$fpp = !empty($fp) ? pre_seao('fp', $fp) : ''; $fgg = !empty($fg) ? pre_seao('fg', $fg) : '';
	$gmm = !empty($gm) ? pre_seao('gm', $gm) : ''; $pnn = !empty($pn) ? pre_seao('pn', $pn) : '';
	$back_other = $fpp.$fgg.$pnn.$gmm.$lang;
	if (isset($form['back_id']) || isset($form['back_sid'])) {
		$back_admin = $back_sid[0]; $back_action = $back_sid[1];
	} else { $back_admin = $get['admin']; $back_action = 'list';}
	if ((isset($_POST['id']) && isset($back_id)) || $action=='delete') {
		$id = $action=='delete' ? $get['tid'] : drukka(clean($_POST['id']));
		$data = retrieve_mysql($back_id[0], $back_id[1], $id, '*', '');
		$sid = isset($data[$back_sid[2]]) ? clean($data[$back_sid[2]]) : false;
	} else {$sid = isset($_POST[$back_sid[2]]) ? drukka(clean($_POST[$back_sid[2]])) : false;}
	$back_tid = isset($sid) && $sid != 0 ? pre_seao('tid', $sid) : '';
	if (isset($form['back_sid'])) {
		$hdata = retrieve_mysql($back_sid[0], 'id', $sid, '*', '');
		if ($hdata) {$back_tks = pre_seao('tks', erukka($hdata[$form['edit_ks']], '', false), true);}
	}
	if ($fp != 0 && isset($data['seftitle'])) {
		if ($fp == 1) {$link = webroot();}
		if ($fp == 2) {$link = pre_seao('page', $data['seftitle'], false, true);}
		if ($fp == 3) {$link = pre_seao('admin', 'root', false, true).pre_seao('fp', '3').$ukey;}
	} else {
		# LAST PAGE WAS A PLUGIN
		if (isset($form['back_to']) && $action == 'save' && $fp == 4) {
			$comment = retrieve_mysql('comments', 'id', $id, 'ptype,plug_id,pcontent_id', '');
			if (isset($comment['ptype']) && $comment['ptype'] == 1 && isset($comment['plug_id'])) {
				$plugin = retrieve_mysql('plugins', 'id', $comment['plug_id'], 'id,sef_file,install_table', '');
				if ($plugin) {
					$vkey = retrieve_mysql($plugin['install_table'], 'id', $comment['pcontent_id'], 'vkey', '');
					$link = pre_seao($plugin['sef_file'], 'view', false, true).pre_seao('action', 'comments');
					$link.= pre_seao('tid', $comment['pcontent_id']);
					$link.= pre_seao('vkey', erukka($vkey['vkey'], '', false));
				}
			}
		} else
		if (isset($form['back_to']) && $action == 'save') {
			if ($fp == 1) {$link = webroot();}
			else {
				$link = pre_seao('admin', $back_to[0], false, true);
				if (isset($back_to[1])) {$link .= pre_seao('action', $back_to[1]);}
				if (isset($back_to[2])) {$bk = explode('@', $back_to[2]);$link .= pre_seao($bk[0], $bk[1]);}
				$link .= $fpp.$ukey;
			}
		} else {
			if (isset($form['back_list']) && $action == 'save') {
				$url1 = isset($back_list[0]) ? $back_list[0] : 'root';
				if ($url1 == 'root') {$url2 = '';} 
				else {$url2 = isset($back_list[1]) ? $back_list[1] : 'list'; $url2 = pre_seao('action', $url2);}
				if (isset($back_list[2])) {
					$u3 = explode('=', $back_list[2]);
					$url3 = pre_seao($u3[0], $u3[1]);
				} else {$url3 = '';}
				$link = pre_seao('admin', $url1, false, true).$url2.$url3.$back_tid.$back_tks.$back_other.$ukey;
			} else {
				$link = pre_seao('admin', $back_admin, false, true).pre_seao('action', $back_action);
				$link .= isset($form['back_option']) ? pre_seao('option', $form['back_option']) : '';
				if (isset($back_to[2])) {$bk = explode('@', $back_to[2]);$link .= pre_seao($bk[0], $bk[1]);}
				$link.= $back_tid.$back_tks.$back_other.$ukey;
			}
		}
	}
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									C  A  N  C  E  L
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************

	if ($_POST && isset($_POST[erukka('cancel')])) {
		echo '<div class="msginfo"><p>'.t('back_content').'</p></div>';
		echo '<meta http-equiv="refresh" content="1; url='.fixUrl($link).'">'; return;
	} else
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									S   A   V   E
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************

	if (($_POST && isset($_POST[erukka('submit')])) || (isset($_POST['adm']) && $_POST['adm'] == 'config')) {
		$id = isset($_POST['id']) ? clean($_POST['id']) : 0; $id = drukka($id); $btid = '';
		$uid = isset($_POST['id']) && $get['admin'] != 'myprofile' && $get['admin'] != 'mypassword' ? pre_seao('tid', $id) : '';
		if (isset($_POST[erukka('tks')])) {$tks = clean($_POST[erukka('tks')]); $tks = drukka($tks);}
		elseif (isset($get['tks'])) {$uid .= pre_seao('tks', erukka($tks, '', false));}
		$tb = isset($_POST['tb']) ? pre_seao('tb', clean($_POST['tb'])) : '';
		$last_action = clean($_POST[erukka('last_action')]); $last_action = drukka($last_action);
		$reserved = ''; $duplicated = ''; $must_redirect = false;
		$parent = isset($_POST['parent']) ? clean($_POST['parent']) : null;
		// VERIFY FIELDS AND PREPARE THEM INTO ARRAY
		for ($i=0; $i<count($form['fields']); $i++) {
			$field = explode(',', $form['fields'][$i]);
			switch ($field[0]) {
				case 'input_text': case 'input_ntext': case 'input_xtext' : case 'input_utext':
				case 'input_dtext': case 'input_dtext1': case 'input_dtext2': case 'input_dtext3':
				case 'input_rukka': case 'input_rukka1': case 'input_rukka2': case 'input_rukka3': case 'input_rukka4': 
				case 'input_float' : case 'input_tags' : case 'input_int': case 'input_dxtext': 
					$inputText = isset($_POST[erukka($field[1])]) ? clean($_POST[erukka($field[1])]) : '';
					if ($field[0] == 'input_ntext') {
						if ($last_action == 'new' && $field[4] == 'true' && empty($inputText)){
							$reserved .= ','.$field[2];}
						if (!empty($inputText)) {$save_fields[$field[1]] = erukka($inputText, '', false);}
					} else {
						if ($field[0] == 'input_float') {$inputText = is_float(floatval($inputText)) ? 
							floatval($inputText) : 0.0;}
						if ($field[0] == 'input_int') {$inputText = is_int(intval($inputText)) ? 
							intval($inputText) : 0;}
						if ($field[0] == 'input_utext') {$inputText = str_replace('&amp;', '&', $inputText);}
						if ($field[0] == 'input_dtext') {$inputText = rencrypt($inputText);}
						if ($field[0] == 'input_dtext1'){$inputText = rencrypt1($inputText);}
						if ($field[0] == 'input_dtext2'){$inputText = rencrypt($inputText, $akey);}
						if ($field[0] == 'input_dtext3'){$inputText = rencrypt($inputText, '', false);}
						if ($field[0] == 'input_rukka'){$inputText  = erukka($inputText);}
						if ($field[0] == 'input_rukka1'){$inputText = erukka1($inputText);}
						if ($field[0] == 'input_rukka2'){$inputText = erukka($inputText, $akey);}
						if ($field[0] == 'input_rukka3'){$inputText = erukka($inputText, '', false);}
						if ($field[0] == 'input_rukka4'){$inputText = erukka($inputText, $akey, false);}
						if ($field[0] == 'input_tags' && empty($inputText)) {
							if (isset($save_fields['texto1'])){
								$inputText = isset($save_fields['texto1']) ?
									generateTags($save_fields['texto1']) : $inputText;
							} else if (isset($save_fields['texto2'])) {
								$inputText = isset($save_fields['texto2']) ?
									generateTags($save_fields['texto2']) : $inputText;
							}
						}
						if ($field[4] == 'true' && empty($inputText)) {$reserved .= ','.$field[2];}
						$save_fields[$field[1]] = $inputText;
					} $param_field[$field[1]] = 's'; break;
				case 'input_password':
					$inputText = $_POST[erukka($field[1])];
					if ($field[4] == 'true' && empty($inputText)) {$reserved .= ','.$field[1];}
					$save_fields[$field[1]] = md5($inputText); 
					$param_field[$field[1]] = 's'; break;
				case 'input_password1': case 'input_password2': case 'input_password3':
					$inputText = $_POST[erukka($field[1])];
					if ($field[4] == 'true' && empty($inputText)) {$reserved .= ','.$field[1];}
					break;
				case 'create_password':
					$inputText = isset($_POST[erukka($field[1])]) ? $_POST[erukka($field[1])] : '';
					if (!empty($inputText)) {$save_fields[$field[1]] = erukka1(md5($inputText)); $param_field[$field[1]] = 's';}
				break;
				case 'reset_password':
					$inputText = isset($_POST[erukka($field[1])]) ? $_POST[erukka($field[1])] : '';
					if (!empty($inputText)) {$save_fields[$field[1]] = erukka1(md5($inputText)); $param_field[$field[1]] = 's';}
				break;
				case 'checkboxscrip': case 'checkbox': 
					$save_fields[$field[1]] = isset($_POST[$field[1]]) ? 1 : 0;
					$param_field[$field[1]] = 'i'; break;
				case 'combotable':
					$value = isset($_POST[$field[1]]) ? clean($_POST[$field[1]]) : '';
					if ($field[4] == 'true' && (empty($value)|| $value == 0)) {$reserved .= ','.$field[2].'1';}
					$save_fields[$field[1]] = $value; $param_field[$field[1]] = $field[11]; break;
				case 'combolist': case 'comboscript':
					$value = isset($_POST[$field[1]]) ? clean($_POST[$field[1]]) : '';
					if ($field[4] == 'true' && (empty($value)|| $value == 0)) {$reserved .= ','.$field[2].'1';}
					$save_fields[$field[1]] = $value;
					$param_field[$field[1]] = isset($field[7]) ? $field[7] : 'i'; break;
				case 'comboimage': case 'bcomboimage': case 'dcomboimage':
					if (isset($_POST[$field[1]])){
						$value = clean($_POST[$field[1]]); $save_fields[$field[1]] = $value; 
						$param_field[$field[1]] = 's'; 
					} break;
				case 'show_folders': 
					$value = clean($_POST[$field[1]]);
					$save_fields[$field[1]] = $value;
					$param_field[$field[1]] = 's';
				break;
				case 'upload_files':
					ini_set('memory_limit', '20M');
					$max = $field[2]; $n = 0;
					echo '<h3>'.t('upload_place').'</h3>';
					$sucess = true;
					$upload_ok1 = false;
					$upload_ok2 = true;
					# THUMB SIZE
					$thumbWidth = load_cfg('thumbWidth');
					$thumbHeight = load_cfg('thumbHeight');
					$target = isset($_POST['folder']) ? clean($_POST['folder']).'/' : '';
					$dir = isset($_POST[$field[4]]) ? $field[1].'/'.$_POST[$field[4]].'/' : $field[1].'/'.$target;
					$dir_thumb = $dir.'thumbs/';
					if (empty($target) || !is_dir($dir)) {echo '<div class="msgerror"><h3>'.t('no_tfolder').'</h3>'; break;}
					$type_files = isset($field[5]) ? $field[5] : "gif|jpg|bmp|png";
					$allowedExts = explode('|', $type_files);
					for ($j = 0; $j < (int)$max; $j++) { $error = 0;
						if (!empty($_FILES['filename'.$j]['name'])) {$n++;}
						$filetemp = $_FILES['filename'.$j]['tmp_name'];
						$imagefile = $_FILES['filename'.$j]['name'];
						$title = clean(substr($imagefile,0,strpos($imagefile,'.')));
						$filename = genSEF($title).strtolower(strrchr($imagefile, '.'));
						$temp = explode(".", $imagefile); $extension = end($temp);
						
						if (!empty($filetemp) && !is_uploaded_file($filetemp)) {$error = 1;} else
						if (!empty($filename) && in_array($extension, $allowedExts)) {
							$filetype = isset($_FILES['imagefile'.$j]) ? $_FILES['imagefile'.$j]['type'] : '';
							if (!is_file($dir.$filename) && !empty($filename)) {
								$ok = move_uploaded_file($filetemp, $dir.clean($filename));
								if (!$ok && isset($_FILES['upfile']['error'])) {
									switch ($_FILES['upfile']['error']) {
								        case UPLOAD_ERR_OK: $error = 1; break;
								        case UPLOAD_ERR_NO_FILE: $error = 2; break;
								        case UPLOAD_ERR_INI_SIZE: $error = 3; break;
								        case UPLOAD_ERR_FORM_SIZE: $error = 4; break;
								        default: $error = 5;
								    }
								} # Upload failed
								$upload_ok1 = $ok ? true : false;
							} else if (file_exists($dir.$filename)) {$error = 7; $upload_ok1 = false;} # File Exists
							else {$error = 9;  $upload_ok1 = false;} # Other error
							# CREATE THUMB image
							if ($upload_ok1 && is_dir($dir_thumb)) {
								# TRY GDI
								if (isset($field[3]) && $field[3] == 'true' && in_array($extension, $allowedExts)) {
									list($width, $height, $itype) = getimagesize($dir.$filename); 
							      	// calculate thumbnail size
							      	$new_width = $thumbWidth;
							      	$new_height = floor($height * ($thumbWidth / $width));
							      	$image_p = imagecreatetruecolor($new_width, $new_height);
									switch ($itype){
										case 1 : $image = imagecreatefromgif($dir.$filename); break;
										case 2 : $image = imagecreatefromjpeg($dir.$filename); break;
										case 3 : $image = imagecreatefrompng($dir.$filename); break;
										case 6 : $image = imagecreatefromwbmp($dir.$filename); break;
									}
									imagecopyresampled($image_p,$image,0,0,0,0,$new_width,$new_height,$width,$height);
									switch ($itype){
										case 1 : imagegif($image_p, $dir_thumb.$filename); break;
										case 2 : imagejpeg($image_p, $dir_thumb.$filename,100); break;
										case 3 : imagepng($image_p, $dir_thumb.$filename, 0); break;
										case 6 : imagewbmp($image_p, $dir_thumb.$filename, 0); break;
									}
								} else if (!empty($extension)) {$error = 6;  $upload_ok2 = false;}
							}
						} else if (!empty($filename) && !empty($extension)) {$error = 6;} # extension not good
						if ($error != 0) {
							echo '<div class="msgerror"><h4>'; $sucess = false;
							switch ($error) {
								case 1 : echo t('upload_fail').' '.$filename; break;
								case 2 : echo t('miss_upl').' :'.$filename; break;
								case 3 : echo t('img_limit'); break;
								case 4 : echo t('form_exced'); break;
								case 5 : echo t('upl_failerr'); break;
								case 6 : echo t('bad_ext').' '.$filename; break;
								case 7 : echo t('file_exists').' ('.$filename.')'; break;
								case 8 : echo t('thumb_err').' ('.$filename.')'; break;
								default : echo t('other_err');
							} echo '</h4></div>';
						}
					}
					// NO IMAGE SELECTED
					if ($n == 0) {
						echo '<div class="msgerror"><h4>'.t('no_simages').'</h4>';
						echo '<meta http-equiv="refresh" content="3; url='.fixUrl($link).'">';
						return;
					} else
					if ($upload_ok1 == true && $upload_ok2 == true && $sucess == true) {
						echo '<div class="msgsuccess"><h4>'.t('operation_completed').'</h4>';
					} break;
				case 'create_folder': 
					$mkdir = isset($_POST[erukka($field[1])]) ? clean($_POST[erukka($field[1])]) : '';
					if (!is_dir($field[4].'/'.$mkdir)) {
						make_safe_folder($field[4].'/'.$mkdir);
						make_safe_folder($field[4].'/'.$mkdir.'/thumbs');
					} break;
				case 'text_code':
					$value = clean_mysql($_POST[$field[1]]); $save_fields[$field[1]] = $value;
					$param_field[$field[1]] = 's';
					break;
				case 'genSEF':
					$value = clean($_POST[erukka($field[1])]);
					if (empty($save_fields[$field[1]])) {
						$save_fields[$field[1]] = genSEF($save_fields[$field[2]]);
						$reserved = str_replace(','.$field[1], '', $reserved);
					} else {$save_fields[$field[1]] = genSEF($value);}
					$param_field[$field[1]] = 's';
					break;
				case 'save_session' : $_SESSION[HURI][$field[1]] = $field[0]; break;
				case 'choose_date': case 'newfield_date':  case 'choose_datetime':
					$day = isset($_POST[$field[1].'_day']) ? $_POST[$field[1].'_day'] : date('d');
					$month = isset($_POST[$field[1].'_month']) ? $_POST[$field[1].'_month'] : date('m');
					$year = isset($_POST[$field[1].'_year']) ? $_POST[$field[1].'_year'] : date('Y');
					if ($field[0] != 'choose_date') {
						$hour = isset($_POST[$field[1].'_hour']) ? $_POST[$field[1].'_hour'] : date('H');
						$min = isset($_POST[$field[1].'_min']) ? $_POST[$field[1].'_min'] : date('i');
						if (strlen($day) == 1) {$day = '0'.$day;}
						if (strlen($month) == 1) {$month = '0'.$month;}
						if (strlen($hour) == 1) {$hour = '0'.$hour;}
						if (strlen($min) == 1) {$min = '0'.$min;}
						$newdate = $year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':00';
					} else {$newdate = $year.'-'.$month.'-'.$day;}
					if ($field[0] == 'choose_date' || $field[0] == 'choose_datetime' ||
						($last_action == 'new' && $field[0] == 'newfield_date')) {
						$save_fields[$field[1]] = $newdate;}
					$param_field[$field[1]] = 's';
				break;
				case 'text_area': if (isset($field[1]) && isset($_POST['ebook'.$field[1]])) {
					$save_fields[$field[1]] = clean_mysql($_POST['ebook'.$field[1]], false); 
					$param_field[$field[1]] = 's';
				} break;
				case 'newfield': if ($last_action == 'new') {
					$save_fields[$field[1]] = str_replace('@',',', $field[2]); 
					$param_field[$field[1]] = isset($field[3]) ? $field[3] : 's' ;} break;
				case 'savefield': if ($action == 'save') {
					$save_fields[$field[1]] = $field[2]; $param_field[$field[1]] = $field[3];} break;
				case 'same_value': if ($field[1]=='avatar') {$_SESSION[HURI]['avatar'] = $save_fields[$field[2]];}
					$save_fields[$field[1]] = $save_fields[$field[2]]; $param_field[$field[1]] = 's'; break;
				case 'same_newvalue': if ($last_action == 'new') {
					$save_fields[$field[1]] = $save_fields[$field[2]]; $param_field[$field[1]] = $field[3];} break;
				case 'updatefield': if ($last_action == 'edit' && isset($_POST[$field[1]])) {
					$value = clean($_POST[$field[1]]); $value = drukka($value);
					$save_fields[$field[1]] = $value;
					$param_field[$field[1]] = 's';
				} break;
				case 'updatevalue': if ($last_action == 'edit' && isset($_POST[$field[1]])) {
					$save_fields[$field[1]] = clean($_POST[$field[1]]);
					$param_field[$field[1]] = $field[2];
				} break;
				case 'sess_update': $_SESSION[HURI][$field[1]] = $save_fields[$field[2]]; break;
				case 'same_rukka':
					$val = rdecrypt($save_fields[$field[2]], $akey);
					$save_fields[$field[1]] = erukka($val, $akey, false);
					$param_field[$field[1]] = 's';
				break;
				case 'newhidden': if ($last_action == 'new') {
					$newvalue = drukka(clean($_POST[$field[1]]));
					$save_fields[$field[1]] = $newvalue;
					$param_field[$field[1]] = $field[3];}
					break;
				case 'redirect' :
					$redirect = isset($_POST[erukka($field[1], '', false)]) ? 
						drukka($_POST[erukka($field[1],'', false)]) : null;
					if ($redirect) {$must_redirect = true;}
					break;
				case 'encryptRSA':
					$value = $save_fields[$field[2]];
					$save_fields[$field[1]] = erukka($value, '', sfalse); 
					$param_field[$field[1]] = 's'; break;
				case 'multichoice':
					$ilist = isset($field[3]) ? explode(',',t($field[3])): ''; $mlist = '';
					$sublist = isset($_POST[$field[1]]) ? clean($_POST[$field[1]]) : '';
					$newlist = isset($field[6]) ? str_replace('@', ',', $field[6]) : $sublist;
					$start = isset($field[5]) ? $field[5] : 0;
					if ($start !=0) {$mlist = '01';}
					for ($j = $start; $j < count($ilist); $j++){
						$xnum = $j>9 ? $j : '0'.$j;
						if (isset($_POST[$field[1].'_'.$j]) && $_POST[$field[1].'_'.$j] == 'on') {
							$mlist .= $mlist != '' ? ','.$xnum : $xnum;}
					}
					if (empty($mlist)) {$mlist = isset($data[$field[1]]) ? $data[$field[1]] : $newlist;}
					$mlist = str_replace('01,01', '01',$mlist);
					$save_fields[$field[1]] = $mlist; $param_field[$field[1]] = 's';
				break;
				case 'choose_num' : 
					$value = isset($_POST[$field[1]]) ? clean($_POST[$field[1]]) : 0;
					$value = intval($value); $save_fields[$field[1]] = $value; $param_field[$field[1]] = 'i';
				break;
			}
		}
		# CHECK REQUIRED FIELDS
		if (!empty($reserved)) {
			$reserved = substr($reserved, 1); $list = explode(',',$reserved);
			$utks = $id != 0 ? pre_seao('tks', erukka($tks, '', false)) : '';
			$pd = isset($_POST['pd']) ? pre_seao('pd', clean($_POST['pd'])) : '';
			echo '<div class="msgerror"><h3>'.t('required_fields').'</h3>';
			echo '<ul>';
			for ($j=0; $j<count($list); $j++) {echo '<li>'.t($list[$j]).'</li>';}
			echo '</ul></div>';
			$tid = isset($_POST['section_id']) ? pre_seao('tid', clean($_POST['section_id'])) : '';
			$option = isset($_POST['option']) ? pre_seao('option', clean($_POST['option'])) : '';
			$redirect = pre_seao($uadmin, $gadmin, false, true).pre_seao('action', $last_action).$option.$tid.$uid.$utks.$pd.$ukey.$pnn;
			echo '<meta http-equiv="refresh" content="3; url='.fixUrl($redirect).'">';
			for ($i=0; $i<count($form['fields']); $i++) {
				$field = explode(',', $form['fields'][$i]);
				switch ($field[0]) {
					case 'input_ntext': 
					if (isset($save_fields[$field[1]])) {
						$save_fields[$field[1]] = drukka($save_fields[$field[1]]);
						$param_field[$field[1]] = 's';
					} break;
				}
			} $_SESSION[HURI]['tmp'] = isset($save_fields) ? $save_fields : '';	return;
		}
		# CHECK DUPLICATE FIELDS
		$no_duplicates = isset($form['noduplicates']) ? explode(',', $form['noduplicates']) : null; $list = '';
		if ($no_duplicates && $id == 0) {
			for ($i=0; $i<count($no_duplicates); $i++) {
				if (empty($save_fields[$no_duplicates[$i]])) {
					$nqwr = "SELECT ".$no_duplicates[$i]." FROM ".PREFIX.$form['query_table']." WHERE id = $id";
					if ($result = db() -> query($nqwr)) {$qnqwr = dbfetch($result); unset($result);}
					$name = drukka($qnqwr[$no_duplicates[$i]]); unset($qnqwr);
					$where1 = $no_duplicates[$i]." = '".$name."'";
				} else {$where1 = $no_duplicates[$i]." = '".$save_fields[$no_duplicates[$i]]."'";}
				if (is_numeric($id)) {$where1 = empty($where1) ? " id <> '$id'" : $where1." AND id <> '$id'";}
				$qwr = "SELECT id FROM ".PREFIX.$form['query_table']." WHERE ".$where1;
				if ($result = db() -> query($qwr)) {$ddata = dbfetch($result); unset($result);}
				if ($ddata) {$duplicated .= ','.$no_duplicates[$i];}
			}
		}
		# FOUND THEN SHOW ERROR MESSAGE
		if (!empty($duplicated)) {
			$btid = isset($_POST['section_id']) ? pre_seao('tid', clean($_POST['section_id'])) : '';
			$duplicated = substr($duplicated, 1);
			$list = explode(',', $duplicated);
			echo '<div class="msgerror"><h3>'.t('duplicated_fields').'</h3>';
			echo '<ul>';
				for ($j=0; $j<count($list); $j++) {echo '<li>'.t($list[$j]).'</li>';}
			echo '</ul></div>';
			if ($last_action == 'new') {$uid = '';}
			$redirect = pre_seao($uadmin, $gadmin, false, true).pre_seao('action', $last_action).$btid.$uid.$ukey.$pnn;
			echo '<meta http-equiv="refresh" content="3; url='.fixUrl($redirect).'">';
			for ($i=0; $i<count($form['fields']); $i++) {
				$field = explode(',', $form['fields'][$i]);
				switch ($field[0]) {
					case 'input_ntext': $save_fields[$field[1]] = drukka($save_fields[$field[1]]); break;
				}
			} $_SESSION[HURI]['tmp'] = isset($save_fields) ? $save_fields : ''; return;
		}
		# CHANGE YOUR PASSWORD
		if (isset($form['change_password'])) {
			$id = $form['myprofile_id'];
			$pass = explode(',',$form['change_password']);
			$pass1 = erukka1(md5($_POST[erukka($pass[1])]));
			$pass2 = $_POST[erukka($pass[2])];
			$pass3 = $_POST[erukka($pass[3])];
			$qp = "SELECT password FROM ".PREFIX.$form['query_table']." WHERE id = ? AND password = ?";
			if ($result = db() -> prepare($qp)) {
				$result = dbbind($result, array($id, $pass1), 'is');
			 	$vpass = dbfetch($result); unset($result);
			}
			if ($vpass && $pass1 != erukka1(md5($pass2)) && $pass2 == $pass3 && strlen($pass2)>3) {
				$save_fields[$pass[0]] = erukka1(md5($pass2)); $param_field[$pass[0]] = 's';
			} else {
				if (!$vpass) {echo '<div class="msgerror"><h3>'.t('wrong_password').'</h3></div>';}
				else if ($vpass && $pass1 == md5($pass2)) {
					echo '<div class = "msgerror"><h3>'.t('same_password').'</h3></div>';'';
				} else {echo '<div class = "msgerror"><h3>'.t('pass_notmatch').'</h3></div>';}
				$redirect = pre_seao($uadmin, $gadmin, false, true).pre_seao('action', $last_action).$btid.$uid.$ukey.$pnn;
				echo '<meta http-equiv="refresh" content="3; url='.fixUrl($redirect).'">';
				return;
			}
		}
		# CREATE NEW KEYS
		if (isset($form['keys'])) {
			$keys = explode(',',$form['keys']);
			for ($i=0; $i<count($keys); $i++) {
				$newkey = genKey($keys[$i]);
			 	$save_fields[$keys[$i]] = $newkey;
				$param_field[$keys[$i]] = 's';
			}
		}
		# GET MAX ID FOR order_content
		if ($last_action == 'new' && isset($form['order'])) {
			$order_where = isset($form['order_where']) ? ' '.$form['order_where'] : '';
			$ord_list = isset($form['order_fields']) ? explode(',', $form['order_fields']) : '';
			if (!empty($ord_list)) {
				for ($k=0; $k < count($ord_list); $k++) {
					$ord = explode('@', $ord_list[$k]);
					$vord = isset($_POST[$ord[0]]) ? drukka($_POST[$ord[0]]) : '';
					$order_where = str_replace($ord[1], $vord, $order_where);
				}
			}
			$qwr = "SELECT count(".$form['table_order'].") AS total from ".PREFIX.$form['query_table'].$order_where;
			$language = !empty($lang_id) ? ' AND (lang_id='.$lang_id.' OR lang_id=0)' : '';
			$qwr = str_replace('%lang_id', $language, $qwr);
			if ($result = db() -> query($qwr)) {$sql = dbfetch($result); unset($result);}
			$save_fields['order_content'] = $sql['total']+1; unset($sql); $param_field['order_content'] = 'i';
		} $params = '';
		if (isset($form['no_save']) && $form['no_save'] == true) {echo '<meta http-equiv="refresh" content="1; url='.fixUrl($link).'">';return;}
		# NEXT INSERT OR UPDATE
			if ($last_action == 'edit') {
				$ff = "id,order_content";
				$ff.= isset($form['inherited_id']) ? ",".$form['inherited_id'] : "";
				$ff.= isset($form['table_reorder']) ? ",".$form['table_reorder'] : "";
				$prevdata = retrieve_mysql($form['query_table'], "id", $id, $ff,"");
				if (isset($form['table_reorder']) && isset($prevdata[$form['table_reorder']]) && 
					isset($save_fields[$form['table_reorder']]) && 
					$prevdata[$form['table_reorder']] != $save_fields[$form['table_reorder']]) {
					$max_cnt = "count(id) AS total";
					$max_where = " AND ".$form['table_reorder']."=".$save_fields[$form['table_reorder']];
					$getmax = retrieve_mysql($form['query_table'], $form['inherited_id'], "'".$prevdata[$form['inherited_id']]."'", $max_cnt, $max_where);
					$save_fields['order_content'] = $getmax['total']+1; $param_field['order_content'] = 'i';
					$qr = "SELECT id, order_content FROM ".PREFIX.$form['query_table']." 
						WHERE ".$form['inherited_id']."=".$prevdata[$form['inherited_id']];
					$qr .= isset($form['table_reorder']) ? " AND ".$form['table_reorder']."=".$prevdata[$form['table_reorder']] : "";
					$qr .= " AND id<>".$id; $n = 1;
					# RE-ORDER FIELDS
					if ($sqr = db() -> query($qr)) {
						while ($r = dbfetch($sqr)) {
							$qx = "UPDATE ".PREFIX.$form['query_table']." SET order_content=".$n." WHERE id = ".$r['id'].";";
							if ($sx = db() -> query($qx)) {$n++;}
						}
					}
				}
				while (list($key, $value) = each($save_fields)) {
					if (empty($a)) {$a = $key.'=?'; $b = array($value);} else {$a .= ', '.$key.'=?'; $b[] = $value;}
					$params .= isset($param_field[$key]) ? $param_field[$key] : '';
				} $b[] = $id; $params .= 'i';
				$query = "UPDATE ".PREFIX.$form['query_table']." SET ".$a." WHERE id = ?;";
				if ($sql = db() -> prepare($query)) {
					$sql = dbbind($sql, $b, $params);
				}
			} elseif ($last_action == 'new') {
				while (list($key, $value) = each($save_fields)) {
					if (empty($a)) {$a = $key; $b = '?';} else {$a .= ','.$key; $b .= ',?';}
					$c[] = $save_fields[$key];
					$params .= isset($param_field[$key]) ? $param_field[$key] : '';
				}
				$query = "INSERT INTO ".PREFIX.$form['query_table']." (".$a.") VALUES (".$b.");";
				if ($sql = db() -> prepare($query)){
					$sql = dbbind($sql, $c, $params);
				} unset($sql);
				$qwr = "SELECT max(id) AS total from ".PREFIX.$form['query_table'];
				if ($rx = db() -> query($qwr)) {$msq = dbfetch($rx); unset($rx);}
				$id = $msq['total'];
			} unset($_SESSION[HURI]['tmp']);
		echo '<div class="msgsuccess"><p>'.t('update_sucess').'</p></div>';
		if ($parent) {echo '<meta http-equiv="refresh" content="3; url='.pre_seao('page', $parent, false, true).$pnn.'">'; return;}
		echo '<meta http-equiv="refresh" content="1; url='.fixUrl($link).'">';
		return;
	} else


# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									E  D  I  T
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************

	if ($action == 'edit' || $action == 'new') { echo chr(13); $lfeed = chr(13).chr(9);
		$tid = $get && isset($get['tid']) ? clean($get['tid']) : '0';
		if (isset($gadmin) && ($gadmin == 'myprofile' || $gadmin == 'myfirsttime') && $form['myprofile_id']) {
			$id = $form['myprofile_id'];
		} else if (isset($form['key_id'])) {$id = $form['key_id'];}
		else if($action == 'edit' && $tid>0) {$id = $tid;}
		if (HTML5) {echo '<section>';} $charset = 'UTF-8';
		$formid = empty($form['form_id']) ? $gadmin : $form['form_id'];
		$enctype = !empty($form['enctype']) ? ' enctype="'.$form['enctype'].'"' : '';
		$query_fields = isset($form['query_fields']) ? $form['query_fields'] : '*';
		if (isset($id)) {
			$fks = isset($form['edit_ks']) ? $form['edit_ks'] : '';
			if (($gadmin == 'myprofile' || $gadmin == 'myfirsttime') && $form['myprofile_ukey']) {
				$tks = $form['myprofile_ukey'];}
			else {$tks = clean($get['tks']); $tks = drukka($tks);}
			$query = "SELECT * FROM ".PREFIX.$form['query_table']." WHERE id = $id AND $fks = '$tks' LIMIT 1";
			if ($result = db() -> query($query)) {$data = dbfetch($result); unset($result);}
			if (!isset($data)) {echo '<div class="msgerror">'.t('wrong_key').'</div>'; return;}
		}
		# FORM
		$form_action = pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'save').$ukey.$pnn.$lang; $rte = '';
		echo '<div class="ebform_edit">';
		echo '<form method="post" action="'.$form_action.'" id="ff'.$formid.'" class = "oforms" ';
		echo 'accept-charset="'.$charset.'" name="'.$formid.'"'.$enctype.'>'.$lfeed;
		for ($i=0; $i<count($form['fields']); $i++) {
			$field = explode(',', $form['fields'][$i]);
			$fname = isset($field[1]) ? $field[1] : ''; $classname = isset($field[1]) ? "f".$field[1] : '';
			switch ($field[0]) {
				// **************
				// OPEN FIELDSET - 1-Name and "f"+ClassName; 2-Display legend name 3-Enable option Togle 
				// 4-Open(1) close(1)
				// **************
				case 'open_fieldset':
					$legend = $field[2] == '1' ? 
						'<legend class="eb_legend">'.ucfirst(t($fname)).'</legend><br />' : '';
					if ($field[3] == 0 || $field[2] == 0) {
						echo '<fieldset class="'.$classname.'">'.$legend; $display = '';
					} else {
						$display = $field[4] != '1' ? ' style="display: none;"' : '';
						echo '<fieldset class="'.$classname.'"><legend class="eb_legend">';
						echo '<a href="javascript:ftoggle(\''.$fname.'\');">';
						echo ucfirst(t($fname)).'</a></legend><br />';
					} echo '<div id="'.$fname.'"'.$display.'>'.$lfeed; break;
				// **************
				// CHECKBOX - 1-Field, Name 2-Text 3-Enable/Disable for new  4-false add <br>
				// **************	
				case 'checkbox' : case '_checkbox' :
					if (isset($_SESSION[HURI]['tmp'][$field[2]])) {$value = $_SESSION[HURI]['tmp'][$field[2]];}
					else if ($action == 'edit') {
						$value = isset($field[2]) && isset($data[$field[2]]) ? $data[$field[2]] : $field[2];
					} else {$value = $field[3];}
					$checked = $value ? ' checked' : '';
					echo '<input type="checkbox" name="'.$field[1].'" id="'.$field[1].'" value="'.$value.'"';
					echo $checked.'> ';
					echo '<label for="'.$field[1].'">'.t($field[2]).'</label><br />'.$lfeed;
					if (isset($field[4]) && $field[4] != 'true') {echo '<br />';} break;
				// **************
				// COMBOTABLE - 1-Field, Name, id  2-Text   3-Table   4=True/False-Rquired but not index=0 
				//	5-indexes@values   6=sql(where)  7=index0 (0@all)  8=default for new   11-type data for mysqli
				// **************	
				case 'combotable':
					echo '<dl><dt><label for = "'.$field[2].'">'.t($field[2]).'</label></dt>';
					$qfields = str_replace('@', ',', $field[5]); $ffilds = explode('@', $field[5]);
					$fvalues = explode('AND ', $field[6]);
					for ($g=0; $g < count($fvalues); $g++) {
						$vvalue = preg_replace("#[\[](.*)[\]]#sU", "$1", $fvalues[$g]);
						$xval = explode('=', $vvalue);
						if (isset($xval[1]) && isset($data[$xval[1]])) {
							$fvalues[$g] = str_replace('['.$xval[1].']', $data[$xval[1]], $fvalues[$g]);
						}
					} $fvalues = implode('AND ', $fvalues);
					$dorder = str_replace('@',' AND ', $fvalues);
					$cdata = retrieve_mysql($field[3], '', '', $qfields, $dorder, '');
					if (isset($_SESSION[HURI]['tmp'][$field[2]]) && $_SESSION[HURI]['tmp'][$field[2]] == '0') {
						$selected = ' selected = "selected"';
					} else if ($action == 'new') {$selected = $field[8] == '0' ? ' selected = "selected"' : '';}
					else {$selected = isset($data[$ffilds[0]]) && $data[$ffilds[0]] == '0' ? 
						' selected = "selected"' : '';}
					echo '<dd><select name = "'.$field[1].'" id = "'.$field[2].'">';
					$toption = explode('@', $field[7]);
					if ($field[7]) {echo '<option value = "'.$toption[0].'"'.$selected.'>'.t($toption[1]).'</option>';}
					for ($j = 0; $j < count($cdata); $j++) {
						$selected = '';
						$value1 = isset($field[9]) && $field[9] == 1 ? 
							rdecrypt1($cdata[$j][$ffilds[1]]) : $cdata[$j][$ffilds[1]];
						$value2 = isset($cdata[$j][$field[10]]) ? $cdata[$j][$field[10]] : $cdata[$j][$ffilds[0]];
						if (isset($_SESSION[HURI]['tmp'][$field[2]]) && 
							$_SESSION[HURI]['tmp'][$field[2]] == $cdata[$j][$ffilds[0]]) {
							$selected = ' selected = "selected"';
						} else
						if ($action == 'new') {
							$selected = $field[8] == $cdata[$j][$ffilds[0]] ? ' selected = "selected"' : '';
						} else if (isset($data[$field[1]]) && $value2 == $data[$field[1]]) {
						 	$selected = ' selected = "selected"';
						} else {$selected = isset($data[$field[1]]) && $data[$field[1]] == $cdata[$j][$ffilds[0]] 
							? ' selected = "selected"' : '';
						} echo '<option value = "'.$value2.'"'.$selected.'>'.$value1.'</option>';
					}
					echo '</select></dd></dl><br />'.$lfeed; break;
				// **************
				// COMBOLIST - 1-Field, Name, id   2-Text   3-Stringlist   4=True/False-Rquired but not index=0 
				// 5-default
				// **************	
				case 'combolist':
					echo '<dl><dt><label for = "'.$field[2].'">'.t($field[2]).'</label></dt>';
					$qfields = explode(',', t($field[3]));
					if (isset($_SESSION[HURI]['tmp'][$field[1]])) {$value = $_SESSION[HURI]['tmp'][$field[1]];}
					else {$value = isset($data[$field[1]]) ? $data[$field[1]] : $field[5];}
					echo '<dd><select name = "'.$field[1].'" id = "'.$field[2].'">';
					for ($j=0; $j<count($qfields); $j++) {
						if (isset($field[6]) && $field[6] == 1) {
							$selected = $value == $qfields[$j] ? ' selected = "selected"' : '';
							echo '<option value = "'.$qfields[$j].'"'.$selected.'>'.$qfields[$j].'</option>';
						} else {
							$selected = $value == $j ? ' selected = "selected"' : '';
							if ($action == 'new') {$selected = $value == $j? ' selected = "selected"' : '';}
							echo '<option value = "'.$j.'"'.$selected.'>'.$qfields[$j].'</option>';
						}
					}
					echo '</select></dd></dl><br />'.$lfeed; break;
				// **************
				// COMBOLIST WITH SCRIPT
				// **************
				case 'comboscript':
					echo '<dl><dt><label for = "'.$field[2].'">'.t($field[2]).'</label></dt>';
					$qfields = explode(',', t($field[3])); $name = $field[2]; $count = count($qfields);
					if (isset($_SESSION[HURI]['tmp'][$field[1]])) {$value = $_SESSION[HURI]['tmp'][$field[1]];}
					else {$value = isset($data[$field[1]]) ? $data[$field[1]] : $field[5];}
					$opt = isset($field[7]) ? $field[7] : 0;
					echo '<dd><select name = "'.$field[1].'" id="'.$name.'"';
					echo ' onChange = "javascript:chooseplace(\''.$name.'\',\''.$count.'\',\''.$opt.'\');">';
					for ($j=0; $j<count($qfields); $j++) {
						if (isset($field[6]) && $field[6] == 1) {
							$selected = $value == $qfields[$j] ? ' selected = "selected"' : '';
							echo '<option value = "'.$qfields[$j].'"'.$selected.'>'.$qfields[$j].'</option>';
						} else {
							$selected = $value == $j ? ' selected = "selected"' : '';
							if ($action == 'new') {$selected = $value == $j? ' selected = "selected"' : '';}
							echo '<option value = "'.$j.'"'.$selected.'>'.$qfields[$j].'</option>';
						}
					} echo '</select></dd></dl><br />'.$lfeed; break;
				// **************
				// TABS
				// **************		
				case "create_tab" : 
					$tfields = explode('@', $field[2]);
					echo '<div class="'.$field[1].'"><ul>';
					$tab_pos = isset($get['sl']) ? $get['sl'] : 0;
					for ($j = 0; $j < count($tfields); $j++){ $index = $j+1;
						$class1 = $j == $tab_pos ? ' class="current"' : '';
						$class2 = $j == $tab_pos ? ' class="active"' : '';
						echo '<li name="tab" id="ltab_'.$index.'"'.$class1.'>';
						echo '<a name="tab" id="tab_'.$index.'" href="javascript:void(0)" ';
						echo 'onClick="tabs('.$index.');" '.$class2.'>'.t($tfields[$j]).'</a></li>';
					} echo '</ul></div>'; break;
				case 'add_tab':
					$tab_pos = isset($get['sl']) ? intval($get['sl'])+1 : 1;
					$class = $field[1] == $tab_pos ? 'class = "tab_content active"' : 'class = "tab_content"';
					echo '<div name="tab_content" '.$class.' id="tab_content_'.$field[1].'">'; 
					echo '<fieldset><div class="tcontent">'; 
					break;
				case 'close_tab': echo '</div></fieldset></div>'.$lfeed; break;
				// **************
				// INPUTLIST - 1-Field, Name, id   2-Text   3-Stringlist   4-default  5-Use length=2
				// **************	
				case 'multichoice':
					echo '<fieldset><legend> '.ucfirst(t($field[1])).' </legend><br />';
					$nlist = isset($field[6]) ? explode('@',$field[6]) : '';
					$rlist = isset($get['action']) && $get['action'] != 'new' && isset($data) ? 
						explode(',',$data[$field[1]]) : $nlist;
					$ilist = isset($field[3]) ? explode(',',t($field[3])): '';
					$start = isset($field[5]) ? $field[5] : 0;
					for ($j=$start; $j<count($ilist); $j++){
						$checked = (is_array($rlist) && in_array($j, $rlist)) || ($action == 'new' && $field[4]==$j) ?
							' checked' : '';
						echo '<input type="checkbox" name="'.$field[1].'_'.$j.'" id="'.$field[1].'_'.$j.'"';
						echo $checked.'> <label for="'.$field[1].'_'.$j.'">'.$ilist[$j].'</label><br />';
					} echo '</fieldset><br />'.$lfeed; break;
				// **************
				// InputText - 1-FieldName  2-TextTitle  3-className  4-Required, 5-Size , 6-Maxlength, 
				// 7=default for new 8=return
				// **************	
				case 'input_text': case 'input_dtext': case 'input_dtext1': case 'input_dtext2': case 'input_dtext3':
				case 'input_rukka': case 'input_rukka1': case 'input_rukka2': case 'input_rukka3': case 'input_rukka4': 
				case 'input_drtext': case 'input_rtext' : case 'input_ntext': case 'input_utext':
				case 'input_xtext': case 'input_float': case 'input_tags': case 'input_int':
					if ($field[0] == 'input_xtext' && empty($data[$field[2]])) {break;}
					if ($field[0] == 'input_ntext' && $action == 'edit') {break;}
					echo '<dl>';
					if ($action == 'new' && !isset($_SESSION[HURI]['tmp'])) {$value = isset($field[7]) ? 
						$field[7] : '';}
					else if (isset($_SESSION[HURI]['tmp']) || (isset($field[1]) && isset($data) && 
						isset($data[$field[1]]))) {
						$value = isset($_SESSION[HURI]['tmp'][$field[1]]) ? 
							$_SESSION[HURI]['tmp'][$field[1]] : (isset($data[$field[1]]) ? $data[$field[1]] : '');
					} else {$value = '';}
					if ($field[0] == 'input_utext') {$value = str_replace(array('&'), array('&amp;'), $value);}
					if ($field[0] == 'input_dtext')  {$value = rdecrypt($value);}
					if ($field[0] == 'input_dtext1') {$value = rdecrypt1($value);}
					if ($field[0] == 'input_dtext2') {$value = rdecrypt($value, $akey);}
					if ($field[0] == 'input_dtext3') {$value = rdecrypt($value, '', false);}
					if ($field[0] == 'input_rukka' || $field[0] == 'input_rukka3')  {$value = drukka($value);}
					if ($field[0] == 'input_rukka1') {$value = drukka1($value);}
					if ($field[0] == 'input_rukka2' || $field[0] == 'input_rukka4') {$value = drukka($value, $akey);}
					$charf = $field[4] == 'true' ? '* ' : '';
					$size = $field[6] ? ' size = "'.$field[5].'"' : '';
					$maxlength = $field[6] ? ' maxlength = "'.$field[6].'"' : '';
					$read_only = $field[0] == 'input_rtext' || $field[0] == 'input_drtext' || (isset($field[9]) && $field[9 == 'true'])
						? ' readonly = "readonly"' : '';
					$txt = $field[0] == 'input_xtext' ? $data[$field[2]] : t($field[2]);
					echo '<dt><label for = "'.$fname.'">'.$charf.$txt.'</label></dt>';
					if (isset($field[8]) && $field[8] != 'true') {echo '<br />';}
					echo '<dd><input type = "text" name="'.erukka($fname).'" id = "'.$fname.'"';
					echo ' value = "'.$value.'" ';
					echo 'class = "'.$field[3].'"'.$size.$maxlength.$read_only.'></dd>';
					echo '</dl><br />'.$lfeed;
				break;
				// **************
				// InputPassword - 1-FieldName  2-TextTitle  3-className  4-Required, 5-Size , 
				// 6-Maxlength
				// **************	
				case 'input_password': case 'input_password1': case 'input_password2': 
				case 'input_password3': case 'create_password': case 'reset_password':
					if ($action == 'new' && $field[0] == 'reset_password') {continue;}
					$value = isset($field[7]) && $field[0] == 'create_password' ? $field[7] : '';
					$charf = $field[4] == 'true' ? '* ' : '';
					$size = $field[6] ? ' size = "'.$field[5].'"' : '';
					$maxlength = $field[6] ? ' maxlength = "'.$field[6].'"' : '';
					if ($field[0] == 'create_password' && $action == 'edit') {continue;}
					echo '<dt><label for = "'.$field[2].'">'.$charf.t($field[2]).'<dt>';
					echo '<dd><input type = "password" name = "'.erukka($fname).'" value = "'.$value.'"';
					echo ' class = "'.$field[3].'"'.$size.$maxlength.'></dd><br />'.$lfeed;
				break;
				// **************
				// TEXTAREA - 1-FieldName  2-TextTitle  3-className, 4 - maxlimit
				// **************
				case 'text_code': case 'text_rcode' :
					$txt = t($field[2]);
					$maxtxt = isset($field[3]) ? 
					' maxlength = "'.$field[3].'" onkeypress = "return textarea_max(this, \''.$field[3].'\');"' : '';
					if (!empty($txt)) {echo $txt.'<br />';}
					if (isset($_SESSION[HURI]['tmp'][$field[1]])) {$editor_html = $_SESSION[HURI]['tmp'][$field[1]];}
					else {$editor_html = isset($data[$field[1]]) ? $data[$field[1]] : '';}
					$read_only = $field[0] == 'text_rcode' ? ' readonly = "readonly"' : '';
					echo '<textarea name = "'.$fname.'" id = "'.$fname.'"'.$maxtxt.$read_only.'>';
					echo $editor_html.'</textarea><br /><br />'.$lfeed;
					break;
				// **************
				// TEXTAREA eBookRTE(WYSIWYG) - 1-FieldName  2-TextTitle  3-className
				// **************
				case 'text_area':
					$txt = t($field[2]);
					$wwith = defined('RTE_with') ? RTE_with : 550;
					$wheight = defined('RTE_height') ? RTE_height : 300;
					$editor_css = defined('RTE_CSS') ? getInfo('ROOT').RTE_CSS : getInfo('ROOT').'css/content.css';
					$style_css = file_exists('css/style.css') ? getInfo('ROOT').'css/style.css' : '';
					$rte_tb1 = defined('RTE_TB1') ? $field[1].'.toolbar1 += \','.RTE_TB1.'\';' : '';
					$rte_tb2 = defined('RTE_TB2') ? $field[1].'.toolbar2 = \','.RTE_TB2.'\';' : '';
					$rte_tb3 = defined('RTE_TB3') ? $field[1].'.toolbar3 = \','.RTE_TB3.'\';' : '';
					$rte_tb4 = defined('RTE_TB4') ? $field[1].'.toolbar4 = \','.RTE_TB4.'\';' : '';
					$smilley = defined('SMILLEY') ? $field[1].'.smilley = \','.SMILLEY.'\';' : '';
					$smilley_txt = defined('SMILLEY_TXT') ? $field[1].'.smilleytxt = \''.SMILLEY_TXT.'\';' : '';
					$add_cmd = defined('ADD_CMD') ? $field[1].'.addcmde = \''.ADD_CMD.'\';' : '';
					$manual_cmd = defined('MANUAL_CMD') ? $field[1].'.addcmdi = \''.MANUAL_CMD.'\';' : '';
					if (!empty($txt)) {echo $txt.'<br />';}
					$editor_html = isset($data[$field[1]]) ? clean($data[$field[1]]) : '';
					$editor_html = str_replace(array(chr(0),chr(10),"\\",chr(34),chr(39)), 
						array("","","&#92;","&quot;","&#39;"), $editor_html);
					echo $lfeed.'<textarea id = "'.$field[1].'" class="getedit" rows="20" cols="50">';
					echo $editor_html.'</textarea>'.$lfeed;
					echo '<script type = "text/javascript">
						var '.$field[1].' = startEditor(\''.$field[1].'\');
						'.$field[1].'.wwidth = '.$wwith.';
						'.$field[1].'.wheight = '.$wheight.';
						'.$field[1].'.cssfile = \''.$editor_css.'\';
						'.$field[1].'.cssfile1 = \''.$style_css.'\';
						'.$rte_tb1.$rte_tb2.$rte_tb3.$rte_tb4.$smilley.$smilley_txt.$add_cmd.$manual_cmd.'
						'.$field[1].'.displayEditor();
					</script>'.$lfeed;
					$rte .= 'uSubmit(\''.$field[1].'\');';
				break;
				// **************
				// COMBOIMAGE  - 1-ID,name  2-TextTitle  3-Folder  4- Extensions 5-Size width  6-Size height
				// 7-Text for empty combo
				// **************	
				case 'comboimage': case 'bcomboimage': case 'dcomboimage':
					$path = getInfo('ROOT_DIR').'/'.$field[3];
					if ($field[0] == 'dcomboimage') {echo '<li>';}
					echo '<div id = "div'.$field[1].'" class = "preview">';
					echo '<div id = "i'.$field[1].'" class = "preview_image">';
					$width = !empty($field[5]) ? $field[5] : '158px';
					$height = !empty($field[6]) ? $field[6] : '158px';
					$question = !empty($field[7]) ? $field[7] : 'none';
					$newfile = !empty($field[8]) ? $field[8] : '';
					echo '<img id = "img1'.$field[1].'" ';
					if ($action == 'new' && !empty($newfile) && !isset($_SESSION[HURI]['tmp'][$field[2]])){
						echo 'src = "'.$field[3].'/'.$newfile.'"';
					} else
					if (isset($_SESSION[HURI]['tmp'][$field[2]])) {
						echo 'src = "'.$_SESSION[HURI]['tmp'][$field[2]].'"';
					} else {echo (isset($data[$field[1]]) ? 'src = "'.$data[$field[1]].'"' : '');}
					echo ' width = "'.$width.'" height = "'.$height.'"/>';
					echo '</div>';
					echo t($field[2]); $extensions = explode('|', $field[4]);
					echo '<br /><select name = "'.$field[1];
					echo '" onChange = "javascript:chgImg(\'img1'.$field[1].'\',\''.$field[3].'\',this)"> ';
					echo '<option value = "">'.t($question).'</option>';
					if (is_dir($path)) {
						$fd = opendir($path);
						while (($file = readdir($fd)) == true) {
							clearstatcache();
							$ext = strrchr($file, '.');
							if (in_array($ext, $extensions)) {
								if ($action == 'new' && !empty($newfile) && $newfile == $file) {
									$selected = ' selected = "selected"';
									$sfile = $newfile;
								} else
								if (isset($_SESSION[HURI]['tmp'][$field[2]]) && 
									$_SESSION[HURI]['tmp'][$field[2]] == $field[3].'/'.$file) {
									$selected = ' selected = "selected"';
									$sfile = $file;
								} else {
									$sfile = $file;
									$selected = isset($data[$field[1]]) && $data[$field[1]] == $field[3].'/'.$file ? 
										' selected = "selected"' : '';
								} echo '<option value = "'.$field[3].'/'.$sfile.'"'.$selected.'>'.$sfile.'</option>';
								$selected = ''; $sfile = '';
							}
						}
					} echo '</select></div>'.$lfeed;
					if ($field[0] == 'dcomboimage') {echo '</li>';}
					if ($field[0] == 'comboimage') {echo '<br />';}
				break;
				// **************
				// UPLOAD FILES
				// **************
				case 'upload_files':
					ini_set('memory_limit', '20M');
					$max = $field[2];
					for($j = 0; $j < (int)$max; $j++) {
						echo '<input type="file" name="filename'.$j.'" id = "filename'.$j.'" class="upload_files" onchange="checkimage(this);" /><br />';
					} break;
				// **************
				// SHOW FOLDERS
				// **************
				case 'show_folders': 
					if ($handle = opendir($field[3])) {
						$blacklist = array('.', '..', 'thumbs', '.htaccess');
						echo '<dl><dt><label for = "'.$field[2].'">'.t($field[2]).'</label></dt>';
						echo '<dd><select name = "'.$field[1].'" id = "'.$field[2].'">';
						echo '<option value = "none">'.t('none').'</option>';
						while (false !== ($file = readdir($handle))) {
							if (!in_array($file, $blacklist)) {
								$selected = isset($data[$field[1]]) && $data[$field[1]] == $file ? 
									' selected="selected"' : '';
								if (is_dir($field[3].'/'.$file)) {
									echo '<option value = "'.$file.'"'.$selected.'>'.$file.'</option>';
								}
							}
						} closedir($handle);
					} echo '</select></dd></dl><br />'.$lfeed;
				break;
				// **************
				// SHOW IMAGES WITH COMBOBOX
				// **************
				case 'show_images':
					$g1 = implode('|', $uri).'/'.implode('|', $get);
					if (isset($field[3]) && (!is_dir($field[3]) || empty($field[3]))) {return;}
					if ($handle = opendir($field[3])) {
						$blacklist = array('.', '..', 'thumbs', '.htaccess');
						$path = 'images';
						echo '<dl><dt><label for = "'.$field[2].'">'.t($field[2]).'</label></dt>';
						echo '<dd><select name = "'.$field[1].'" id = "'.$field[2].'" onchange="showimages(this.value,\''.$g1.'\')">';
						echo '<option value = "0">'.t('none').'</option>';
						while (false !== ($file = readdir($handle))) {
							if (!in_array($file, $blacklist)) {
								$selected = isset($data[$field[1]]) && $data[$field[1]] == $file ? 
									' selected="selected"' : '';
								if (is_dir($field[3].'/'.$file)) {
									echo '<option value = "'.$file.'"'.$selected.'>'.$file.'</option>';
								}
							}
						} closedir($handle);
					} echo '</select></dd></dl><br />'.$lfeed;
					echo '<div id="images_place">'.t('select_imgs').'</div>';
				break;
				// **************
				// SHOW IMAGES - WITH BOTTOM
				// **************
				case 'show_gallery' :
					$path = isset($field[2]) ? $field[2] : '';
					$folder = $field[3];
					echo '<input type="button" onclick="showgalMin(\''.$path.'\',\''.$folder.'\');" value="'.t($field[1]).'">';
					echo '<div id="images_place">'.t('btn_imgs').'</div>';
				break;
				// **************
				// CREATE FOLDER
				// **************
				case "create_folder":
					$value = ''; $subtxt = ''; $stext = '';
					if ($action == 'new' && !isset($_SESSION[HURI]['tmp'])) {$value = '';}
					else if (isset($_SESSION[HURI]['tmp'])) {
						$value = isset($_SESSION[HURI]['tmp'][$field[1]]) ? $_SESSION[HURI]['tmp'][$field[1]] : '';
					} else {$value = '';}
					echo '<dl><dt><label for = "'.$field[2].'">'.t($field[2]).'</label></dt>';
					echo '<dd><input type = "text" name="'.erukka($fname).'" id = "'.$fname.'"';
					echo ' value = "'.$value.'" ';
					echo 'class = "'.$field[3].'"> '.$stext.$subtxt.'</dd>';
					echo '</dl>';
				break;
				// **************
				// CHOICE DIV
				// **************
				case 'choice_Div':
					$multi = isset($field[4]) ? explode('|',$field[4]) : array();
					$class = !isset($data[$field[2]]) || (intval($data[$field[2]]) != intval($field[3])) 
						&& !in_array($data[$field[2]], $multi)
					? ' style = "display: none" ' : '';
					echo '<div id = "'.$field[1].'"'.$class.'>'.$lfeed;
				break;
				// **************
				// CHECKBOX WITH SHOW/HIDE PLACE - open_Div
				// **************
				case 'checkboxscrip':
					$name = $field[1];
					if (isset($_SESSION[HURI]['tmp'][$field[2]])) {$value = $_SESSION[HURI]['tmp'][$field[2]];}
					else if ($action == 'edit') {
						$value = isset($field[2]) && isset($data[$field[2]]) ? $data[$field[2]] : $field[2];
					} else {$value = $field[3];}
					$checked = $value || $action == 'new' ? ' checked' : '';
					echo '<input type = "checkbox" id = "'.$name.'" name = "'.$name.'" value = "'.$value.'"'.$checked;
					echo ' onClick = "javascript:hideplace(\''.$name.'\',\''.$field[3].'\',\''.$field[4].'\');"';
					echo '><label for = "'.$name.'"> '.t($field[2]).'</label>'.$lfeed;					
					if ($field[4] != 'true') {echo '<br />';}
				break;
				// **************
				// CHOOSE INTEGER
				// **************
				case 'choose_num' : 
					echo '<label for = "_'.$field[2].'"> '.t($field[2]).'</label><br />';
					echo '<select name="'.$field[2].'" id="'.$field[2].'">';
					$min = isset($field[3]) ? $field[3] : 1;
					$max = isset($field[4]) ? $field[4] : 10;
					for ($j = $min; $j <= $max; $j++){
						$selected = ($action == 'new' && isset($field[5]) && $j == $field[5]) || 
						(isset($data) &&  isset($data[$field[2]]) && $j == $data[$field[2]]) ? 
							' selected="selected"' : '';
						$val = $j < 10 ? '0'.$j : $j;
						echo '<option value="'.$j.'"'.$selected.'>'.$val.'</option>';
					} echo '</select><br /><br />';
				break;
				// **************
				// CHOOSE DATE
				// **************	
				case 'choose_date': case 'choose_datetime':
					$name = erukka($field[2]);
					if (isset($_SESSION[HURI]['tmp'][$field[2]])) {$value = $_SESSION[HURI]['tmp'][$field[2]];}
					else {$value = $action == 'edit' && isset($data[$field[1]]) ? $data[$field[1]] : $field[5];}
					$checked = $value ? ' checked' : '';
					$year = date('Y', strtotime($value));
					$month = date('m', strtotime($value));
					$day = date('d', strtotime($value));
					$hour = date('H', strtotime($value));
					$min = date('i', strtotime($value));
					$start = isset($field[3]) ? $field[3] : 2010;
					$end = isset($field[4]) ? $field[4] : 2017;
					echo '<label for = "'.$name.'"> '.t($field[2]).'</label><br />';
					# DAY
					echo '<select name="'.$field[2].'_day" id="'.$name.'">';
					for ($j = 1; $j <= 31; $j++){
						$selected = $j == $day ? ' selected="selected"' : '';
						$val = $j < 10 ? '0'.$j : $j;
						echo '<option value="'.$j.'"'.$selected.'>'.$val.'</option>';
					} echo '</select> ';
					# MONTH
					$lmonth = explode(',', t('month_names'));
					echo '<select name="'.$field[2].'_month">';
					for ($j = 1; $j <= 12; $j++){
						$selected = $j == $month ? ' selected="selected"' : '';
						echo '<option value="'.$j.'"'.$selected.'>'.$lmonth[$j-1].'</option>';
					} echo '</select> ';
					# YEAR
					echo '<select name="'.$field[2].'_year">';
					for ($j = $start; $j <= $end; $j++){
						$selected = $j == $year ? ' selected="selected"' : '';
						echo '<option value="'.$j.'"'.$selected.'>'.$j.'</option>';
					} echo '</select>';
					if ($field[0] == 'choose_datetime') {
						# HOUR
						echo ' - <select name="'.$field[2].'_hour">';
						for ($j = 0; $j <= 23; $j++){
							$selected = $j == $hour ? ' selected="selected"' : '';
							$val = $j < 10 ? '0'.$j : $j;
							echo '<option value="'.$j.'"'.$selected.'>'.$val.'</option>';
						} echo '</select>:';
						# MINUTE
						echo '<select name="'.$field[2].'_min">';
						for ($j = 0; $j <= 59; $j++){
							$selected = $j == $min ? ' selected="selected"' : '';
							$val = $j < 10 ? '0'.$j : $j;
							echo '<option value="'.$j.'"'.$selected.'>'.$val.'</option>';
						} echo '</select><br />';
					}
					echo $lfeed;
				break;
				// **************
				// PAGES
				// **************
				case 'showpage':
					$page =  retrieve_mysql('pages', "id", $data[$field[1]], 'id, seftitle, texto1',"");
					$pagelink = '<a href="'.pre_seao('page', $page['seftitle'], false, true).'" target="_blank">'.t($field[2]).'</a>';
					$plink = isset($field[4]) && $field[4] == 'true' ? $pagelink : t($field[2]);
					echo $plink.'<textarea name = "'.$field[1].'" id = "'.$field[1].'" readonly = "readonly">';
					echo $page[$field[3]].'</textarea><br /><br />'.$lfeed;
				break;
				case 'savenew':
					$query = "UPDATE ".PREFIX.$form['query_table']." SET ".$field[1]."=".$field[2]." WHERE id = ?;";
					$sql = db() -> prepare($query);
					$sql -> execute(array($data['id']));
				break;
				// **************
				// Close Fieldset
				// **************	
				case 'close_fieldset': echo '</div></fieldset>'.$lfeed; break;
				// **************
				// OTHER
				// **************	
				case 'horizontal_line': case 'hr': echo '<hr>'; break;
				case 'br': echo '<br />'; break;
				case 'clear': echo '<div class="clear"></div>'; break;
				case 'choice_Div':
					$multi = isset($field[4]) ? explode('|',$field[4]) : array();
					$class = !isset($data[$field[2]]) || (intval($data[$field[2]]) != intval($field[3])) 
						&& !in_array($data[$field[2]], $multi)
					? ' style = "display: none" ' : '';
					echo '<div id = "'.$field[1].'"'.$class.'>'.$lfeed;
					
				break;
				case 'open_Div':
					$display = (isset($data[$field[2]]) && $data[$field[2]] == $field[3]) ||
						(isset($_SESSION[HURI]['tmp'][$field[2]]) && $_SESSION[HURI]['tmp'][$field[2]] == $field[3]) ||
						($action == 'new' && $field[3] == '1') ? 'inline' : 'none';
					echo '<div';
					if ($field[1]) {echo ' id = "'.$field[1].'" style = "display:'.$display.'"';}
					echo '>'.$lfeed; break;
				case "close_Div": echo '</div>'.$lfeed; break;
				case "newhidden": 
					if ($action == 'new') {
						echo '<input type = "hidden" name = "'.$field[1].'" ';
						echo 'value = "'.erukka($field[2], '', false).'">'.$lfeed;
					} break;
				case 'hiddenfield': 
					echo '<input type = "hidden" name = "'.$field[1].'" ';
						echo 'value = "'.erukka($field[2], '', false).'">'.$lfeed;
				break;
				case "updatefield": 
					if ($action == 'edit') {
						echo '<input type = "hidden" name = "'.$field[1].'" ';
						echo 'value = "'.erukka($field[2], '', false).'">'.$lfeed;} break;
				case "redirect": 
					echo '<input type = "hidden" name = "'.erukka($field[1], '', false).'" ';
					echo 'value = "'.erukka($field[3], '', false).'">'.$lfeed; break;
				case "open_Divclass": echo '<div class="'.$field[1].'">'.$lfeed; break;
				case "open_ul": 
					echo '<ul'; if (isset($field[1])) {echo ' class="'.$field[1].'">';} else {echo '>';} 
					echo $lfeed; break;
				case "close_ul": echo '</ul>'.$lfeed; break;
				case 'show_html': echo $field[1]; break;
				case "none": break;
			}
		}
		// HIDDEN FIELDS
		if (isset($id)) {
			echo '<input type = "hidden" name = "id" value = "'.erukka($id, '', false).'">'.$lfeed;
			echo '<input type = "hidden" name = "'.erukka('tks').'" ';
				echo 'value = "'.erukka($data['key_security'], '', false).'">'.$lfeed;
		}
		if ($get){
			$vget = isset($uri[1]) ? implode(',', $get) : '';
			echo '<input type="hidden" name="'.erukka('get').'" value="'.$vget.'">'.$lfeed;
			$vuri = isset($uri[1]) ? implode(',', $uri) : '';
			echo '<input type="hidden" name="'.erukka('uri').'" value="'.$vuri.'">'.$lfeed;
			
			if (isset($get['admin'])) {echo '<input type = "hidden" name = "adm" value = "'.$get['admin'].'">'.$lfeed;}
			if (isset($get['tid'])) {
				echo '<input type = "hidden" name = "'.erukka('tid').'"';
				echo ' value = "'.erukka($get['tid'], '', false).'">'.$lfeed;
			}
			if (isset($get['pd'])) {echo '<input type = "hidden" name = "pd" value = "'.$get['pd'].'">'.$lfeed;}
			if (isset($get['pn'])) {echo '<input type = "hidden" name = "pn" value = "'.$get['pn'].'">'.$lfeed;}
			if (isset($get['tb'])) {echo '<input type = "hidden" name = "tb" value = "'.$get['tb'].'">'.$lfeed;}
			if (isset($get['fg'])) {echo '<input type = "hidden" name = "fg" value = "'.$get['fg'].'">'.$lfeed;}
			if (isset($get['gm'])) {echo '<input type = "hidden" name = "gm" value = "'.$get['gm'].'">'.$lfeed;}
			if (isset($get['fp'])) {
				echo '<input type = "hidden" name = "fp" value = "'.erukka($get['fp'], '', false).'">'.$lfeed;
			}
			if (isset($get['parent'])) {echo '<input type = "hidden" name = "parent" value = "'.$get['parent'].'">'.$lfeed;}
			if (isset($get['option'])) {echo '<input type = "hidden" name = "option" value = "'.$get['option'].'">'.$lfeed;}
			if (isset($data['section_id'])) {
				echo '<input type = "hidden" name = "ci" value = "'.$data['section_id'].'">'.$lfeed;
			}
			
		}
		if (isset($data) && isset($data['wtype'])) {
			echo '<input type = "hidden" name = "wtype" value = "'.$data['wtype'].'">'.$lfeed;
		}
		if (isset($form['last_command'])) {
			echo '<input type = "hidden" name = "last_command" value = "'.$form['last_command'].'">'.$lfeed;
		}
		echo '<input type = "hidden" name = "lang" value = "'.$lang_id.'">'.$lfeed;
		echo '<input type = "hidden" name = "'.erukka('last_action').'" value = "'.erukka($action, '', false).'">'.$lfeed;
		if (!empty($rte)) {$rte = ' onclick = "'.$rte.'"';} else {$rte = '';}
		echo '<input type = "submit" class = "bigbutton" name = "'.erukka('submit').'" ';
			echo 'value = "'.t('submit').'"'.$rte.'>'.$lfeed;
		if ($form['cancel']) {
			echo '<input type = "submit" class = "bigbutton" name = "'.erukka('cancel').'" ';
			echo 'value = "'.t('cancel').'">'.$lfeed;
		} echo '</form></div>'.$lfeed;
		if (HTML5) {echo '</section>'.$lfeed;}
		echo chr(13); unset($_SESSION[HURI]['tmp']);
	} else
	
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									L  I  S  T 
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************

	if ($action == 'list') {
		table_filterBy($form, 'top_filter_by');
		$mygroup = $_SESSION && isset($_SESSION[HURI][erukka('group_member')]) ? 
			drukka($_SESSION[HURI][erukka('group_member')]) : '0';
		$table_header = !empty($form['table_header']) ? explode(',', $form['table_header']) : array();
		$table_hsize  = !empty($form['table_hsize'])  ? explode(',', $form['table_hsize']) : array();
		$table_fields = !empty($form['table_fields']) ? explode(',', $form['table_fields']) : array();
		$table_cspans = !empty($form['table_fields']) ? explode(',', $form['table_cspans']) : array();
		$query_fields = isset($form['query_fields']) ? $form['query_fields'] : '*';
		$table_where = isset($form['query_where']) ? $form['query_where'] : '';
		$qorder = isset($form['list_order']) && !empty($form['list_order']) ? $form['list_order'] : '';
		if (isset($form['list_order']) && !empty($form['list_order'])){
			$qorder = $form['order'] == 1 ? "ORDER BY order_content ASC" : $form['order'];
		} else {$qorder = "";}
		$qorder = isset($form['order']) && $form['order'] == 1 ? " ORDER BY order_content ASC" : "";
		if (empty($qorder) && isset($form['order_by'])) {$qorder = $form['order_by'];}
		$qtotal= "SELECT count(id) AS total FROM ".PREFIX.$form['query_table'].$table_where;
		if ($rcount = db() -> query($qtotal)) {$r = dbfetch($rcount); $total = $r['total']; unset($rcount);} 
			else {$total = 0;}
		$query = "SELECT $query_fields FROM ".PREFIX.$form['query_table'].$table_where." ".$qorder;
		$limit_link = '<a href="'.pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'list').$ukey;
		$limit = isset($form['limits']) && is_numeric($form['limits']) ? $form['limits'] : 10;
		$total_fields = !empty($limit) ? ceil($total/$limit) : ceil($total);
		$section_id = isset($form['section_id']) ? $form['section_id'] : '';
		$no_access = '<td class="center"><img src="'.load_icon('img_noaccessd').'" alt="'.t('no_access').'" /></td>';
		$pageNum = !empty($get['pn']) ? $get['pn'] : ''; $search_query = '';
		if (empty($pageNum)) {$pageNum=1;} else
		if (!empty($pageNum) && $total_fields+1 == $pageNum) {$pageNum = $total_fields;}
		$pn = $pageNum>1 ? pre_seao('pn', $pageNum) : '';
		$current = isset($pageNum) && is_numeric($pageNum) ? $pageNum : (empty($pageNum) ? 1 : 0);
		if ($current == 0 || ($current > $total_fields && $total_fields!=0)) {
			echo t('error_404'); unset($data); return;
		}
		$query .= !empty($limit) ? ' LIMIT '.($current - 1) * $limit.','.$limit : ''; 
		$k = !empty($limit) ? ($current-1)*$limit + 1 : 1;
		$result = db() -> query($query);
		#   *****   T A B L E   **********
		echo '<table border="1" width="100%" cellspacing="0" class="table_ebook">';
		// Header
		echo '<tr>';
		for ($i=0; $i<count($table_header); $i++) {
		 	$col_spans = $table_cspans[$i] == '1' ? '' : ' colspan="'.$table_cspans[$i].'"';
			echo '<th';
			echo isset($table_hsize[$i]) ? ' width="'.$table_hsize[$i].'" ': '';
			echo ' scope="col"'.$col_spans;
			echo '>'.$table_header[$i].'</th>';
		} echo '</tr>'; $k = 1; $kk = ($current - 1) * $limit;
		// EMPTY ROWS
		if (isset($total) && $total == 0) {
			echo '<tr>';
			for ($i=0; $i<count($table_fields); $i++) {echo '<td class="center">&hellip;&nbsp;</td>';} 
			echo '</tr>';
		}
		// VALUES ROWS
		while ($r = dbfetch($result)) {
			if ($k%2) {echo '<tr>';} else {echo '<tr class="even">';}
			// MAKE SURE ORDER_CONTENT IS ALLRIGHT
			if (isset($form['order']) && $r['order_content'] != ($kk+$k)) {
			 	$order_content = $kk+$k;
				$sql = "UPDATE ".PREFIX.$form['query_table']." SET order_content = ? WHERE id = ?;";
				$res = db() -> prepare($sql);
				$res -> execute(array($order_content, $r['id']));
			}
			for ($i=0; $i<count($table_fields); $i++) {
				$dks = isset($form['delkey']) && isset($r[$form['delkey']]) ? 
					pre_seao('dks', erukka($r[$form['delkey']], '', false)) : '';
				$tid = $r['id'];
				$id = isset($r['key_security']) ? pre_seao('tid', $tid) : ''; 
				$tks = pre_seao('tks', erukka($r['key_security'], '', false));
				$edit = '<a href="'.pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'edit');
				$edit .= $id.$op.$tb.$pn.$gmm.$fgg.$fpp.$tks.$ukey.'" title="'.t('edit').'">';
				if (isset($r['sec_type']) && $r['sec_type'] == 2) {
					$plugin_name = retrieve_mysql('plugins', "id", $r['plug_id'], 'sef_file',"");
					$content = '<a href="'.pre_seao('admin', $plugin_name['sef_file'], false, true).pre_seao('action', 'list');
				} else {$content = '<a href="'.pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'content');}
				$content.= $id.$tks.$tb.$ukey.$pn.'" title="'.t('content').'">';
				$delete = '<a href="'.pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'delete');
				$delete.= $id.$op.$dks.$tb.$ukey.$pn.'" title="'.t('delete').'"';
				switch ($table_fields[$i]) {
					case 'id': echo '<td class="center">'.$r['id'].'</td>'; break;
					case '%order': echo '<td class="center">';
						$allowed = isset($form['edit_rights']) && $form['edit_rights'] == 1 ? true : false;
						if (!$allowed) {echo '<img src="'.load_icon('img_minus').'" alt="'.t('none').'" />'; break;}
						$up = $r['order_content'] == 1 || $kk == 1 ? 
							'<img src="'.load_icon('img_minus').'" alt="'.t('none').'" />' :
							'<a href="'.pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'moveup').
							pre_seao('oc', $r['order_content']).$ukey.$pn.
							'" title="'.t('move_up').'"><img src="'.load_icon('img_up').'" alt="'.t('move_up').'" /></a>';
						$link_down = pre_seao($uadmin, $gadmin, false, true).pre_seao('action', 'movedown');
						$link_down.= pre_seao('oc', $r['order_content']).$ukey.$pn;
						$down = $kk+$k == $total ? 
							'<img src="'.load_icon('img_minus').'" alt="'.t('none').'" />' : 
							'<a href="'.$link_down.'" title="'.t('move_down').'"><img src="'.load_icon('img_down').
								'" alt="'.t('move_down').'" /></a>';
						echo $up.' '.$down;
						echo '</td>'; break;
					case '%edit':
						$allowed = isset($form['edit_rights']) && $form['edit_rights'] == 1 ? true : false;
						$edit_list = isset($form['edit_id']) ? explode(',',$form['edit_id']) : '';
						$list = !empty($edit_list) && in_array($r['id'], $edit_list) ? false : true;
						$group_list = isset($form['edit_group']) ? explode(',',$form['edit_group']) : '';
						$group = !empty($group_list) && isset($form['key_group']) && isset($r[$form['key_group']]) 
							&& in_array($r[$form['key_group']], $group_list) ? false : true;
						if ($allowed && $list && $group) {
							echo '<td class="center">'.$edit.'<img src="'.load_icon('img_edit').'" ';
							echo 'alt="'.t('edit').'" /></a></td>';
						} else {echo $no_access;}
					break;
					case '%delete':
						$allowed = isset($form['delete_rights']) && $form['delete_rights'] == 1 ? true : false;
						$delete_list = isset($form['delete_id']) ? explode(',',$form['delete_id']) : '';
						$list = !empty($delete_list) && in_array($r['id'], $delete_list) ? false : true;
						$group_list = isset($form['delete_group']) ? explode(',',$form['delete_group']) : '';
						$group = !empty($group_list) && isset($form['key_group']) && 
							in_array($r[$form['key_group']], $group_list) ? false : true;

						$delete_id = isset($form['delete_id']) ? $form['delete_id'] : '';
						if ($allowed && $list && $group) {
							$deltxt = $taction.'_delete';
							$delete .= !empty($deltxt) ? 'onClick="return confirm(\''.t($deltxt).'\');">' : '>';
							if ($delete_id != $r['id']) {}
							echo '<td class="center">'.$delete;
							echo '<img src="'.load_icon('img_delete').'" alt="'.t('delete').'" /></a></td>';
						} else {echo $no_access;}
					break;
					case '%content':
						$link = $r[$section_id] == 0 ? $no_access :
							'<td class="center">'.$content.'<img src="'.load_icon('img_admin').'" alt="'.
								t('content').'" /></a></td>';
						echo $link;
					break;
					case '%install':
						$image = '<img src="'.load_icon('img_install').'" alt="'.t('install').'" />';
						$action = pre_seao('action', 'install');
						$link = $action.pre_seao('tid', $r['id']).pre_seao('tks', erukka($r[$form['edit_ks']], '', false));
						$install = '<td class="center"><a href="'.pre_seao($uadmin, $gadmin, false, true).$link.$ukey;
						$install .= '">'.$image.'</a></td>';
						$option = $r['installed'] ? $no_access : $install;
						echo $option;
					break;
					case '%dtable':
						$image = '<img src="'.load_icon('img_delete').'" alt="'.t('drop_plugin').'" />';
						$action = pre_seao('action', 'dtable');
						$dropdtxt = t('iplugins_del');
						$link = $action.pre_seao('tid', $r['id']).pre_seao('dks', erukka($r[$form['edit_ks']],'', false));
						$drop = '<td class="center"><a href="'.pre_seao($uadmin, $gadmin, false, true).$link.$ukey;
						$drop .= '" title="'.t('drop_plugin').'"';
						$drop .= ' onClick="return confirm(\''.$dropdtxt.'\');">'.$image.'</a></td>';
						$option = $r['installed'] ? $drop : $no_access;
						echo $option;
					break;
					case '%ctable':
						$image = '<img src="'.load_icon('img_clean').'" alt="'.t('empty_table').'" />';
						$action = pre_seao('action', 'ctable');
						$dropdtxt = t('iplugins_clear');
						$link = $action.pre_seao('tid', $r['id']).pre_seao('dks', erukka($r[$form['edit_ks']],'', false));
						$clear = '<td class="center"><a href="'.pre_seao($uadmin, $gadmin, false, true).$link.$ukey;
						$clear .= '" title="'.t('empty_table').'"';
						$clear .= ' onClick="return confirm(\''.$dropdtxt.'\');">'.$image.'</a></td>';
						$option = $r['installed'] ? $clear : $no_access;
						echo $option;
					break;
					case '%padmin':
						$image = '<img src="'.load_icon('img_admin').'" alt="'.t('admin').'" />';
						$action = pre_seao('action', 'list');
						$clear = '<td class="center"><a href="'.pre_seao('admin', $r[$form['padmin']], false, true).$action.$ukey;
						$clear .= '">'.$image.'</a></td>';
						$option = $r['installed'] ? $clear : $no_access;
						echo $option;
					break;
					case '[x1]': 
						if (isset($form['[x1]'])){
							$extlist = explode(',', $form['[x1]']);
							$name = retrieve_mysql($extlist[1], $extlist[2], $r[$extlist[0]], $extlist[3]);
							echo '<td class="center">'.$name[$extlist[3]].'</td>';
						} else {echo '<td class="center">NA</td>';}
					break;
					default: $cfield = $table_fields[$i]; $cf = true;
						if (substr($cfield,0,1) == '@') {
							$cfield = substr($cfield,1);
							echo '<td class="center">'.rdecrypt1($r[$cfield]).'</td>';
						} else
						if (substr($cfield,0,1) == '$') {
							$cfield = substr($cfield,1);
							echo '<td class="center">'.drukka($r[$cfield]).'</td>';
						} else
						if (substr($cfield,0,1) == '#') {
							$xfield = str_replace('#', '', $cfield);
							$xfield = explode('|',$xfield);
							$lfield = explode(',',t($xfield[0])); $cfield = $xfield[1];
							echo '<td class="center">'.$lfield[$r[$cfield]].'</td>';
						} else {echo '<td class="center">'.$r[$cfield].'</td>';}
					 break;
				}
			} echo '</tr>'; $k++; $kk+1;
		}
		echo '</table>';
		if ($total_fields > 1 && !empty($limit)) {paginator($current, $total_fields, $search_query, $limit_link);}
		# FILTER BY
		table_filterBy($form, 'filter_by');
		# BACK
		if (isset($form['back_list'])) {
			$links = explode(',', $form['back_list']);
			$url_admin = isset($form['admin']) ? $form['admin'] : $uadmin;
			$url1 = isset($links[0]) ? $links[0] : 'root';
			if ($url1 == 'root') {$url2 = '';} 
			else {$url2 = isset($links[1]) ? $links[1] : 'list'; $url2 = pre_seao('action', $url2);}
			echo '<a href="'.pre_seao($url_admin, $url1, false, true).$url2.$ukey.'">'.t('back').'</a>';
		} else
		if (isset($form['back_root']) && $form['back_root'] != false) {
			if (isAllowed('root_access')) {$lnk = pre_seao('admin', 'root', false, true, true);}
			else {$lnk = pre_seao('admin', 'personal_data', false, true, true);}
			echo '<a href="'.$lnk.$ukey.'">'.t('back_root').'</a>';
		} else
		if (isset($form['back_bigroot']) && $form['back_bigroot'] != false) {
			echo '<a href="'.pre_seao('admin', 'bigroot', false, true, true).$ukey.'">'.t('back_bigroot').'</a>';
		} return;
	} else


# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									D  E  L  E  T  E
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************

	if ($action == 'delete') {
		$id = isset($get['tid']) ? $get['tid'] : null;
		$dks = drukka($get['dks']);
		$fdks = isset($form['delkey']) ? $form['delkey'] : 'del_key';
		$parent = isset($get['parent']) ? $get['parent'] : null;
		$order_where = isset($form['order_where']) ? $form['order_where'] : '';
		$data = retrieve_mysql($form['query_table'], 'id', $id, '*', " AND $fdks = '$dks'");
		if (!$data) {echo '<h3>'.t('wrong_key').'</h3>'; return;}
		$language = isset($get['lang']) ? ' AND lang_id = '.$lang_id : ' AND lang_id = 1';
		$order_where = str_replace('%lang_id', $language, $order_where); $n = 0;
		if (isset($form['order_fields'])) {
			$order_fields = explode(',', $form['order_fields']);
			for ($i = 0; $i < count($order_fields); $i++){
				$list = explode('@', $order_fields[$i]);
				if ($data[$list[0]]) {$order_where = str_replace($list[1], $data[$list[0]], $order_where);}
			}
		}
		# DELETE FIELD
		$qwr = "DELETE FROM ".PREFIX.$form['query_table']." WHERE id = ? AND $fdks = ?";
		if ($res = db() -> prepare($qwr)) {$res = dbbind($res, array($id, $dks), 'is');}
		unset($res, $qwr);
		# DELETE HERITED DATA
		if (isset($form['section_id']) && isset($data[$form['section_id']]) && isset($form['del_herited'])){
			if (isset($form['del_herited'][$data[$form['section_id']]])){
				$list = explode(',', $form['del_herited'][$data[$form['section_id']]]);
				$dqwr = "DELETE FROM ".PREFIX.$list[0]." WHERE ".$list[1];
				$dqwr = str_replace('%lang_id', $language, $dqwr);
				$dqwr = str_replace('%id', $id, $dqwr);
				if ($rx = db() -> query($dqwr)){$r = dbfetch($rx);}
			}
		} unset($rx, $dqwr);
		# AUTO RE-ORDER
		if (isset($form['order']) && $form['order'] == 'true') {
			$query = "SELECT id,order_content 
				FROM ".PREFIX.$form['query_table'].$order_where." ORDER BY order_content ASC";
			if ($result = db() -> query($query)){
				while ($r = dbfetch($result)) {
					$id = $r['id']; $n++;
					$sql =  "UPDATE ".PREFIX.$form['query_table']." SET order_content = ? WHERE id = ?";
					if ($q = db() -> prepare($sql)) {$q = dbbind($q, array($n, $id), 'ii'); unset($q);}
				}
			}
		}
		echo '<div class="msgsuccess">'.t('update_sucess').'</div>'; unset($data);
		if ($parent) {echo '<meta http-equiv="refresh" content="1; url='.pre_seao('page', $parent, false, true).$pn.'">';}
		else {echo '<meta http-equiv="refresh" content="1; url='.$link.'">';} unset($form); return;
	} else
	
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									C  O  N  T  E  N  T
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************
	if ($action == 'content') {
		$mygroup = load_value('user_gm');
		$tid = $get['tid']; $id = $tid;
		$tks = drukka($get['tks']);
		$table_fields = $form['content_fields'];
		$no_access = '<td class="center"><img src="'.load_icon('img_noaccessd').'" alt="'.t('no_access').'" /></td>';
		$data = retrieve_mysql($form['query_table'], "id", $id, $table_fields, " AND key_security = '$tks'");
		$edit_id = isset($form['edit_content'][$data['sec_type']]) ? 
			explode(',', $form['edit_content'][$data['sec_type']]) : array('0');
		$delete_id = isset($form['del_content'][$data['sec_type']]) ? 
			explode(',', $form['del_content'][$data['sec_type']]) : array('0');
		if (isset($data)) {
			echo '<h1 class="h1_title">'.$data['title'].'</h1>';
			$allowed = isGroupAllowed($data) ? true : false;
			$add_content = isGroupAllowed($data, 'add_content');
			$only_author = isset($data['only_author']) && $data['only_author'] == 1 && $mygroup != 1 ? true : false;
			$edit_content = isGroupAllowed($data, 'edit_by');
			$delete_content = isGroupAllowed($data, 'delete_by');
			# PLUGIN
			if ($data['sec_type'] == 2) {
				$action = $action == 'content' ? 'list' : $action;
				$plugin = retrieve_mysql('plugins', 'id', '"'.$data['plug_id'].'"', 'id,admin_func', 
					" AND installed = 1");
				if ($plugin) {
					$fname = $plugin['admin_func'];
					if (function_exists($fname)) {call_user_func_array($fname, array($action));}
					else {echo '</div>'; set_error();}
				} return;
			}
			# NON PLUGIN
			else if ($data['sec_type'] != 2) {
				$control = isset($form['control_content']) && isset($data[$form['control_content']]) ? 
					$data[$form['control_content']] : 1;
				$field = isset($form['content'][$control]) ? $form['content'][$control] : '';
				if (empty($field)) {echo '</div>'; return;}
				for ($i=0; $i<count($field); $i++) {
					$content = !empty($field[$i]) ? explode(',', $field[$i]) : '';
					if ($content[0] == 'new_content') {
						$ltable = $content[2];
						$table_where = isset($content[8]) ? $content[8] : '';
						$table_where = str_replace('%id', $data['id'], $table_where);
						$table_where = isset($form['control_subcontent']) && isset($data[$form['control_subcontent']])
							? str_replace('%sid', $data[$form['control_subcontent']], $table_where) : $table_where;
						$table_where = ' WHERE '.$table_where;
						$qtotal = "SELECT count(id) AS total FROM ".PREFIX.$ltable.' '.$table_where;
						$language = isset($get['lang']) ? ' AND lang_id = '.$lang_id : '';
						$qtotal = str_replace('%lang_id', $language, $qtotal);
						if ($rcount = db() -> query($qtotal)) {
							$rtt = dbfetch($rcount); $total = $rtt['total']; unset($rcount);
						}
					}
					$sub_content = isset($data['title']) ? '"'.$data['title'].'"' : t('content');
					if ($content[0] == 'drawtable' || $content[0] == 'filter'){
						$table = $content[0] == 'drawtable' ? $content[12] : $content[2];
						$extract = $content[0] == 'drawtable' ? explode('=', $content[13]) : explode('=', $content[4]);
						$ntotal = retrieve_mysql($table, trim($extract[0]), trim($extract[1]), 'count(id) AS total', "");
					}
					switch ($content[0]) {
						case 'new_content':
							if (isset($content[6])) {
								$wwhere = isset($content[7]) ? ' AND wtype = '.$content[7] : '';
								$language = isset($get['lang']) ? ' AND lang_id = '.$lang_id : ' AND lang_id = 1';
								$max = retrieve_mysql($content[6], 'section_id', $id, 'count(id) AS total', 
									$wwhere.$language);
							}
							$txt = t($content[1]);
							$sid = isset($data[$content[5]]) ? $data[$content[5]] : $id;
							$here = '<a href="'.pre_seao('admin', $content[2], false, true);
							$here .= pre_seao('action', 'new').pre_seao($content[4], erukka($sid, '', false));
							$here .= $lang.$ukey.'" title="'.t('add_content').'">'.t('here').'</a>';
							$txt = str_replace(array('$here', '$sub'), array($here, $sub_content), $txt);
							if (isset($max['total']) && $data['lcontent'] != 0 && $max['total'] >= $data['lcontent']) {
								$txt = '';} else
							if ((isset($form['edit_crights'][$data['sec_type']]) && $form['edit_crights'][$data['sec_type']] == 0) ||
								(isset($data['lcontent']) && $data['lcontent'] != 0 && $data['lcontent'] >= $total)) {$txt = '';}
							else if ($add_content){echo '<p>'.$txt.'</p>';}
						break;
						case 'info': 
							if (isset($content[1])) {echo '<div class="msginfo"><h3>'.t($content[1]).'</h3></div>';}
						break;
						case 'br': echo '<br />'; break;
						case 'drawtable':
							$ltable = explode('@', $content[1]);
							$query_fields = isset($content[4]) ? $content[4] : '*';
							$query_fields = str_replace('@',',',$query_fields);
							$limit = isset($content[10]) ? $content[10] : '10';
							$limit_link = '<a href="'.pre_seao('admin', $gadmin, false, true).pre_seao('action', $get['action']);
							$limit_link .= isset($get['tid']) ? pre_seao('tid', $get['tid']) : '';
							$limit_link .= isset($get['tks']) ? pre_seao('tks', $get['tks']) : '';
							$limit_link .= $ukey;
							$order = isset($content[11]) ? ' '.$content[11] : ' ORDER BY order_content ASC';
							$order = str_replace('@', ',', $order);
							if (isset($ntotal['total']) && $ntotal['total'] != 1 &&	!isset($get['lang']) && 
								isset($form['order_lang']) && $form['order_lang']==true) {
								$order = ' ORDER BY id ASC';}
							$total_fields = !empty($limit) ? ceil($total/$limit) : ceil($total);
							$query = "SELECT $query_fields FROM ".PREFIX.$ltable[0].' '.$table_where.$order;
							$pageNum = !empty($get['pn']) ? $get['pn'] : '';
							if (empty($pageNum)) {$pageNum=1;} else
							if (!empty($pageNum) && $total_fields+1 == $pageNum) {$pageNum = $total_fields;}
							$pn = $pageNum>1 ? pre_seao('pn', $pageNum) : '';
							$current = isset($pageNum) && is_numeric($pageNum) ? $pageNum : (empty($pageNum) ? 1 : 0);
							if ($current == 0 || ($current > $total_fields && $total_fields!=0)) {
								echo t('error_404'); unset($data); return;
							}
							$query .= !empty($limit) ? ' LIMIT '.($current - 1) * $limit.','.$limit : '';
							$query = str_replace('%lang_id', $language, $query);
							$k = !empty($limit) ? ($current-1) * $limit + 1 : 1;
							$result = db() -> query($query);
							$table_header = explode('@',$content[6]);
							$table_fields = explode('@',$content[7]);
							$table_cspans = explode('@',$content[8]);
							$table_hsize = explode('@',$content[9]);
							$link1 = isset($ltable[1]) ? 
								pre_seao('admin', $ltable[1], false, true) : pre_seao('admin', $ltable[0], false, true);
							#   *****   T A B L E   **********
							echo '<table border="1" width="100%" cellspacing="0" class="table_ebook">';
							// Header
							echo '<tr>';
							for ($j=0; $j<count($table_header); $j++) {
							 	$col_spans = $table_cspans[$j] == '1' ? '' : ' colspan="'.$table_cspans[$j].'"';
								echo '<th';
								echo isset($table_hsize[$j]) ? ' width="'.$table_hsize[$j].'" ': '';
								echo ' scope="col"'.$col_spans;
								echo '>'.t($table_header[$j]).'</th>';
							} echo '</tr>'; $k = 1; $kk = ($current - 1) * $limit;
							// EMPTY ROWS
							if ($total == 0) {
								echo '<tr>';
								for ($j=0; $j<count($table_fields); $j++) {
									echo '<td class="center">&hellip;&nbsp;</td>';
								} echo '</tr>';
							}
							// VALUES ROWS
							while ($r = dbfetch($result)) {
								if (isset($r['admin_by']) && !isGroupAllowed($r) && $mygroup != 1) {continue;}
								if ($k%2) echo '<tr>'; else echo '<tr class="even">';
								for ($j=0; $j<count($table_fields); $j++) {
									$id = isset($r['key_security']) ? pre_seao('tid', $r['id']) : '';
									$tks = pre_seao('tks', erukka($r['key_security'], '', false), true);
									$dks = isset($r['del_key']) ? pre_seao('dks', erukka($r['del_key'], '', false)) : '';
									switch ($table_fields[$j]) {
										# ORDER
										case '%order': 
											echo '<td class="center">';
											$minus = '<img src="'.load_icon('img_minus').'" alt="'.t('none').'" />';
											if (!$allowed || $only_author) {echo $minus; break;}
											if (isset($ntotal['total']) && $ntotal['total'] != 1 &&							
												!isset($get['lang']) && isset($form['order_lang']) && 
												$form['order_lang']==true) {echo '&hellip;&nbsp;'; break;}
											$ct = pre_seao('id', $r['id']).pre_seao('oc', $r['order_content']);
											$ct .= pre_seao('tid', $tid).pre_seao('ha', $get['admin']).$ukey.$pn;
											$link_up = $link1.pre_seao('action', 'cmoveup').$ct;
											$up = $r['order_content'] == 1 || $kk == 1 || $total == 1 ? $minus :
												'<a href="'.$link_up.$lang.'" title="'.t('move_up').'">'.
												'<img src="'.load_icon('img_up').'" alt="'.t('move_up').'" /></a>';
											$link_down = $link1.pre_seao('action', 'cmovedown').$ct;
											$down = $kk+$k == $total ? $minus :
												'<a href="'.$link_down.$lang.'" title="'.t('move_down').'">'.
												'<img src="'.load_icon('img_down').'" alt="'.t('move_down').'" /></a>';
											echo $up.' '.$down;
											echo '</td>';
										break;
										# EDIT
										case '%edit':
											if ($allowed) {
												$edit = '<a href="'.$link1.pre_seao('action', 'edit').$id.$tks.$ukey.$pn.$lang.
													'" title="'.t('edit').'">';
												if ($edit_content == 0) {echo $no_access; break;}
												if (isset($r['admin_by']) && !isGroupAllowed($r)) {
													echo $no_access; break;
												}
												if ($only_author == true && isset($r['author_id']) && 
													$r['author_id'] != $uid && !in_array($r['id'], $edit_id)) {
														echo $no_access; break;
												}
												if (!in_array($r['id'], $edit_id)) {
													echo '<td class="center">'.$edit;
													echo '<img src="'.load_icon('img_edit').'" ';
													echo 'alt="'.t('edit').'" /></a></td>';
												} else {echo $no_access; break;}
											} else {echo $no_access;}
										break;
										# DELETE
										case '%delete':
											if ($allowed) {
												$deltxt = $ltable[0].'_delete';
												$delete = '<a href="'.$link1.pre_seao('action', 'delete').$id.$dks.$ukey.$pn.'" ';
												$delete .= 'title="'.t('delete').'"';
												$delete .= !empty($deltxt) ? 
													' onClick="return confirm(\''.t($deltxt).'\');">' : '>';
												if ($delete_content == 0) {echo $no_access; break;}
												if (isset($form['del_section_id'][$data['sec_type']]) && 
													$form['del_section_id'][$data['sec_type']] == $r['id']) {
													echo $no_access; break;
												}
												if (isset($r['admin_by']) && !isGroupAllowed($r)) {
													echo $no_access; break;
												}
												if ($only_author == true && isset($r['author_id']) && 
													$r['author_id'] != $uid && !in_array($r['id'], $delete_id)) {
													echo $no_access; break;
												}
												if (!in_array($r['id'], $delete_id)) {
													echo '<td class="center">'.$delete;
													echo '<img src="'.load_icon('img_delete').'" ';
													echo 'alt="'.t('delete').'" /></a></td>';
												} else {echo $no_access; break;}
											} else {echo $no_access;}
										break;
										# SUB-CONTENT
										case '%content':
											$enabled = isset($r[$content[2]]) && $r[$content[2]] == 0 ? false : true;
											echo '<td class="center">';
											if ($enabled) {
												$xcontent = '<a href="'.pre_seao('admin', $content[12], false, true);
												$xcontent.= pre_seao('action', 'content').$id.$tks.$ukey.$pn.'"';
												$xcontent .= ' title="'.t('content').'">';
												echo $xcontent.'<img src="'.load_icon('img_pages').'" ';
												echo 'alt="'.t('content').'" /></a></td>';
											} else {
												echo '<img src="'.load_icon('img_exclamation').'" ';
												echo 'alt="'.t('content').'" /></td>';
											}
										break;
										# FIELDS
										default: 
											$cfield = $table_fields[$j]; $cf = true;
											if (substr($cfield,0,1) == '#') {
												$xfield = str_replace('#', '', $cfield);
												$xfield = explode('|',$xfield);
												$lfield = explode(',',t($xfield[0])); $cfield = $xfield[1];
												$cf = false;
											}
											if ($cf) {echo '<td>'.$r[$cfield].'</td>';}
											else {echo '<td>'.$lfield[$r[$cfield]].'</td>';}
										break;
									}
								} echo '</tr>'; $k++; $kk+1;
							} break;
						case 'end_table': echo '</table>';
							if ($total_fields > 1 && !empty($limit)) {
								paginator($current, $total_fields, $search_query, $limit_link);
							}
						break;
						case 'filter':
							$admin = isset($get['admin']) ? 'admin' : $uri[0];
							$admin = pre_seao($admin, $get[$admin], false, true);
							$action = isset($get['action']) ? pre_seao('action', $get['action']) : '';
							$tid = isset($get['tid']) ? pre_seao('tid', $get['tid']) : '';
							$tks = isset($get['tks']) ? pre_seao('tks', $get['tks']) : '';
							echo '<p><strong>'.t($content[1]).'</strong>';
							if (isset($content[7]) && isset($content[8])){
								$fields = 'a.'.str_replace('@', ',a.', $content[3]).', count(b.id) AS total ';
								$from = $content[2].' AS a';
								$where = 'a.'.str_replace('@', ',a.', $content[4]);
								$left = ' LEFT OUTER JOIN '.$content[7].' AS b on '.$content[8];
								$left .= isset($get['tid']) ? ' AND b.section_id='.$get['tid'].' ': '';
								$group_by = " GROUP BY a.id";
								$pref = 'a.';
							} else {
								$fields = str_replace('@', ',', $content[3]);
								$from = $content[2];
								$where = str_replace('@', ',', $content[4]);
								$left = ''; $group_by = ''; $pref = '';
							}
							$query = "SELECT $fields FROM ".$from.$left."WHERE ".$where.$group_by;
							$link_root = '<a href="'.$admin.$action.$tid.$tks.$ukey;
							$title = ' title="'.t('filter_by').'"';
							if (isset($ntotal['total']) && $ntotal['total'] > 1){
								if (isset($get['lang']) && isset($content[9]) && $content[9] == 'true' ){
									echo ' '.$link_root.'"'.$title.'> '.t('all').'</a>';
								} else if (isset($content[9]) && $content[9] == 'true'){echo ' '.t('all');}
							}
							if ($result = db() -> query($query)){
								while($r = dbfetch($result)){
									$total = isset($r['total']) ? $r['total'] : 1; $name = $r[$content[6]];
									$lfilter = $link_root.pre_seao($content[5], $r['id']).'"'.$title.' >'.$name.'</a>';
									$link = $total >= 1 && isset($ntotal['total']) && $ntotal['total'] > 1 && 
									(!isset($get[$content[5]]) || isset($get[$content[5]]) && $get[$content[5]] != $r['id']) ? 
										$lfilter : $name;
									if ($total != 0) {echo ' - '.$link;}
								} unset($result);
							} echo '</p><br />'; break;
						case 'backlink':
							$ladmin = isset($content[2]) ? explode('@',$content[2]) : '';
							$fcontrol = isset($content[5]) && !empty($content[5]) ? explode('@',$content[5]) : '';
							if (!empty($fcontrol)){
								$admin = isset($fcontrol[1]) && isset($data[$fcontrol[1]]) && 
									$data[$fcontrol[1]] != 0 ? $ladmin[1] : $ladmin[0];
								$action = isset($content[3]) && !empty($content[3]) ? pre_seao('action', $content[3]) : '';
								$sid = isset($data[$fcontrol[1]]) && $data[$fcontrol[1]] != 0 ? 
									$data[$fcontrol[1]] : $data[$fcontrol[0]];
								$tid = !empty($content[4]) ? pre_seao('tid', $sid) : '';
								$herited = retrieve_mysql($admin, "id", $sid, "id,key_security", "");
								$tks = pre_seao('tks', erukka($herited['key_security'],'', false));
							} else {
								$admin = isset($ladmin[0]) ? $ladmin[0] : $content[2];
								$action = $content[1] == 'false' && isset($content[3]) ? pre_seao('action', $content[3]) : '';
								$tks = ''; $tid = '';
							}
							echo '<a href="'.pre_seao('admin', $admin, false, true).$action.$tid.$tks.$lang.$ukey.'" ';
							echo 'title="'.t('back').'">'.t('back').'</a>';
						break;							
					}
				}
			}
		} return;
	} else
	

# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
# 									CHANGE ORDER MOVE UP/DOWN
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************
	
	if ($action == 'moveup' || $action == 'movedown' || $action == 'cmoveup' || $action == 'cmovedown') {
		if (!isset($form['order'])) {return;}
		$id = isset($get['id']) && is_numeric($get['id']) ? $get['id'] : '';
		$tid = isset($get['tid']) && is_numeric($get['tid']) ? $get['tid'] : '';
		$num = isset($get['oc']) && is_numeric($get['oc']) ? $get['oc'] : null;
		$language = isset($get['lang']) ? ' AND lang_id = '.$lang_id : ' AND lang_id = 1';
		$order_where = isset($form['order_where']) ? $form['order_where'] : '';
		$order_where = str_replace('%tid', $tid, $order_where);
		$ord_list = isset($form['order_fields']) ? explode(',', $form['order_fields']) : '';
		if (!empty($ord_list)) {
			$ks = isset($get['key_security']) ? $get['key_security'] : '';
			$query = "SELECT * FROM ".$form['query_table']." WHERE id = '$id'";
			if ($result = db()-> query($query)){$data = dbfetch($result); unset($result);}
			for ($k=0; $k < count($ord_list); $k++) {
				$ord = explode('@', $ord_list[$k]);
				$order_where = str_replace($ord[1], $data[$ord[0]], $order_where);
			} unset($data);
		}
		if (!empty($order_where)) {$order = " ".$order_where." AND ";} else {$order = " WHERE ";}
		if ($action == 'movedown' || $action == 'cmovedown') { $n1 = $num+1;
			echo '<h3>'.t('move_down').'</h3>';
			$query = "SELECT * FROM ".PREFIX.$form['query_table'];
			$query .= $order."(order_content = $num OR order_content = $n1) ORDER BY order_content DESC LIMIT 2;";
			$query = str_replace('%lang_id', $language, $query);
			if ($result = db() -> query($query)) {
				while ($r = dbfetch($result)) { $id = $r['id'];
				 	$qm = "UPDATE ".PREFIX.$form['query_table']." SET order_content = ? WHERE id = ?";
					if ($qx = db() -> prepare($qm)) {
						$qx = dbbind($qx, array($num, $id), 'ii');
						$num++; unset($qx);
					}
				} unset($result);
			}
		} else
		if ($action == 'moveup' || $action == 'cmoveup') { $n1 = $num-1;
			echo '<h3>'.t('move_up').'</h3>';
			$query = "SELECT * FROM ".PREFIX.$form['query_table'];
			$query .= $order."(order_content = $num OR order_content = $n1) ORDER BY order_content ASC LIMIT 2;";
			$query = str_replace('%lang_id', $language, $query);
			if ($result = db() -> query($query)){
				while ($r = dbfetch($result)) { $id = $r['id'];
					$qm = "UPDATE ".PREFIX.$form['query_table']." SET order_content = ? WHERE id = ?;";
					if ($qx = db() -> prepare($qm)){
						dbbind($qx, array($num, $id), 'ii');
						$rx = dbfetch($qx);
						$num--; unset($qx);
					}
				} unset($result);
			}
		}
		if ($action == 'cmoveup' || $action == 'cmovedown') {
			$table_fields = isset($form['content_fields']) ? $form['content_fields'] : '';
			$id = isset($get[$form['inherited_id']]) ? $get[$form['inherited_id']] : 0;
			$query = "SELECT a.$form[inherited_id], b.id, b.key_security 
				FROM ".PREFIX.$form['query_table']." AS a 
				LEFT JOIN ".PREFIX.$form['inherited']." AS b 
					ON b.id = a.$form[inherited_id]
				WHERE b.id = ".$tid;
			$query = str_replace('%lang_id', $language, $query);
			if ($result = db() -> query($query)){
				$sql = dbfetch($result);
				$tid = $sql['id'];
				$tks = $sql['key_security']; $tks = erukka($tks, '', false);
			} unset($sql);
			$link = pre_seao('admin', $form['inherited'], false, true).pre_seao('action', 'content').pre_seao('tid', $tid);
			$link .= pre_seao('tks', $tks).$ukey.$lang;
			echo '<meta http-equiv="refresh" content="2; url='.$link.'">'; return;
		} echo '<meta http-equiv="refresh" content="1; url='.pre_seao('admin', $gadmin, false, true).pre_seao('action', 'list');
		echo $ukey.$pn.$lang.'">';
	} else
	
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
#						i  P  L  U  G  I  N  S
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************
	if ($action == 'install' || $action == 'dtable' || $action == 'ctable' ) {
		$id = isset($get['tid']) ? $get['tid'] : null;
	 	$key = $action == 'install' ? 'tks': 'dks';
	 	$tks = drukka($get[$key]);
	 	$data = retrieve_mysql('plugins', 'id', $id, 'filename', " AND key_security = '$tks'");
	 	if (!$data) {set_error(); return;}
	 	$fname = 'plugin_'.load_value('getfilename', $data['filename']);
	 	if (function_exists($fname)) {call_user_func_array($fname, array($action, $data['filename']));}
		echo '<meta http-equiv="refresh" content="2; url='.pre_seao('admin', 'iplugins', false, true);
		echo pre_seao('action', 'list').$ukey.'">';
	}
	
# **************************************************************************************************************
# --------------------------------------------------------------------------------------------------------------
#
#							U  N  K  N  O  W  N     C  O  M  M  A  N  D
#
# --------------------------------------------------------------------------------------------------------------
# **************************************************************************************************************
	else {echo '<meta http-equiv="refresh" content="0; url='.pre_seao('error', '404', false, true).'">';}
}
?>