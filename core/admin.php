<?php
/* ------------------------------------------------------------------------------
	
	eBookCMS 1.69
	Stable Release date: September 16, 2015
	Copyright (c) Rui Mendes
	eBookCMS is licensed under a "Creative Commons License"
	
 ------------------------------------------------------------------------------ */

	if (!defined('SECURE_ID') && SECURE_ID == '1234') {die('ACCESS DENIED');}
	require('framework.php');

// LOGIN FIRST TIME
function admin_myfirsttime() {
	$id = drukka($_SESSION[HURI]['user_id']);
	$ks = drukka($_SESSION[HURI][erukka('user_security')]);
	$data = retrieve_mysql('users', 'id', $id, 'first_login', ' AND key_security = "'.$ks.'"');
	if (isset($data) && !$data['first_login']) {unset($data); set_error(); return;}
	if ($id == 1) {$extended = "input_dtext1,username,username,biglength,true,64,64";} else {$extended = "none";}
	$form['action'] = 'myfirsttime';
	$form['myprofile_id'] = $id;
	$form['myprofile_ukey'] = $ks;
	$form['query_table'] = 'users';
	$form['fields'] = array (
		"open_fieldset,personal_data,1,0,0", $extended,
			"input_dtext2,emailreg,emailreg,biglength,true,64,64",
			"input_dtext2,secretQuestion,secretQuestion,biglength,true,64,64",
			"input_dtext2,secretAnswer,secretAnswer,biglength,true,64,64",
			"checkbox,ask_sp,ask_sp,1", "br",
			"input_rukka2,special_num,special_num,smalllength,false,32,32",
			"same_rukka,email,emailreg",
			"same_rukka,avatar,emailreg",
			"savefield,first_login,0,i",
			"save_session,first,0",
		"close_fieldset"
	);
	$form['edit_ks'] = 'key_security';
	$form['cancel'] = false;
	$form['back_to'] = isAllowed('root_access') ? 'root' : 'personal_data';
	create_form($form);
}

// ADMIN MY PROFILE
function admin_myprofile() {
	$form['action'] = 'myprofile';
	$form['myprofile_id'] = drukka($_SESSION[HURI]['user_id']);
	$form['myprofile_ukey'] = drukka($_SESSION[HURI][erukka('user_security')]);
	$form['query_table'] = 'users';
	$form['fields'] = array (
		"open_fieldset,personal_data,1,0,0",
		"input_dtext1,realname,realname,biglength,true,64,64",
		"input_rukka4,avatar,avatar,biglength,false,100,128",
		"input_rukka4,email,email,biglength,true,100,128",
		"input_rukka3,website,website,biglength,false,100,128",
		"checkbox,ask_sp,ask_sp,1", "br",
		"input_rukka2,special_num,special_num,smalllength,false,32,32",
		"sess_update,realname,realname",
		"sess_update,avatar,avatar",
		"sess_update,website,website",
		"close_fieldset"
	);
	$form['edit_ks'] = 'key_security';
	$form['cancel'] = true;
	$form['back_to'] = isAllowed('root_access') && !isset($_POST['pd']) ? 'root' : 'personal_data';
	create_form($form);
}

// ADMIN SETUP
function admin_config() { global $get;
	$form['action'] = 'config';
	$form['title'] = 'config';
	$form['query_table'] = 'config';
	$form['table_header'] = t('id').','.t('title').','.t('action');
	$form['table_cspans'] = '1,1,1';
	$form['table_hsize'] = '15%,55%,15%';
	$form['table_fields'] = 'id,field,%edit';
	$form['query_where'] = isset($get['fg']) ? ' WHERE fgroup = '.$get['fg'] : '';
	$form['fields'] = array (
		"open_fieldset,admin_place,1,0,0",
		"input_rtext,field,field,biglength,false,60,60"
	);
	if (isset($get['tid'])) {$id = $get['tid'];}
	else {$id = isset($_POST['id']) ? drukka(clean($_POST['id'])) : null;}
	switch ($id) {
		case  3 : $form['fields'][] = "combolist,value,value,charset_list,false,1,1"; break;
		case  5 : $form['fields'][] = "input_dtext,value,value,biglength,false,60,100"; break;
		case  7 : 
			$form['fields'][] = "combotable,value,value,languages,false,id@title@prefixo,enabled=1,,1,false,prefixo,s";
		break;
		case 10 : $form['fields'][] = "combolist,value,value,yes_no,false,1,0"; break;
		case 11 : case 12 : case 13 : case 14 : case 15 : case 16 : case 17 : case 18 : case 19 : case 20 :  case 27 : case 28 :
			$form['fields'][] = "multichoice,value,edit_by,gm_list,1,2"; break;
		case 21 : case 22 : case 23 : case 24 : case 25 : case 26 : case 42 : case 43 : 
			$form['fields'][] = "input_int,value,value,smalllength,false,60,100"; break;
		case 29 : $form['fields'][] = "combolist,value,value,uactive_list,false,1"; break;
		case 30 : $form['fields'][] = "combolist,value,value,gm_list,false,1"; break;
		case 31 : case 33 : $form['fields'][] = "combolist,value,value,yes_no,false,1"; break;
		case 32 : $form['fields'][] = "combolist,value,value,asc_desc,false,1,1"; break;
		case 34 : $form['fields'][] = "combolist,value,value,level_comments,false,1"; break;
		case 35 : case 36 : case 37 : case 38 : case 40 : $form['fields'][] = "multichoice,value,edit_by,gm_list,1,2"; break;
		default : $form['fields'][] = "input_text,value,value,biglength,false,60,100"; break;
	}
	$form['fields'][] = "close_fieldset";
	$form['edit_ks'] = 'key_security';
	$form['keys'] = 'key_security';
	$form['edit_id'] = '1';
	$form['cancel'] = true;
	$form['back_bigroot'] = isAllowed('bigroot_access') ? true : false;
	$form['limits'] = 30;
	$form['edit_rights'] = isAllowed('config_access');
	$form['top_filter_by'] = 'config,fgroup,fg,filter_by,fgroup_list,all';
	create_form($form);
}

// CHANGE MY PASSWORD
function admin_mypassword() { global $get;
	$form['myprofile_id'] = drukka($_SESSION[HURI]['user_id']);
	$form['myprofile_ukey'] = drukka($_SESSION[HURI][erukka('user_security')]);
	$form['action'] = 'mypassword';
	$form['query_table'] = 'users';
	$form['fields'] = array (
		"open_fieldset,personal_data,1,0,0",
		"input_password1,password1,password1,biglength,true,32,32",
		"input_password2,password2,password2,biglength,true,32,32",
		"input_password3,password3,password3,biglength,true,32,32",
		"close_fieldset"
	);
	$form['params'] = 'si';
	$form['change_password'] = "password,password1,password2,password3";
	$form['edit_ks'] = 'key_security';
	$form['cancel'] = true;
	$form['back_to'] = isAllowed('root_access') && !isset($_POST['pd']) ? 'root' : 'personal_data';
	create_form($form);
}

// ADMIN LANGUAGES
function admin_langs() { global $get;
	$form['action'] = 'langs';
	$form['query_table'] = 'languages';
	$form['subtitle1'] = t('langs_list');
	$form['table_header'] = t('order').','.t('title').','.t('action');
	$form['table_cspans'] = '1,1,2';
	$form['table_hsize'] = '15%,55%,15%,15%';
	$form['table_fields'] = '%order,title,%edit,%delete';
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	$tabs = $java ? "create_tab,ftab,admin_place@admin_currency" : "none";
	$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_currency,1,0,0';
	$close = $java ? "close_tab" : "close_fieldset";
	if ($get['action'] == 'edit') {
		$tid = $get['tid'];
		$pages_id = "combotable,page_id,page_id,pages,false,id@title,(published = 1 OR published = 2) ";
		$pages_id.= "AND lang_id = $tid,0@none,0,,,i";
	} else {$pages_id = "updatevalue,page_id,i";}
	$form['fields'] = array ( $tabs,
		$tab1,	// TAB1
			"checkbox,enabled,enabled,1", "br",
			"input_text,title,title,biglength,true,60,60",
			"input_text,seftitle,seftitle,biglength,true,60,60",
			"input_text,prefixo,prefixo,biglength,true,2,2",
			$pages_id,
			"checkboxscrip,use_prefix,use_prefix,d_flag,1", "br",
			"open_Div,d_flag,use_prefix,1",
			"open_fieldset,f_flag,1,0,0",
				"comboimage,image_li,image_li,js/langs,.jpg|.gif|.png,16px,11px,sflag",
			"close_fieldset",
			"close_Div",
			"combolist,charset,charset,charset_list,false,0",
		$close,
		$tab2,	// TAB2
			"input_utext,csymbol,csymbol,smalllength,true,10,10,&pound;",
			"input_int,cdecimal,cdecimal,smalllength,false,2,2",
			"input_text,dec_point,dec_point,smalllength,true,2,2,.",
			"input_text,thousands,thousands,smalllength,true,8,8, ",
		$close,
		"genSEF,seftitle,title"
	);
	$form['keys'] = 'key_security,del_key';
	$form['edit_ks'] = 'key_security';
	$form['delkey'] = 'del_key';
	$form['delete_id'] = '1';
	$form['order'] = true;
	$form['table_order'] = 'id';
	$form['cancel'] = true;
	$form['noduplicates'] = 'seftitle,prefixo';
	$form['limits'] = load_cfg('langs_limit');
	$form['back_bigroot'] = true;
	$form['edit_rights'] = isAllowed('lang_access');
	$form['delete_rights'] = isAllowed('lang_access');
	create_form($form);
}

// ADMIN SECTIONS
function admin_sections($action = 'sections', $backbig = true) {
	$form['action'] = $action;
	$form['taction'] = 'sections';
	$form['query_table'] = 'sections';
	$form['subtitle1'] = t('sections_list');
	$form['table_header'] = t('order').','.t('title').','.t('action');
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	$tabs = $java ? "create_tab,ftab,admin_place@admin_options@admin_rights" : "none";
	$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_options,1,0,0';
	$tab3 = $java ? "add_tab,3" : 'open_fieldset,admin_rights,1,0,0';
	$close = $java ? "close_tab" : "close_fieldset";
	$form['table_cspans'] = '1,1,3';
	$form['table_fields'] = '%order,title,%content,%edit,%delete';
	$form['table_hsize'] = '15%,45%,12%,12%,12%';
	$form['fields'] = array ( $tabs,
		$tab1,	// TAB1
		"checkbox,enabled,enabled,1", "br",
		"input_text,title,title,biglength,true,32,32",
		"input_text,seftitle,seftitle,biglength,true,32,32",
		"combolist,group_by,group_by,menu_sections,false,0",
		"comboscript,sec_type,sec_type,type_sections,false,0,",
		"choice_Div,sec_type2,sec_type,2",
			"combotable,plug_id,plug_id,plugins,false,id@title,enabled=1,0@noaction,0,,,i",
		"close_Div",
		"input_text,csstype,csstype,biglength,false,16,16",
		"input_int,lcontent,lcontent,biglength,false,16,16,0",
		"genSEF,seftitle,title",
		$close	// END TAB1
	);
	$gm = load_value('user_gm');
	if (($gm == 1 || isAllowed('section_access')) && isAllowed('enable_poptions')) {
		$form['fields'][] = $tab2;	// TAB2
		$form['fields'][] = "choice_Div,sec_type1,sec_type,1";
			$form['fields'][] = "checkbox,only_author,only_author,1";
			$form['fields'][] = "checkbox,option_txt1,option_txt1,1";
			$form['fields'][] = "checkbox,option_txt2,option_txt2,0";
			$form['fields'][] = "checkbox,show_lang,show_lang,0";
			$form['fields'][] = "checkbox,show_date,show_date,0";
			$form['fields'][] = "br";
			$form['fields'][] = "checkbox,show_plugin,show_plugin,0";
			$form['fields'][] = "checkbox,show_pmsg,show_pmsg,0";
			$form['fields'][] = "checkbox,show_content,show_content,0";
		$form['fields'][] = "close_Div";
		$form['fields'][] = "br";
		#IMAGE FOLDER
		$form['fields'][] = "open_fieldset,admin_fimage,1,1,0";
			$form['fields'][] = "checkboxscrip,option_img,option_img,d_img,1";
			$form['fields'][] = "br";
			$form['fields'][] = "open_Div,d_img,option_img,1";
				$form['fields'][] = "input_text,image_folder,image_folder,biglength,false,32,32";
			$form['fields'][] = "close_Div";
		$form['fields'][] = "close_fieldset";
		$form['fields'][] = $close;	// END TAB2
		# ADMIN RIGHTS
		$form['fields'][] = $tab3;	// TAB3
		$form['fields'][] = "multichoice,admin_by,admin_by,gm_list,1,1";
		$form['fields'][] = "multichoice,edit_by,edit_by,gm_list,1,1";
		$form['fields'][] = "multichoice,delete_by,delete_by,gm_list,1,1";
		$form['fields'][] = "multichoice,add_content,add_content,member_list,1,1";
		$form['fields'][] = "multichoice,options_by,options_by,member_list,1,1";
		$form['fields'][] = "multichoice,member_by,member_by,member_list,0,0";
		$form['fields'][] = $close;	// END TAB3
	}
	$form['content_fields'] = 'id,order_content,title,sec_type,plug_id,admin_by,only_author,add_content,member_by,'.
		'edit_by,delete_by,lcontent';
	$form['control_content'] = 'sec_type';
	$form['control_rights'] = 'admin_by';
	$backlink = $action == 'asections' ? "backlink,false,".$action.",list" : "backlink,false,root";
	# PAGES
	$form['content'][1] = array (
		"new_content,add_page,pages,new,tid,id,,,section_id=%id %lang_id",
		"drawtable,pages,ptype,sections,id@order_content@title@seftitle@author_id@key_security@del_key,section_id=%id ".
		"%lang_id,order@title@seftitle@action,%order@title@seftitle@%edit@%delete,1@1@1@2,10%@20%@40%@15%@15%,".
		load_cfg('pages_limit').",ORDER BY order_content@id ASC,languages,enabled=1",
		"end_table",
		"filter,langs,languages,id@prefixo,enabled = 1,lang,prefixo,pages,b.lang_id=a.id,true", $backlink
	);
	$form['section_id'] = 'sec_type';
	$form['del_herited'][1] = 'pages,ptype = 0 AND section_id=%id %lang_id';
	// EDIT/DELETE content by ID - $form['edit_content'][1] = '1'; $form['del_content'][1] = '1';
	$form['del_section_id'][1] = '1';
	$form['noduplicates'] = 'seftitle';
	$form['limits'] = load_cfg('sections_limit');
	$form['keys'] = 'key_security,del_key';
	$form['edit_ks'] = 'key_security';
	$form['delkey'] = 'del_key';
	$form['delete_id'] = '1';	// LIST TO NOT DELETE
	$form['order'] = true;
	$form['table_order'] = 'order_content';
	$form['cancel'] = true;
	$form['back_to'] = 'asections,list';
	$form['order_lang'] = true;
	if ($backbig !== false) {$form['back_bigroot'] = $backbig;}
	$form['edit_rights'] = isAllowed('section_access');
	$form['delete_rights'] = isAllowed('section_access');
	create_form($form);
}

// GET id FOR SECTION OPTIONS
function get_sectionID ($table) { global $get;
	if ($_POST) {
		$last_action = clean($_POST[erukka('last_action')]);
		$last_action = drukka($last_action);
		$id = $last_action == 'edit' ? drukka(clean($_POST['id'])) : 0;
	} else {$id = 0; $last_action = ''; $sid =0;}
	if (isset($_POST['section_id'])) {$sid = drukka(clean($_POST['section_id']));}
	else if ($get['action']=='new' && isset($get['tid'])){$sid = drukka($get['tid']);}
	else if ($get['action']=='edit' && isset($get['tid'])) {
		$page_data = retrieve_mysql($table, 'id', $get['tid'], 'section_id', '');
		if($page_data && $page_data['section_id']) {$sid = $page_data['section_id'];}
	} else if ($last_action == 'edit' && $id !=0) {
		$page_data = retrieve_mysql($table, 'id', $id, 'section_id', '');
		if ($page_data && $page_data['section_id']) {$sid = $page_data['section_id'];}
	} return $sid;
}

// ADMIN PAGES
function admin_pages($action, $type) { global $get, $t;
	if (isset($get['tid'])) {
		$data = get_permissions($get['tid']);
		$show = isAllowed('enable_poptions') &&	isGroupAllowed($data, 'options_by') ? true : false;
	} else {$show = false;}
	$form['taction'] = 'pages';
	$form['action'] = $action;
	$form['query_table'] = 'pages';
	$form['subtitle1'] = t('add_page');
	$form['table_header'] = t('order').','.t('title').','.t('action');
	$form['table_cspans'] = '1,1,2';
	$form['table_fields'] = '%order,title,%edit,%delete';
	$form['inherited'] = 'sections';
	$form['inherited_id'] = 'section_id';
	$sid = get_sectionID('pages');
	$gm = load_value('user_gm');
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	if ($gm == 1 || $show) {
		$tabs = $java ? "create_tab,ftab,admin_place@admin_options" : "none";
		$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
		$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_options,1,0,0';
	} else {
		$tabs = $java ? "create_tab,ftab,admin_place" : "none";
		$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	} $close = $java ? "close_tab" : "close_fieldset";
	$fields = 'sec_type,csstype,image_folder,option_txt1,option_txt2,option_img,show_lang,show_date,show_pmsg';
	$data = retrieve_mysql('sections', 'id', $sid, $fields, '');
	# TEXT
	if ($data && isset($data['option_txt1']) && $data['option_txt1'] == 0) {$text = "none";}
		else {$text = "text_area,texto1,text";}
	if ($data && isset($data['option_txt2']) && $data['option_txt2'] == 0) {$text2 = "none";}
		else {$text2 = "text_area,texto2,text";}
	# BUTTON TO SHOW IMAGES
	if ($data && isset($data['option_img']) && $data['option_img'] == 0 && !empty($data['image_folder'])) {
		$gallery = "show_gallery,show_images,images,".$data['image_folder'];
	} else {$gallery = "none";}
	# FIELDS
	$form['fields'] = array ( $tabs,
		$tab1,
		"input_text,title,title,biglength,true,100,100",
		"input_text,seftitle,seftitle,biglength,true,100,100",
		$text,
		"checkbox,showtitle,showtitle,1", "br",
		"combolist,published,published,page_list,false,1", "br",
		$gallery, "br"
	);
	# LANG_ID
	$lang_id = load_value('lang'); $now = date('Y-m-d H:i:s');
	if ($data && isset($data['show_lang']) && $data['show_lang'] == 0) {
		$form['fields'][] = "newhidden,lang_id,".$lang_id.',i';}
	else if ($data && isset($data['show_lang']) && $data['show_lang'] == 1) {
		$form['fields'][] = "combotable,lang_id,lang_id,languages,false,id@title,enabled=1,0@none,".$lang_id.",,,i";
	}
	$user_id = load_value('user_id');
	$form['fields'][] = "newhidden,section_id,".$sid.',i';
	$form['fields'][] = "newhidden,author_id,".$user_id.',i';
	$form['fields'][] = "genSEF,seftitle,title";
	# IMAGE
	$dir = $data && isset($data['image_folder']) ? 'images/'.$data['image_folder'] : 'images';
	if (($data && isset($data['option_img']) && $data['option_img'] == 1) || ($get['action'] == 'save')) {
		$form['fields'][] = "open_fieldset,admin_image,1,0,0";
		$form['fields'][] = "comboimage,image,image,".$dir.",.jpg|.gif|.png,128px,128px,select_image";
		$form['fields'][] = "close_fieldset";
	}
	$form['fields'][] = $close;
	# OPTIONS
	$gm = load_value('user_gm');
	if ($gm == 1 || $show) {
		$form['fields'][] = $tab2;
			# DATE
			if ($data && isset($data['show_date']) && $data['show_date'] == 1) {
				$form['fields'][] = "choose_date,date_upd,date_upd,2010,2017,".$now;
				$form['fields'][] = "same_newvalue,date_reg,date_upd,s";
				$form['fields'][] = "br";
				$form['fields'][] = "br";
			} else {
				$form['fields'][] = "newfield_date,date_reg,".$now.',s';
				$form['fields'][] = "newfield_date,date_upd,".$now.',s';
				$form['fields'][] = "updatefield,date_upd,".$now;
			}
			# CSS
			$form['fields'][] = "input_text,pcsstype,csstype,biglength,false,16,16";
			$form['fields'][] = $text2;
			
			# DMETA & KEYWORDS
			$dmeta = str_replace(',','|', load_cfg('description'));
			$keywords = str_replace(',', '|', load_cfg('web_keywords'));
			$form['fields'][] = "input_text,dmeta,dmeta,biglength,false,100,128,".$dmeta;
			$form['fields'][] = "input_tags,keywords,keywords,biglength,false,100,128";
			$form['fields'][] = $data && isset($data['show_pmsg']) && $data['show_pmsg'] == 1 ? "checkbox,show_pmsg,show_pmsg,0" : "none";
			$form['fields'][] = $data && isset($data['show_pmsg']) && $data['show_pmsg'] == 1 ? "br" : "none";
			$form['fields'][] = "combolist,comments,comments,comments_list,false,0";
		$form['fields'][] = $close;
	} else {
		$form['fields'][] = $text2;
		$form['fields'][] = "newfield_date,date_reg,".$now.',s';
		$form['fields'][] = "newfield_date,date_upd,".$now.',s';
		$form['fields'][] = "updatefield,date_upd,".$now;
	}
	$form['noduplicates'] = 'seftitle';
	$form['limits'] = load_cfg('pages_limit');
	$form['keys'] = 'key_security,del_key';
	$form['edit_ks'] = 'key_security';
	$form['order'] = true;
	$form['table_order'] = 'id';
	$form['table_reorder'] = 'lang_id';
	$form['order_where'] = ' WHERE ptype = '.$type.' AND section_id = %section_id %lang_id';
	$form['order_fields'] = 'ptype@%ptype,section_id@%section_id';
	$form['limits'] = load_cfg('pages_limit');
	$form['only_author'] = 'author_id';
	$form['cancel'] = true;
	$form['delete_id'] = '1';
	$form['back_bigroot'] = false;
	$form['back_id'] = 'pages,id';
	$form['back_sid'] = 'sections,content,section_id';
	$form['edit_rights'] = isAllowed('page_edit');
	$form['delete_rights'] = isAllowed('page_delete');
	create_form($form);
}

// ADMIN USERS
function admin_users() { global $get;
	$form['taction'] = 'users';
	$form['action'] = 'users';
	$form['query_table'] = 'users';
	$form['subtitle1'] = t('users_list');
	$form['table_header'] = t('id').','.t('username').','.t('realname').','.t('action');
	$form['table_cspans'] = '1,1,1,2';
	$form['table_fields'] = 'id,@username,@realname,%edit,%delete';
	$form['table_hsize'] = '10%,25%,45%,10%,10%';
	$form['query_where'] = isset($get['gm']) ? ' WHERE gmember = '.$get['gm'] : '';
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	$tabs = $java ? "create_tab,ftab,admin_place@admin_options" : "none";
	$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_options,1,0,0';
	$close = $java ? "close_tab" : "close_fieldset";
	$gm = load_value('user_gm');
	$user_id = load_value('user_id');
	$ip_address = getInfo('YOUR_IP');
	if ($ip_address == '::1') {$ip_address = '127.0.0.1';}
	$ip_address = erukka($ip_address, '', false);
	$username = "none"; $pass = "none";
	if ($get['action'] == 'new') {$add_default = $get['action'] == 'new' ? ',test' : '';}
	else {$add_default = '';}
	if (isset($get['tid']) && $get['tid']) {
		$data = retrieve_mysql('users', 'id', $get['tid'], 'akey');
		$akey = "hiddenfield,akey,".$data['akey'];
		$form['akey'] = erukka($data['akey']);
	} else {$akey = "";}
	$active = load_cfg('user_active');
	$usergm = load_cfg('user_member');
	$form_gm = $usergm == 1 || isAllowed('users_edit') ? "combolist,gmember,gmember,gm_list,false,".$usergm : "none";
	$form['fields'] = array ( $tabs,
		$tab1,
			"checkbox,first_login,first_login,1", "br",
			"input_dtext1,username,username,biglength,true,32,32",
			"create_password,password,cpassword,biglength,true,32,32".$add_default,
			"reset_password,password,rpassword,biglength,true,32,32",
			"input_dtext1,realname,realname,biglength,true,100,128",
			"input_dtext2,emailreg,email,biglength,true,100,255",
			"input_rukka3,website,website,biglength,false,100,255",
			"newhidden,user_ip,".$ip_address.',s',
			"newhidden,vcode,".genKey('validation_code').',s',
			"newhidden,total_timedate,0,i",
			"newhidden,regdate,".date('Y-m-d').',s',
			"newhidden,akey,".gen_PKey().',s',
			"newhidden,browser_id,".get_hsa().',s',
			$akey,
		$close,
		$tab2,
			"combolist,user_active,user_active,uactive_list,false,".$active,
			$form_gm,
			"same_rukka,email,emailreg",
			"same_rukka,avatar,emailreg",
		$close
	);
	$form['content_fields'] = 'id,order';
	$form['noduplicates'] = 'username';
	$form['limits'] = load_cfg('users_limit');
	$form['keys'] = 'key_security,del_key';
	$form['edit_ks'] = 'key_security';
	$form['delkey'] = 'del_key';
	$form['table_order'] = 'id';
	$form['cancel'] = true;
	# EDIT USER_ID
	$form['edit_id'] = $user_id == '1' ? '1' : ''.$user_id;
	# DELETE USER_ID
	$form['delete_id'] = $user_id == 1 ? '1' : '1,'.$user_id;
	$form['edit_group'] = $user_id == '1' ? '' : ($gm != '1' ? '1' : '1,'.$gm);
	$form['delete_group'] = $user_id == '1' ? '' : isAllowed('users_delete');
	$form['key_group'] = 'gmember';
	$form['back_bigroot'] = true;
	$form['back_to'] = 'users,list';
	$form['filter_by'] = 'users,gmember,gm,filter_by,gm_list,all_members';
	$form['edit_rights'] = isAllowed('users_edit');
	$form['delete_rights'] = isAllowed('users_delete');
	create_form($form);
}

// WEB TEXT
function cpanel_comments() {
	echo '<h1>'.t('comments').'</h1>';
	$root = defined('HTACCESS') && HTACCESS == true ? webroot().'admin/' : webroot().'?admin=';
	$char = defined('HTACCESS') && HTACCESS == true ? '/' : '&amp;';
	$uri = '<li class="$class"><a href="'.$root;
	if (isAllowed('comments_access') != true) {echo '<p>'.t('no_access').'</p>'; return;}
	$action = $char.pre_seao('action', 'list', true); $ukey = pre_seao('ukey', load_value('user_ks'), true);
	$admin_msg = isAllowed('comments_access') && isAllowed('admin_messages') ? true : false;
	$block_msg = isAllowed('comments_access') && isAllowed('admin_messages') && isAllowed('block_messages') ? 
		true : false;
	$spam_msg = isAllowed('comments_access') && isAllowed('admin_messages') && isAllowed('spam_messages') ? 
		true : false;
	$archive_msg = isAllowed('comments_access') && isAllowed('admin_messages') && isAllowed('archive_messages') ? 
		true : false;
	echo '<fieldset><legend class="eb_legend">'.t('admin').'</legend>';
		echo '<div class="admin_core"><ul>';
		$link = str_replace('$class', 's_approved', $uri);
		if ($admin_msg)  {echo $link.'comok'.$action.$ukey.'">'.t('approved').'</a></li>';}
		$link = str_replace('$class', 's_comwap', $uri);
		if ($admin_msg) {echo $link.'comwap'.$action.$ukey.'">'.t('comwap').'</a></li>';}
		$link = str_replace('$class', 's_spam', $uri);
		if ($spam_msg) {echo $link.'comspam'.$action.$ukey.'">'.t('spam').'</a></li>';}
		$link = str_replace('$class', 's_ublocked', $uri);
		if ($block_msg) {echo $link.'combku'.$action.$ukey.'">'.t('combku').'</a></li>';}
		$link = str_replace('$class', 's_combke', $uri);
		if ($block_msg) {echo $link.'combke'.$action.$ukey.'">'.t('combke').'</a></li>';}
		$link = str_replace('$class', 's_comarch', $uri);
		if ($archive_msg) {echo $link.'comarch'.$action.$ukey.'">'.t('comarch').'</a></li>';}
		$link = str_replace('$class', 's_comrep', $uri);
		if ($spam_msg) {echo $link.'comrep'.$action.$ukey.'">'.t('comrep').'</a></li>';}
		if (isAllowed('root_access') && isAllowed('bigroot_access')) {
			$link = str_replace('$class', 's_back', $uri);
			echo $link.'bigroot'.$action.$ukey.'">'.t('back').'</a></li>';
		} echo '</ul></div>';
	echo '</fieldset>';
}

// ADMIN COMMENTS
function admin_comments($action, $option, $type, $admin = 'admin_messages', $plugin = 0) { global $get;
	$form['action'] = $action;
	$form['title'] = $type != 0 ? 'comments' : 'comment';
	$now = date('Y-m-d H:i:s');
	$form['taction'] = 'comments';
	$form['query_table'] = 'comments';
	switch ($type) {
		case 1 : $form['query_where'] = ' WHERE approved = 1 AND archive_comment = 0 AND spam = 0 AND block_email = 0'; break;
		case 2 : $form['query_where'] = ' WHERE approved = 0'; break;
		case 3 : $form['query_where'] = ' WHERE spam = 1'; break;
		case 4 : $form['query_where'] = ' WHERE block_user = 1'; break;
		case 5 : $form['query_where'] = ' WHERE block_email = 1'; break;
		case 6 : $form['query_where'] = ' WHERE archive_comment = 1'; break;
		case 7 : $form['query_where'] = ' WHERE report_abuse = 1 AND spam = 0'; break;
		default: $form['query_where'] = '';
	}
	$form['query_where'] .= isset($get['fg']) ? ' AND ptype = '.$get['fg'] : '';
	$form['subtitle1'] = '<h3>'.t($option).'</h3>';
	$form['table_header'] = t('name').','.t('avatar_email').','.t('ip').','.t('action');
	$form['table_cspans'] = '1,1,1,2';
	$form['table_hsize'] = '25%,30%,13%,10%,15%';
	$form['table_fields'] = '$name,$avatar_email,$user_ip,%edit,%delete';
	$form['fields'] = array (
		"open_fieldset,admin_place,1,0,0",
			"input_rukka,name,name,biglength,true,100,255",
			"input_rukka3,avatar_email,avatar_email,biglength,true,100,255",
			"input_rukka3,url,url,biglength,false,100,255",
			"text_code,comment,comment",
			"checkbox,approved,approved,1",
			"checkbox,spam,spam,0",
			"checkbox,block_user,block_user,0",
			"checkbox,block_email,block_email,0",
			"checkbox,archive_comment,archive_comment,0", "br",
			"combolist,report_abuse,report_abuse,abuse_list,false,1",
			"combotable,user_id,user,users,false,id@username,id=[user_id],0@anonymous,1,1,,i",
			"newfield_date,date_reg,".$now.',s',
		"close_fieldset"
	);
	$form['keys'] = 'key_security,delkey';
	$form['edit_ks'] = 'key_security';
	$form['delkey'] = 'delkey';
	$form['order_by'] = ' ORDER BY id DESC';
	$form['cancel'] = true;
	$form['limits'] = load_cfg('comments_limit');
	$form['back_to'] = $action.',list';
	if ($plugin == 0) {$form['back_list'] = isset($get['fp']) && $get['fp'] == 3 ? 'root' : 'comments,list';}
	else {}
	$form['edit_rights'] = isAllowed($admin);
	$form['delete_rights'] = isAllowed($admin);
	$form['filter_by'] = 'comments,ptype,fg,filter_by,ptype_sec,all';
	create_form($form);
}

// ADMIN PERSONAL MESSAGES
function admin_pmsg($action, $option, $type) { global $get;
	$form['action'] = $action;
	$form['title'] = $action;
	$form['taction'] = 'sections';
	$form['query_table'] = 'pmessages';
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	$tabs = $java ? "create_tab,ftab,admin_place@admin_options" : "none";
	$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_options,1,0,0';
	$close = $java ? "close_tab" : "close_fieldset";
	switch ($type) {
		case 0 : $form['query_where'] = ' WHERE unread = 1'; break;
		case 1 : $form['query_where'] = ' WHERE unread = 0'; break;
		default: $form['query_where'] = '';
	}
	$form['query_where'] .= isset($get['fg']) && $get['fg'] != -1 ? 
		' AND suser_id = '.load_value('user_id') : ' WHERE suser_id = '.load_value('user_id');
	$form['subtitle1'] = '<h3>'.t($option).'</h3>';
	$form['table_header'] = t('name').','.t('subject').','.t('date_upd').','.t('action');
	$form['table_cspans'] = '1,1,1,2';
	$form['table_hsize'] = '20%,30%,13%,10%,15%';
	$form['table_fields'] = '@name,subject,date_sent,%edit,%delete';
	$form['fields'] = array ( $tabs,
		$tab1,
			"input_dtext1,name,name,biglength,true,100,255,,,true",
			"input_rtext,subject,subject,biglength,false,100,255",
			"input_rukka,email,email,biglength,true,100,255,,,true",
			"text_code,message,message",
			"savefield,unread,0,i",
			"savenew,unread,0,i",
		$close,
		$tab2,
			"input_rtext,date_sent,date_upd,tinylength,false,20,20",
			"showpage,page_id,page_ref,texto1,false",
		$close
	);
	$form['keys'] = 'key_security,delkey';
	$form['edit_ks'] = 'key_security';
	$form['delkey'] = 'delkey';
	$form['order_by'] = ' ORDER BY id DESC';
	$form['cancel'] = true;
	$form['limits'] = load_cfg('pmsg_limit');
	if ($_POST) {
		$luri = explode(',', clean($_POST[erukka('get')]));
		$mode = isset($luri[3]) ? $luri[3]: 0;
	} else {$mode = 0;}
	$form['back_to'] = $action.',list,fg@'.$mode;
	$form['back_root'] = true;
	$form['edit_rights'] = true;
	$form['delete_rights'] = true;
	$form['filter_by'] = 'pmessages,unread,fg,filter_by,ptype_msg,all,,-1';
	create_form($form);
}

// ADMINISTRATION PLUGIN
function admin_iplugins($action) { global $pn, $get;
	$plugdir = 'plugins/';
	# REFRESH DIRECTORY AND PRE-INSTALL PLUGINS
	if (is_dir($plugdir)) {
		$fd = opendir($plugdir); $plug_array = array();
		$plugin_data = retrieve_mysql('plugins', 'preinstalled', 1, 'filename', '', '');
		for ($i=0; $i<count($plugin_data); $i++) {$plug_array[] = $plugin_data[$i]['filename'];}
		while (($file = @readdir($fd)) == true) {
			clearstatcache();
			$ext = substr($file, strrpos($file, '.') + 1);
			if (($ext == 'php' || $ext == 'txt') && strpos($file, '_install') == 0) {
				include_once($plugdir.$file);
				if (!in_array($file, $plug_array)) {
					$fname = 'plugin_'.load_value('getfilename', $file);
					call_user_func_array($fname, array('pre-install',$file));
				}
			}
		}
	}
	$form['action'] = 'iplugins';
	$form['query_table'] = 'plugins';
	$form['table_header'] = t('title').','. t('author').','.t('action');
	$form['table_cspans'] = '1,1,4';
	$form['table_fields'] = 'title,author,%install,%edit,%ctable,%dtable';
	$form['table_hsize'] = '30%,22%,15%,15%,15%';
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	$tabs = $java ? "create_tab,ftab,admin_place@admin_options@admin_rights" : "none";
	$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_options,1,0,0';
	$tab3 = $java ? "add_tab,3" : 'open_fieldset,admin_rights,1,0,0';
	$close = $java ? "close_tab" : "close_fieldset";
	$form['fields'] = array ($tabs,
		$tab1,
			"checkbox,auto_load,auto_load,1", "br",
			"checkbox,enabled,enabled,1", "br",
			"input_rtext,title,title,biglength,false,80,80",
			"input_rtext,author,author,biglength,false,80,80",
			"input_rtext,version,version,biglength,false,8,8",
			"input_rtext,filename,filename,biglength,false,64,64",
			"input_rtext,install_table,install_table,biglength,false,80,80",
			"input_rtext,sef_file,sef_file,biglength,false,80,80",
			"input_rtext,admin_func,admin_func,biglength,false,64,64",
			"input_rtext,public_func,public_func,biglength,false,64,64",
			"text_rcode,description,description,255",
		$close,
		$tab2,
			"input_xtext,xstr1,xtxt1,biglength,false,64,64",
			"input_xtext,xint1,xtxt2,biglength,false,64,64",
			"input_xtext,xdate1,xtxt3,biglength,false,64,64",
		$close,
		$tab3,
			"multichoice,admin_by,admin_by,gm_list,1,1",
			"multichoice,edit_by,edit_by,gm_list,1,1",
			"multichoice,delete_by,delete_by,gm_list,1,1",
			"multichoice,add_content,add_content,member_list,1,1",
			"multichoice,public_by,public_by,member_list,0,0",
		$close
	);
	$form['keys'] = 'key_security,delkey';
	$form['edit_ks'] = 'key_security';
	$form['delkey'] = 'delkey';
	$form['limits'] = load_cfg('plugins_limit');
	$form['edit_enabled'] = 'installed';
	$form['back_to'] = 'iplugins,list';
	$form['cancel'] = true;
	$form['back_bigroot'] = true;
	$form['edit_rights'] = isAllowed('plugins_install');
	create_form($form);
}

// ADMINISTRATION DATA FROM PLUGIN
function admin_plugins($action) {
	$form['action'] = 'plugins';
	$form['padmin'] = 'sef_file';
	$form['query_table'] = 'plugins';
	$form['table_header'] = t('title').','. t('author').','.t('action');
	$form['table_cspans'] = '1,1,1';
	$form['table_fields'] = 'title,author,%padmin';
	$form['query_where'] = ' WHERE installed = 1';
	$form['table_hsize'] = '55%,30%,12%';
	$form['back_bigroot'] = true;
	$form['edit_rights'] = isAllowed('plugins_access');
	create_form($form);
}

// BIG ROOT FOR SUPER-ADMIN
function admin_bigroot() { global $get;
	if (!LOGGED) {terminateSession();}
	if (!isAllowed('bigroot_access')) {return;}
	echo '<h1>'.t('panel_control').'</h1>';
	echo '<h2>'.t('admin_place').'</h2>';
	$root = defined('HTACCESS') && HTACCESS == true ? webroot().'admin/' : webroot().'?admin=';
	$char = defined('HTACCESS') && HTACCESS == true ? '/' : '&amp;';
	$uri = '<li class="$class"><a href="'.$root;
	$ks = load_value('user_ks'); $ukey = isset($ks) ? pre_seao('ukey', $ks, true) : '';
	$list = $char.pre_seao('action', 'list', true); $edit = pre_seao('action', 'edit', true);
	echo '<fieldset><legend class="eb_legend">'.t('advanced_menu').'</legend>';
		echo '<div class="admin_core"><ul>';
		$link = str_replace('$class', 's_setup', $uri);
		if (isAllowed('config_access')) {echo $link.'config'.$list.$ukey.'">'.t('config').'</a></li>';}
		$link = str_replace('$class', 's_langs', $uri);
		if (isAllowed('lang_access')) {echo $link.'langs'.$list.$ukey.'">'.t('langs').'</a></li>';}
		$link = str_replace('$class', 's_sections', $uri);
		if (isAllowed('section_access')) {echo $link.'asections'.$list.$ukey.'">'.t('sections').'</a></li>';}
		$link = str_replace('$class', 's_users', $uri);
		if (isAllowed('users_access')) {echo $link.'users'.$list.$ukey.'">'.t('users').'</a></li>';}
		$link = str_replace('$class', 's_iplugins', $uri);
		if (isAllowed('plugins_install')) {echo $link.'iplugins'.$list.$ukey.'">'.t('plugins_intall').'</a></li>';}
		$link = str_replace('$class', 's_plugins', $uri);
		if (isAllowed('plugins_access')) {echo $link.'plugins'.$list.$ukey.'">'.t('plugins').'</a></li>';}
		$link = str_replace('$class', 's_comments', $uri);
		if (isAllowed('comments_access')) {echo $link.'comments'.$char.$ukey.'">'.t('comments').'</a></li>';}
		$link = str_replace('$class', 's_upimgs', $uri);
		$option = pre_seao('option', 'images');
		if (isAllowed('uplimg_access')) {echo $link.'uimages'.$char.$edit.$option.$ukey.'">'.t('upload_place').'</a></li>';}
		echo '</ul></div>';
	echo '</fieldset>';
	$link = ' <a href="'.pre_seao('admin', 'root', false, true, true).$ukey.'">'.t('here').'</a>';
	echo '<p>'.t('back_root').' '.t('click').$link.'</p><br />';
}

// USER ADMINISTRATION
function user_root() {
	if (!LOGGED) {terminateSession();}
	$gm = load_value('user_gm');
	if (strlen($gm)==1) {$gm = '0'.$gm;}
	$char = defined('HTACCESS') && HTACCESS == true ? '/' : '&amp;';
	$root = defined('HTACCESS') && HTACCESS == true ? webroot().'admin/' : webroot().'?admin=';
	$uri = '<li class="$class"><a href="'.$root;
	$ukey = pre_seao('ukey', load_value('user_ks'), true);
	$lang_id = load_value('lang');
	if ($lang_id != 1) {$lang = pre_seao('lang', $lang_id, true);} else {$lang = '';}
	echo '<h1>'.t('panel_control').'</h1>';
	if (isAllowed('root_access')) {
		echo '<h2>'.t('useradmin_place').'</h2>';
		$uri1 = '<li class="$class"><a href="'.$root.'sections'.$char.pre_seao('action', 'content', true);
		$list = pre_seao('action', 'list', true); $edit = pre_seao('action', 'edit', true);
		$nocontent = true;
		$menus = explode(',', t('menu_sections'));
		for ($i=1; $i<count($menus); $i++) {
			$qtotal = "SELECT count(id) AS total 
				FROM ".PREFIX."sections WHERE enabled = 1 AND admin_by LIKE  '%".$gm."%' AND group_by = ?";
			if ($rres = db() -> prepare($qtotal)) {
				$rt = dbbind($rres, array($i), 'i');
				$vvalues = dbfetch($rt, true);
				$total = $vvalues['total']; unset($vvalues, $rres);
			}
			$query = "SELECT * FROM ".PREFIX."sections 
				WHERE enabled = 1 AND admin_by LIKE  '%".$gm."%' AND group_by = ?  AND sec_type<>0";
			if ($result = db() -> prepare($query)) {
				$result = dbbind($result, array($i), 'i');
				if ($total > 0) {
					$nocontent = false;
					echo '<fieldset><legend class="eb_legend">'.$menus[$i].'</legend>';
					echo '<div class="admin_core"><ul>';
					while ($r = dbfetch($result)) {
						$class = str_replace('-','_', $r['seftitle']);
						$link = str_replace('$class', 's_'.$class, $uri);
						$link1 = str_replace('$class', 's_'.$class, $uri1);
						if ($r['sec_type'] != 2) {
						 	$plug_key = erukka($r['key_security']);
							echo $link1.pre_seao('tid', $r['id']).pre_seao('tks', $plug_key).$ukey.$lang.'">';
							echo $r['title'].'</a></li>';
						} else {
							$pid = $r['plug_id'];
							$plugin = retrieve_mysql('plugins', 'id', $pid, 'id,sef_file', " AND installed = 1");
							echo $link.$plugin['sef_file'].$char.pre_seao('action', 'list', true).$ukey.$lang.'">'.$r['title'].'</a></li>';
						}
					} echo '</ul></div></fieldset>';
				}
			} unset($result);
		}
		# MESSAGES - WAITING APPROVAL
		$waiting = retrieve_mysql('comments', 'approved', 0, 'count(DISTINCT id) AS total', '');
		if (isAllowed('comments_access') && isAllowed('admin_messages') && $waiting && $waiting['total'] != 0) {
			echo '<fieldset><legend class="eb_legend">'.t('comwap').'</legend>';
				echo '<div class="admin_core"><ul>';
					$link = str_replace('$class', 's_comwap', '<li class="$class"><a href="');
					echo $link.pre_seao('admin', 'comwap', false, true);
					echo pre_seao('action', 'list', true).pre_seao('fp', '3', true).$ukey.$lang.'">';
					echo $waiting['total'].' '.t('comments').'</a></li>';
			echo '</ul></div></fieldset>'; unset($waiting);
		}
		# MESSAGES - REPPORT ERRORS OR SPAM
		$errors = retrieve_mysql('comments', 'report_abuse', 1, 'count(DISTINCT id) AS total', ' AND spam = 0');
		if (isAllowed('comments_access') && isAllowed('spam_messages') && $errors && $errors['total'] != 0) {
			echo '<fieldset><legend class="eb_legend">'.t('comrep').'</legend>';
				echo '<div class="admin_core"><ul>';
					$link = str_replace('$class', 's_comrep', '<li class="$class"><a href="');
					echo $link.pre_seao('admin', 'comrep', false, true);
					echo pre_seao('action', 'list', true).pre_seao('fp', '3', true).$ukey.$lang.'">';
					echo $errors['total'].' '.t('comments').'</a></li>';
			echo '</ul></div></fieldset>'; unset($errors);
		}
	} else {echo '<div class="ebook_admin"><div class="msgwarning"><p>'.t('no_access').'</p></div></div>'; return;}
	# PROFILE
	$uri = '<li$class><a href="';
	$action = pre_seao('action', 'edit');
	echo '<fieldset><legend class="eb_legend">'.t('f_myprofile').'</legend>';
		echo '<div class="admin_core"><ul>';
			echo '<li class="change_pmsg"><a href="';
			echo pre_seao('admin', 'pminbox', false, true);
			echo pre_seao('action', 'list').pre_seao('fg', '0').$ukey.'">'.t('pmessages').'</a></li>';
			$link = str_replace('$class', ' class="change_profile"', $uri);
			echo $link.pre_seao('admin', 'myprofile', false, true).$action.$ukey.$lang.'">'.t('change_profile').'</a></li>';
			$link = str_replace('$class', ' class="mypassword"', $uri);
			echo $link.pre_seao('admin', 'mypassword', false, true).$action.$ukey.$lang.'">'.t('mypassword_title').'</a></li>';
			echo '<li class="logout"><a href="'.pre_seao('logout', 'now', false, true).'">'.t('logout').'</a></li>';
		echo '</ul></div>';
	echo '</fieldset>';
 	if (isAllowed('root_access') && isAllowed('bigroot_access')) {
		echo '<p>'.t('advanced_func').' '.t('click').' <a href="'.pre_seao('admin', 'bigroot', false, true, true).$ukey.$lang.'">';
		echo t('here').'</a></p><br />';
	}
}

// PERSONAL DATA
function personal_data($title = true) {
	echo '<div class="ebook_admin">';
	if ($title) {echo '<h1>'.t('panel_control').'</h1>';}
	$ukey = $_SESSION && isset($_SESSION[HURI][erukka('user_security')]) ? 
		pre_seao('ukey', $_SESSION[HURI][erukka('user_security')], true) : '';
	echo '<fieldset><legend class="eb_legend">'.t('f_myprofile').'</legend>';
		echo '<div class="admin_core">';
		echo '<ul>';
			echo '<li class="change_pmsg"><a href="';
			echo pre_seao('admin', 'pminbox', false, true);
			echo pre_seao('action', 'list').pre_seao('fg', '0').$ukey.'">'.t('pmessages').'</a></li>';
			echo '<li class="change_profile"><a href="';
			echo pre_seao('admin', 'myprofile', false, true);
			echo pre_seao('action', 'edit', true).pre_seao('pd', '1', true).$ukey.'">'.t('change_profile').'</a></li>';
			echo '<li class="mypassword"><a href="';
			echo pre_seao('admin', 'mypassword', false, true);
			echo pre_seao('action', 'edit', true).pre_seao('pd', '1', true).$ukey.'">';
			echo t('mypassword_title').'</a></li>';
			echo '<li class="logout"><a href="'.pre_seao('logout', 'now', false, true).'">'.t('logout').'</a></li>';
		echo '</ul></div>';
	echo '</fieldset>';
	if (isAllowed('root_access')) {
		echo '<p>'.t('back_root').' '.t('click').' <a href="'.pre_seao('admin', 'root', false, true).$ukey.'">';
		echo t('here').'</a></p><br />';
	} echo '</div>';
}

// FORM TO ASK YOUR SECRET NUMBER
function ask_myPin($msg) {
	echo '<div class="form_logpin">';
		echo '<div class="logpin_title"><h1>'.t('login_pin').'</h1></div>';
		$url = pre_seao('admin', 'root', false, true).load_value('durl');
		echo '<form method="post" action="'.$url;
			echo '" id="login" accept-charset="'.load_cfg('charset').'" name="login" autocomplete="off" >';
				echo '<textarea class="private" name="private" cols="1" rows="1"></textarea>';
				echo '<div class="logpin_info">';
					echo '<div class="info_user">';
						echo '<label for="userpin">'.t('pin').'</label>';
						echo '<input type="password" name="'.erukka('userpin').'" ';
						echo 'id="userpin" value="" maxlength = "20" autocomplete="off">';
					echo '</div><span class="red">'.$msg.'</span>';
				echo '</div>';
				echo '<div class="login_foot">';
					echo '<input type="submit" value="'.t('submit').'" class="btLogin" ';
					echo 'name="'.erukka('submit').'" >';
				echo '</div>';
				fightSpamRSA();
			echo '</form>';
		echo '</div>';
}

// UPLOAD IMAGES
function upload_files($mode = 'images') {
	$form['action'] = 'uimages';
	$form['title'] = 'upload';
	$java = isset($_COOKIE['ejava']) && $_COOKIE['ejava'] != 'disabled' ? true : false;
	$tabs = $java ? "create_tab,ftab,upload_place@manage_files" : "none";
	$tab1 = $java ? "add_tab,1" : 'open_fieldset,admin_place,1,0,0';
	$tab2 = $java ? "add_tab,2" : 'open_fieldset,admin_options,1,0,0';
	$close = $java ? "close_tab" : "close_fieldset";
	$form['fields'] = array (
		$tabs, 
		$tab1,
			"show_html,<h2>".t('select_images')."</h2>",
			"upload_files,images,5,true,gfolder,gif|jpg|png", "br",
			"show_folders,folder,target_folder,images",
			
		$close,
		$tab2, 
			"create_folder,cfolder,create_folder,biglength,images", "br",
			"open_fieldset,images,1,0,0",
				"show_images,cssgal,select_folder,images",
			"close_fieldset",
		$close
	);
	$form['enctype'] = 'multipart/form-data';
	$form['no_save'] = true;
	$form['cancel'] = true;
	if ($_POST && isset($_POST[erukka('cancel')])) {$form['back_to'] = isAllowed('uplimg_access') ? 'bigroot' : 'root';}
	$form['back_list'] = 'uimages,edit,option=images';
	create_form($form);
}

// ADMIN CENTER
function admin_center() { global $get;
	if (!LOGGED) {terminateSession();} else
	if (LOGGED) {
		# FIRST TIME
		if (isset($_SESSION[HURI]['first']) && $_SESSION[HURI]['first'] == 1 && $get['admin'] != 'myfirsttime') {
			$ukey = pre_seao('ukey', $_SESSION[HURI][erukka('user_security')], true);
			echo '<meta http-equiv="refresh" content="0; url=';
			echo pre_seao('admin', 'myfirsttime', false, true, true).pre_seao('action', 'edit', true).$ukey.'">';
			return;
		}
		# ASK ME MY SECRET NUMBER
		if ($_SESSION[HURI]['ask'] == 1 && $_SESSION[HURI]['pin'] == 0 && !isset($_SESSION[HURI]['first'])) {
			if ($_POST && $_POST[erukka('submit')] == t('submit') && checkSpamRSA()) {
				$id = drukka($_SESSION[HURI]['user_id']);
				$akey = drukka1($_SESSION[HURI]['akey']);
				$ks = drukka($_SESSION[HURI][erukka('user_security')]);
				$pin_number = clean($_POST[erukka('userpin')]);
				$user = retrieve_mysql('users', 'id', $id, 'special_num', ' AND key_security = "'.$ks.'"');
				if ($pin_number == drukka($user['special_num'], $akey)) {$_SESSION[HURI]['pin'] = 1;}
				else {$msg = t('wrong_num');}
			} else {$msg = '';}
			if ($_SESSION[HURI]['pin'] == 0) {ask_myPin($msg); return;}
		}
		$admin = isset($get['admin']) ? $get['admin'] : '';
		$action = isset($get['action']) ? $get['action'] : '';
		$tid = isset($get['tid']) ? $get['tid'] : null;
		$tks = isset($get['tks']) ? $get['tks'] : '';
		$admin_msg = isAllowed('comments_access') && isAllowed('admin_messages') ? true : false;
		$block_msg = isAllowed('comments_access') && isAllowed('admin_messages') && isAllowed('block_messages') ? 
			true : false;
		$spam_msg = isAllowed('comments_access') && isAllowed('admin_messages') && isAllowed('spam_messages') ? 
			true : false;
		$archive_msg = isAllowed('comments_access') && isAllowed('admin_messages') && isAllowed('archive_messages') ? 
			true : false;
		if (empty($admin) || (isset($get['ukey']) && $get['ukey'] != load_value('user_ks'))) {set_error(); return;}
		echo '<div class="ebook_admin">';
		switch ($admin) {
			case 'personal_data': personal_data(); break;
			case 'myfirsttime': admin_myfirsttime(); break;
			case 'myprofile': admin_myprofile(); break;
			case 'mypassword': admin_mypassword(); break;
			case 'root': user_root(); break;
			case 'bigroot': if (isAllowed('bigroot_access')) {admin_bigroot();} else {set_error('401');} break;
			case 'config': if (isAllowed('config_access')) {admin_config();} else {set_error('401');} break;
			case 'langs': if (isAllowed('lang_access')) {admin_langs();} else {set_error('401');} break;
			case 'asections': 
				if (isAllowed('section_access') || check_rights('sections', $tid, $tks, 'admin_by', 1)) {
					admin_sections('asections', $action, false);}
				else {set_error('401');} break;
			case 'sections': 
				if (isAllowed('section_access') || check_rights('sections', $tid, $tks, 'admin_by', 1)) {
					admin_sections('sections', $action);}
				else {set_error('401');} break;
			case 'users': if (isAllowed('users_access')) {admin_users();} else {set_error('401');} break;
			case 'pminbox': $fg = isset($get['fg']) ? $get['fg'] : 0; admin_pmsg('pminbox', 'pmessages', $fg); break;
			case 'iplugins': 
				if (isAllowed('plugins_install')) {admin_iplugins($action);} else {set_error('401');} break;
			case 'plugins': if (isAllowed('plugins_access')) {admin_plugins($action);} else {set_error('401');} break;
			case 'pages': 
				if (isAllowed('page_list') || check_rights('pages', $tid, $tks, 'admin_by', $action) || 
					verify_admin('pages', $tid)) {admin_pages('pages', 0);} else {set_error('401');} break;
			case 'cpages': 
				if (isAllowed('page_list') || verify_admin('cpages', $tid)){admin_pages('cpages', 0);}
				else {set_error('401');} break;
			case 'comments': if (isAllowed('comments_access')) {cpanel_comments();} else {set_error('401');} break;
			case 'pcomment': if ($admin_msg) {admin_comments('pcomment', 'comment', 0, 'admin_messages', 1);}
				else {set_error('401');} break;
			case 'comment': if ($admin_msg) {admin_comments('comment', 'comment', 0);} else {set_error('401');} break;
			case 'comok': if ($admin_msg) {admin_comments('comok', 'comok', 1);} else {set_error('401');} break;
			case 'comwap': if ($admin_msg) {admin_comments('comwap', 'comwap', 2);} else {set_error('401');} break;
			case 'comspam': 
				if ($spam_msg) {admin_comments('comspam', 'comspam', 3, 'spam_messages');}
				else {set_error('401');} break;
			case 'combku': 
				if ($block_msg) {admin_comments('combku', 'combku', 4, 'block_messages');}
				else {set_error('401');} break;
			case 'combke': 
				if ($block_msg) {admin_comments('combke', 'combke', 5, 'block_messages');}
				else {set_error('401');} break;
			case 'comarch': 
				if ($archive_msg) {admin_comments('comarch', 'comarch', 6, 'archive_messages');}
				else {set_error('401');} break;
			case 'comrep': 
				if ($spam_msg) {admin_comments('comrep', 'comrep', 7, 'spam_messages');}
					else {set_error('401');} break;
			case 'uimages': upload_files('images'); break;
			case 'showimages': showimages($get['d']);break;
			default :
				$plugin = retrieve_mysql('plugins', 'sef_file', '"'.$admin.'"', 'id,admin_by', " AND installed = 1");
				if ($plugin && isGroupAllowed($plugin)) {call_user_func_array('admin_'.$admin, array($action));}
				else {set_error();}
		} echo '</div>';
	} else {set_error();}
}

?>