<?php

$ttotal = 5;
if (defined('YOUR_KEY') && YOUR_KEY == "aPO7IU8Yz6xTREWQc9ASDvFGHJb4KLnZXmCVB3NM5lkjhgfdsqwerty01u2iop-_") {return;}

// EXECUTE SQL COMMAND
function executeSQL ($cmd) {
	if (empty($cmd)) {return;}
	if ($result = db() -> query($cmd)) {
		$r = dbfetch($result); unset($result);
	} else {echo 'Error in SQL: '.$cmd.'<br />';}
}

if (load_cfg('core_version')=='ebookcms_1.64') {
	// RUN SQLITE COMMANDS TO UPDATE DATABASE
	if (defined('DB_TYPE') && DB_TYPE == 'sqlite') {
		executeSQL("UPDATE [config] SET value = 'ebookcms_1.69' WHERE id = 1;");
		executeSQL("INSERT INTO [config] VALUES  (39,3, 'pmsg_limit', '10', 'je6ggfCX');");
		executeSQL("INSERT INTO [config] VALUES  (40,2, 'uplimg_access', '01', 'g4fdGh');");
		executeSQL("INSERT INTO [config] VALUES  (41,6, 'path_images', 'images', 'ggD33ddS');");
		executeSQL("INSERT INTO [config] VALUES  (42,6, 'thumbWidth', 'images', 'yGGrwS31');");
		executeSQL("INSERT INTO [config] VALUES  (43,6, 'thumbHeight', 'images', '9UUgft');");	
		executeSQL("ALTER TABLE [languages] RENAME TO [templng];");
		executeSQL("CREATE TABLE [languages] (
			[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
			[order_content] INTEGER  DEFAULT '0' NOT NULL,
			[enabled] TINYINT  DEFAULT '1' NOT NULL,
			[title] VARCHAR(60)  NOT NULL,
			[seftitle] VARCHAR(60)  UNIQUE NOT NULL,
			[use_prefix] TINYINT  DEFAULT '1' NOT NULL,
			[prefixo] VARCHAR(2)  UNIQUE NOT NULL,
			[page_id] INTEGER  DEFAULT '0' NOT NULL,
			[image_li] VARCHAR(128)  DEFAULT '1' NOT NULL,
			[charset] VARCHAR(32)  DEFAULT '' NOT NULL,
			[csymbol] VARCHAR(10) DEFAULT '&euro;' NOT NULL,
			[cdecimal] TINYINT  DEFAULT '2' NOT NULL,
			[dec_point] VARCHAR(2) DEFAULT '.' NOT NULL,
			[thousands] VARCHAR(8) DEFAULT ' ' NOT NULL,
			[key_security] VARCHAR(16)  NOT NULL,
			[del_key] VARCHAR(16)  NOT NULL
		);");
		executeSQL("INSERT INTO [languages] ([order_content], [enabled], [title], [seftitle], [use_prefix], [prefixo], [page_id], [image_li], [charset], [key_security], [del_key])
			SELECT [order_content], [enabled], [title], [seftitle], [use_prefix], [prefixo], [page_id], [image_li], [charset], [key_security], [del_key]
			FROM [templng];");
		executeSQL("DROP TABLE IF EXISTS [templng];");
		executeSQL("ALTER TABLE [sections] RENAME TO [tempsec];");
		executeSQL("CREATE TABLE [sections] (
			[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
			[order_content] INTEGER  DEFAULT '0' NOT NULL,
			[lcontent] INTEGER DEFAULT '0' NOT NULL,
			[admin_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
			[add_content] VARCHAR(255)  DEFAULT '01' NOT NULL,
			[edit_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
			[delete_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
			[member_by] VARCHAR(255)  DEFAULT '00' NOT NULL,
			[options_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
			[group_by] TINYINT DEFAULT '0' NOT NULL,
			[enabled] TINYINT  DEFAULT '1' NOT NULL,
			[title] VARCHAR(32) NOT NULL,
			[seftitle] VARCHAR(32) UNIQUE NOT NULL,
			[sec_type] TINYINT  DEFAULT '0' NOT NULL,
			[plug_id] INTEGER  DEFAULT '0' NOT NULL,
			[only_author] TINYINT DEFAULT '1' NOT NULL,
			[csstype] VARCHAR(16)  DEFAULT '' NOT NULL,
			[option_img] TINYINT DEFAULT '0' NOT NULL,
			[image_folder] VARCHAR(64)  DEFAULT 'images' NOT NULL,
			[option_txt1] TINYINT DEFAULT '1' NOT NULL,
			[option_txt2] TINYINT DEFAULT '0' NOT NULL,
			[show_lang] TINYINT DEFAULT '0' NOT NULL,
			[show_date] TINYINT DEFAULT '0' NOT NULL,
			[show_pmsg] TINYINT DEFAULT '0' NOT NULL,
			[show_plugin] TINYINT DEFAULT '0' NOT NULL,
			[show_content] TINYINT DEFAULT '0' NOT NULL,
			[order_by] TINYINT DEFAULT '0' NOT NULL,
			[cssrule] VARCHAR(16)  DEFAULT '' NOT NULL,
			[key_security] VARCHAR(16)  NOT NULL,
			[del_key] VARCHAR(16)  NOT NULL
		);");
		executeSQL("INSERT INTO [sections] ([order_content], [lcontent], [admin_by], [add_content], [edit_by], [delete_by], [member_by],
			[group_by], [enabled], [title], [seftitle], [sec_type], [plug_id], [only_author], [csstype], [option_img], [image_folder], [option_txt1], [option_txt2], [show_lang], [show_date], [key_security], [del_key])
				SELECT [order_content], [lcontent], [admin_by], [add_content],	[edit_by], [delete_by],	[member_by], 
			[group_by], [enabled], [title], [seftitle], [sec_type], [plug_id], [only_author], [csstype],[option_img], [image_folder], [option_txt1], [option_txt2], [show_lang], [show_date], [key_security], [del_key] 
			FROM [tempsec];");
		executeSQL("DROP TABLE IF EXISTS [tempsec];");
		executeSQL("ALTER TABLE [pages] RENAME TO [temppag];");
		executeSQL("CREATE TABLE [pages] (
			[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
			[order_content] INTEGER  DEFAULT '1' NOT NULL,
			[ptype] TINYINT  DEFAULT '0' NOT NULL,
			[section_id] INTEGER  DEFAULT '0' NOT NULL,
			[lang_id] INTEGER  DEFAULT '1' NOT NULL,
			[published] TINYINT  DEFAULT '1' NOT NULL,
			[date_reg] datetime NULL,
			[date_upd] datetime NULL,
			[title] VARCHAR(128)  NOT NULL,
			[showtitle] TINYINT  DEFAULT '1' NOT NULL,
			[seftitle] VARCHAR(128)  UNIQUE NOT NULL,
			[image] varchar(255)  DEFAULT '' NOT NULL,
			[dmeta] VARCHAR(128)  DEFAULT NULL,
			[keywords] VARCHAR(128)  DEFAULT NULL,
			[texto1] TEXT  NULL,
			[texto2] TEXT  NULL,
			[author_id] INTEGER  DEFAULT '1' NOT NULL,
			[comments] INTEGER  DEFAULT '0' NOT NULL,
			[pcsstype] VARCHAR(16)  DEFAULT '' NOT NULL,
			[show_pmsg] TINYINT  DEFAULT '0' NOT NULL,
			[plugin_id] INTEGER  DEFAULT '0' NOT NULL,
			[content_id] INTEGER  DEFAULT '0' NOT NULL,
			[key_security] VARCHAR(16)  NOT NULL,
			[del_key] VARCHAR(16)  NOT NULL
		);");
		executeSQL("INSERT INTO [pages] ([order_content], [ptype], [section_id],	[lang_id], [published],	[date_reg], [date_upd],	[title], 
			[showtitle], [seftitle], [image], [dmeta],	[keywords], [texto1], [texto2],	[author_id], [comments], [pcsstype], [key_security], 
			[del_key])
				SELECT [order_content], [ptype], [section_id],	[lang_id], [published],	[date_reg], [date_upd],	[title], [showtitle], 
			[seftitle], [image], [dmeta],	[keywords], [texto1], [texto2],	[author_id], [comments], [pcsstype], [key_security], [del_key]  
			FROM [temppag];");
		executeSQL("DROP TABLE IF EXISTS [temppag];");
		executeSQL("ALTER TABLE [users] RENAME TO [tempusrs];");
		executeSQL("CREATE TABLE [users] (
			[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
			[user_active] INTEGER  DEFAULT '1' NOT NULL,
			[user_ip] VARCHAR(255)  DEFAULT '' NOT NULL,
			[username] VARCHAR(128)  UNIQUE NOT NULL,
			[realname] VARCHAR(255)  DEFAULT '' NOT NULL,
			[avatar] VARCHAR(255)  DEFAULT NULL,
			[password] VARCHAR(40)  DEFAULT '' NOT NULL,
			[gmember] TINYINT  DEFAULT '0' NOT NULL,
			[browser_id] VARCHAR(40)  DEFAULT '' NOT NULL,
			[emailreg] VARCHAR(255)  DEFAULT NULL,
			[email] VARCHAR(255)  DEFAULT NULL,
			[website] VARCHAR(255)  DEFAULT NULL,
			[first_login] TINYINT  DEFAULT '1' NOT NULL,
			[last_login] datetime DEFAULT NULL,
			[last_logoff] datetime DEFAULT NULL,
			[secretQuestion] VARCHAR(255)  DEFAULT '' NOT NULL,
			[secretAnswer] VARCHAR(255)  DEFAULT '' NOT NULL,
			[regdate] DATE DEFAULT '2015-08-16' NOT NULL ,
			[total_timedate] VARCHAR(16)  DEFAULT '' NOT NULL,
			[vcode] VARCHAR(16)  DEFAULT ''  NOT NULL,
			[ask_sp] TINYINT  DEFAULT '0' NOT NULL,
			[special_num] VARCHAR(32) DEFAULT '' NOT NULL,
			[akey] VARCHAR(64) DEFAULT '' NOT NULL,
			[key_security] VARCHAR(16)  NOT NULL,
			[del_key] VARCHAR(16)  NOT NULL
		);");
		executeSQL("INSERT INTO [users] ([user_active], [user_ip], [username], [realname], [avatar], [password], [gmember], [browser_id], [emailreg],
			[email], [website], [first_login], [last_login], [last_logoff], [secretQuestion], [secretAnswer],[regdate],
			[total_timedate], [vcode], [key_security], [del_key])
				SELECT [user_active], [user_ip], [username], [realname], [avatar], [password], [gmember], [browser_id], [emailreg],
			[email], [website], [first_login], [last_login], [last_logoff], [secretQuestion], [secretAnswer],[regdate],
			[total_timedate], [validation_code], [key_security], [del_key] 
			FROM [tempusrs];");
		executeSQL("ALTER TABLE [comments] RENAME TO [tempucmt];");
		executeSQL("CREATE TABLE [comments] (
			[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
			[ptype] TINYINT  DEFAULT '0' NOT NULL,
			[plug_id] INTEGER  DEFAULT '0' NOT NULL,
			[pcontent_id] INTEGER  DEFAULT '0' NOT NULL,
			[reply_id] INTEGER  DEFAULT '0' NOT NULL,
			[user_id] INTEGER  DEFAULT '0' NOT NULL,
			[user_ip] VARCHAR(120)  DEFAULT '' NOT NULL,
			[name] VARCHAR(255)  NOT NULL, 
			[avatar_email] VARCHAR(255)  NOT NULL, 
			[url] VARCHAR(255)  NOT NULL, 
			[comment]  TEXT  NULL,
			[date_reg] datetime DEFAULT '2015-08-16 12:00:00' NOT NULL ,
			[date_upd] datetime DEFAULT '2015-08-16 12:00:00' NOT NULL ,
			[approved] TINYINT  DEFAULT '0' NOT NULL,
			[report_abuse] TINYINT  DEFAULT '0' NOT NULL,
			[spam] TINYINT  DEFAULT '0' NOT NULL,
			[block_user] TINYINT  DEFAULT '0' NOT NULL,
			[block_email] TINYINT  DEFAULT '0' NOT NULL,
			[archive_comment] TINYINT  DEFAULT '0' NOT NULL,
			[key_security] VARCHAR(16)  NOT NULL,
			[delkey] VARCHAR(16)  NOT NULL
		);");
		executeSQL("DROP TABLE IF EXISTS [tempusrs];");
		executeSQL("INSERT INTO [comments] ([ptype], [plug_id], [pcontent_id], [reply_id], [user_id], [user_ip], [name], [avatar_email], [url], [comment],
			[date_reg], [date_upd], [approved], [report_abuse], [spam], [block_user], [block_email], [archive_comment],
			[key_security],	[delkey])
			SELECT [ptype], [plug_id], [pcontent_id], [reply_id], [user_id], [user_ip], [name], [avatar_email], [url], [comment],
			[date_reg], [date_upd], [approved], [report_abuse], [spam], [block_user], [block_email], [archive_comment],
			[key_security],	[delkey] FROM [tempucmt];");
		executeSQL("DROP TABLE IF EXISTS [tempucmt];");
		executeSQL("CREATE TABLE [pmessages] (
			[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
			[ptype] TINYINT  DEFAULT '0' NOT NULL,
			[user_id] INTEGER  DEFAULT '0' NOT NULL,
			[suser_id] INTEGER  DEFAULT '1' NOT NULL,
			[gmember] INTEGER  DEFAULT '1' NOT NULL,
			[page_id] INTEGER  DEFAULT '1' NOT NULL,
			[lang_id] INTEGER  DEFAULT '1' NOT NULL,
			[date_sent] datetime NULL,
			[name] VARCHAR(64)  NOT NULL,
			[email] VARCHAR(255)  NOT NULL, 
			[subject] VARCHAR(32)  NOT NULL,
			[message] TEXT NOT NULL,
			[unread] TINYINT  DEFAULT '1' NOT NULL,
			[key_security] VARCHAR(16)  NOT NULL,
			[delkey] VARCHAR(16)  NOT NULL
		);");
	} else if (defined('DB_TYPE') && DB_TYPE == 'mysql') {
	// RUN mySQL COMMANDS TO UPDATE DATABASE
		executeSQL("UPDATE `config` SET value = 'ebookcms_1.69' WHERE id = 1;");
		executeSQL("INSERT INTO `config` VALUES  (39,3, 'pmsg_limit', '10', 'je6ggfCX');");
		executeSQL("INSERT INTO `config` VALUES  (40,2, 'uplimg_access', '01', 'g4fdGh');");
		executeSQL("INSERT INTO `config` VALUES  (41,6, 'path_images', 'images', 'ggD33ddS');");
		executeSQL("INSERT INTO `config` VALUES  (42,6, 'thumbWidth', 'images', 'yGGrwS31');");
		executeSQL("INSERT INTO `config` VALUES  (43,6, 'thumbHeight', 'images', '9UUgft');");	
		executeSQL("ALTER TABLE `languages` RENAME TO `templng`;");
		executeSQL("CREATE TABLE `languages` (
			`id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
			`order_content` tinyint(1) unsigned NOT NULL DEFAULT '0',
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`title` varchar(60) DEFAULT NULL,
			`seftitle` varchar(60) DEFAULT NULL,
			`use_prefix` tinyint(1) NOT NULL DEFAULT '1',
			`prefixo` varchar(2) DEFAULT NULL,
			`page_id` int(4) unsigned NOT NULL DEFAULT '0',
			`image_li` varchar(128) DEFAULT NULL,
			`charset` varchar(32) DEFAULT NULL,
			`csymbol` varchar(10) NOT NULL DEFAULT '&euro;',
			`cdecimal` tinyint(1)  NOT NULL DEFAULT '2',
			`dec_point` varchar(2) NOT NULL DEFAULT  '.',
			`thousands` varchar(8) NOT NULL DEFAULT ' ',
			`key_security` varchar(16) NOT NULL DEFAULT '',
			`del_key` varchar(16) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`),
			UNIQUE KEY `seftitle` (`seftitle`),
			UNIQUE KEY `prefixo` (`prefixo`)
		);");
		executeSQL("INSERT INTO `languages` (`order_content`, `enabled`, `title`, `seftitle`, `use_prefix`, `prefixo`, `page_id`, `image_li`, `charset`, `key_security`, `del_key`)
			SELECT `order_content`, `enabled`, `title`, `seftitle`, `use_prefix`, `prefixo`, `page_id`, `image_li`, `charset`, `key_security`, `del_key` FROM `templng`;");
		executeSQL("DROP TABLE IF EXISTS `templng`;");
		executeSQL("ALTER TABLE `sections` RENAME TO `tempsec`;");
		executeSQL("CREATE TABLE `sections` (
		 	`id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
		 	`order_content` smallint(2) unsigned NOT NULL DEFAULT '0',
			`lcontent` tinyint(1) NOT NULL DEFAULT '1',
			`admin_by` varchar(255) DEFAULT '01' NOT NULL,
			`add_content` varchar(255) DEFAULT '01' NOT NULL,
			`edit_by` varchar(255) DEFAULT '01' NOT NULL,
			`delete_by` varchar(255) DEFAULT '01' NOT NULL,
		 	`member_by` varchar(255) DEFAULT '00' NOT NULL,
		 	`options_by` varchar(255)  DEFAULT '01' NOT NULL,
			`group_by` tinyint(1) unsigned NOT NULL DEFAULT '0',
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`title` varchar(32) DEFAULT NULL,
			`seftitle` varchar(32) DEFAULT NULL,
			`sec_type` tinyint(1) NOT NULL DEFAULT '0',
			`plug_id` smallint(2) unsigned NOT NULL DEFAULT '0',
			`only_author` tinyint(1) NOT NULL DEFAULT '1',
			`csstype` varchar(16)  DEFAULT '' NOT NULL,
			`option_img` tinyint(1) NOT NULL DEFAULT '0',
			`image_folder` varchar(64)  DEFAULT '' NOT NULL,
			`option_txt1` tinyint(1) NOT NULL DEFAULT '1',
			`option_txt2` tinyint(1) NOT NULL DEFAULT '0',
			`show_lang` tinyint(1) NOT NULL DEFAULT '0',
			`show_date` tinyint(1) NOT NULL DEFAULT '0',
			`show_pmsg` tinyint(1) NOT NULL DEFAULT '0', 
			`show_plugin` tinyint(1) NOT NULL DEFAULT '0',
			`show_content` tinyint(1) NOT NULL DEFAULT '0',
			`order_by` tinyint(1) NOT NULL DEFAULT '0',
			`cssrule` varchar(16)  DEFAULT '' NOT NULL,
			`key_security` varchar(16) NOT NULL DEFAULT '',
			`del_key` varchar(16) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`),
			UNIQUE KEY `seftitle` (`seftitle`)
		);");
		executeSQL("INSERT INTO `sections` (`order_content`, `lcontent`, `admin_by`, `add_content`, `edit_by`, `delete_by`, `member_by`,
			`group_by`, `enabled`, `title`, `seftitle`, `sec_type`, `plug_id`, `only_author`, `csstype`, `option_img`, `image_folder`, `option_txt1`, `option_txt2`, `show_lang`, `show_date`, `key_security`, `del_key`)
				SELECT `order_content`, `lcontent`, `admin_by`, `add_content`,	`edit_by`, `delete_by`,	`member_by`, 
			`group_by`, `enabled`, `title`, `seftitle`, `sec_type`, `plug_id`, `only_author`, `csstype`,`option_img`, `image_folder`, `option_txt1`, `option_txt2`, `show_lang`, `show_date`, `key_security`, `del_key` 
			FROM `tempsec`;");
		executeSQL("DROP TABLE IF EXISTS `tempsec`;");
		executeSQL("ALTER TABLE `pages` RENAME TO `temppag`;");
		executeSQL("CREATE TABLE `pages` (
			`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
			`order_content` int(4) unsigned NOT NULL DEFAULT '1',
			`ptype` tinyint(1) NOT NULL DEFAULT '0',
			`section_id` smallint(2) unsigned  DEFAULT '0' NOT NULL,
			`lang_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
			`published` tinyint(1) NOT NULL DEFAULT '1',
			`date_reg` datetime NULL,
			`date_upd` datetime NULL,
			`title` varchar(128)  NOT NULL,
			`showtitle` tinyint(1) NOT NULL DEFAULT '1',
			`seftitle` varchar(128)  NOT NULL,
			`image` varchar(255)  DEFAULT '' NOT NULL,
			`dmeta` varchar(128)  DEFAULT NULL,
			`keywords` varchar(128)  DEFAULT NULL,
			`texto1` text  NULL,
			`texto2` text  NULL,
			`author_id` int(4) unsigned  DEFAULT '1' NOT NULL,
			`comments` int(4) unsigned  DEFAULT '0' NOT NULL,
			`pcsstype` varchar(16)  DEFAULT '' NOT NULL,
			`show_pmsg` tinyint(1) NOT NULL DEFAULT '0',
			`plugin_id` smallint(2) NOT NULL DEFAULT '0',
			`content_id` smallint(2) NOT NULL DEFAULT '0',
			`key_security` varchar(16)  NOT NULL,
			`del_key` varchar(16)  NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `seftitle` (`seftitle`)
		);
		");
		executeSQL("INSERT INTO `pages` (`order_content`, `ptype`, `section_id`,	`lang_id`, `published`,	`date_reg`, `date_upd`,	`title`, 
			`showtitle`, `seftitle`, `image`, `dmeta`,	`keywords`, `texto1`, `texto2`,	`author_id`, `comments`, `pcsstype`, `key_security`, 
			`del_key`)
				SELECT `order_content`, `ptype`, `section_id`,	`lang_id`, `published`,	`date_reg`, `date_upd`,	`title`, `showtitle`, 
			`seftitle`, `image`, `dmeta`,	`keywords`, `texto1`, `texto2`,	`author_id`, `comments`, `pcsstype`, `key_security`, `del_key`  
			FROM `temppag`;");
		executeSQL("DROP TABLE IF EXISTS `temppag`;");
		executeSQL("ALTER TABLE `users` RENAME TO `tempusrs`;");
		executeSQL("CREATE TABLE `users` (
			`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
			`user_active` tinyint(1)  DEFAULT '1' NOT NULL,
			`user_ip` varchar(255)  DEFAULT '' NOT NULL,
			`username` varchar(128)  NOT NULL,
			`realname` varchar(255)  DEFAULT '' NOT NULL,
			`avatar` varchar(255)  DEFAULT NULL,
			`password` varchar(40)  DEFAULT '' NOT NULL,
			`gmember` tinyint(1) unsigned  DEFAULT '0' NOT NULL,
			`browser_id` varchar(40)  DEFAULT '' NOT NULL,
			`emailreg` varchar(255)  DEFAULT NULL,
			`email` varchar(255)  DEFAULT NULL,
			`website` varchar(255)  DEFAULT NULL,
			`first_login` tinyint(1)  DEFAULT '1' NOT NULL,
			`last_login` datetime DEFAULT NULL,
			`last_logoff` datetime DEFAULT NULL,
			`secretQuestion` varchar(255)  DEFAULT '' NOT NULL,
			`secretAnswer` varchar(255)  DEFAULT '' NOT NULL,
			`regdate` date NOT NULL DEFAULT '2015-08-16',
			`total_timedate` varchar(16)  DEFAULT '0' NOT NULL,
			`vcode` varchar(16)  DEFAULT ''  NOT NULL,
			`ask_sp` tinyint(1)  DEFAULT '0' NOT NULL,
			`special_num` varchar(32)  DEFAULT ''  NOT NULL,
			`akey` varchar(64)  DEFAULT ''  NOT NULL,
			`key_security` varchar(16)  NOT NULL,
			`del_key` varchar(16)  NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `username` (`username`)
		);");
		executeSQL("INSERT INTO `users` (`user_active`, `user_ip`, `username`, `realname`, `avatar`, `password`, `gmember`, `browser_id`, `emailreg`,
			`email`, `website`, `first_login`, `last_login`, `last_logoff`, `secretQuestion`, `secretAnswer`,`regdate`,
			`total_timedate`, `vcode`, `key_security`, `del_key`)
				SELECT `user_active`, `user_ip`, `username`, `realname`, `avatar`, `password`, `gmember`, `browser_id`, `emailreg`,
			`email`, `website`, `first_login`, `last_login`, `last_logoff`, `secretQuestion`, `secretAnswer`,`regdate`,
			`total_timedate`, `validation_code`, `key_security`, `del_key` 
			FROM `tempusrs`;");
		executeSQL("DROP TABLE IF EXISTS `tempusrs`;");
		executeSQL("ALTER TABLE `comments` RENAME TO `tempucmt`;");
		executeSQL("CREATE TABLE `comments` (
		 	`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
		 	`ptype` tinyint(1) NOT NULL DEFAULT '0',
		 	`plug_id` int(4) DEFAULT '0',
		 	`pcontent_id` int(4) DEFAULT '0',
		 	`reply_id` int(4) DEFAULT '0',
		 	`user_id` int(4) DEFAULT '0',
		 	`user_ip` varchar(120) NOT NULL DEFAULT '',
		 	`name` varchar(255) NOT NULL,
		 	`avatar_email` varchar(255) NOT NULL,
		 	`url` varchar(255) NOT NULL,
		 	`comment` text,
		 	`date_reg` datetime NOT NULL DEFAULT '2015-08-16 12:00:00',
		 	`date_upd` datetime NOT NULL DEFAULT '2015-08-16 12:00:00',
		 	`approved` tinyint(1) NOT NULL DEFAULT '0',
		 	`report_abuse` tinyint(1) NOT NULL DEFAULT '0',
		 	`spam` tinyint(1) NOT NULL DEFAULT '0',
		 	`block_user` tinyint(1) NOT NULL DEFAULT '0',
		 	`block_email` tinyint(1) NOT NULL DEFAULT '0',
		 	`archive_comment` tinyint(1) NOT NULL DEFAULT '0',
			`key_security` varchar(16) NOT NULL DEFAULT '',
			`delkey` varchar(16) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`)
		);");
		executeSQL("INSERT INTO `comments` (`ptype`, `plug_id`, `pcontent_id`, `reply_id`, `user_id`, `user_ip`, `name`, `avatar_email`, `url`, `comment`,
			`date_reg`, `date_upd`, `approved`, `report_abuse`, `spam`, `block_user`, `block_email`, `archive_comment`,
			`key_security`,	`delkey`)
			SELECT `ptype`, `plug_id`, `pcontent_id`, `reply_id`, `user_id`, `user_ip`, `name`, `avatar_email`, `url`, `comment`,
			`date_reg`, `date_upd`, `approved`, `report_abuse`, `spam`, `block_user`, `block_email`, `archive_comment`,
			`key_security`,	`delkey` FROM `tempucmt`;");
		executeSQL("DROP TABLE IF EXISTS `tempucmt`;");
		executeSQL("CREATE TABLE `pmessages` (
			`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
			`ptype` tinyint(1) NOT NULL DEFAULT '0',
			`user_id` int(4) DEFAULT '0' NOT NULL,
			`suser_id` int(4) DEFAULT '1' NOT NULL,
			`gmember` tinyint(1) unsigned  DEFAULT '1' NOT NULL,
			`page_id` int(4) DEFAULT '0' NOT NULL,
			`lang_id` tinyint(1) unsigned DEFAULT '1' NOT NULL,
			`date_sent` datetime DEFAULT NULL,
			`name` varchar(64) NOT NULL,
			`email` varchar(255) NOT NULL,
			`subject` varchar(32) NOT NULL,
			`message`  text  NULL,
			`unread` tinyint(1) NOT NULL DEFAULT '1',
			`key_security` varchar(16) DEFAULT NULL,
			`delkey` varchar(16) DEFAULT NULL,
			PRIMARY KEY (`id`)
		);");
	}
} else if (load_cfg('core_version') == 'ebookcms_1.65' || load_cfg('core_version') == 'ebookcms_1.66'  || load_cfg('core_version') == 'ebookcms_1.67' || 
load_cfg('core_version') == 'ebookcms_1.68') {
	if (defined('DB_TYPE') && DB_TYPE == 'sqlite') {
		executeSQL("UPDATE [config] SET field = 'core_version', value = 'ebookcms_1.69' WHERE id = 1;");
		executeSQL("INSERT INTO [config] VALUES  (40,2, 'uplimg_access', '01', 'g4fdGh');");
		executeSQL("INSERT INTO [config] VALUES  (41,6, 'path_images', 'images', 'ggD33ddS');");
		executeSQL("INSERT INTO [config] VALUES  (42,6, 'thumbWidth', 'images', 'yGGrwS31');");
		executeSQL("INSERT INTO [config] VALUES  (43,6, 'thumbHeight', 'images', '9UUgft');");	
	} else {
		executeSQL("UPDATE `config` SET field = 'core_version', value = 'ebookcms_1.69' WHERE id = 1;");
		executeSQL("INSERT INTO `config` VALUES  (40,2, 'uplimg_access', '01', 'g4fdGh');");
		executeSQL("INSERT INTO `config` VALUES  (41,6, 'path_images', 'images', 'ggD33ddS');");
		executeSQL("INSERT INTO `config` VALUES  (42,6, 'thumbWidth', 'images', 'yGGrwS31');");
		executeSQL("INSERT INTO `config` VALUES  (43,6, 'thumbHeight', 'images', '9UUgft');");			
	}
}


$nusers = retrieve_mysql('users', 'akey', "''", 'count(id) AS total');

if ($nusers && $nusers['total'] != '0') {
	echo 'PLEASE refresh until this message disapears. There are '.$nusers['total'].' users need to update.';
	for ($i= 0; $i < $ttotal; $i++) {
	 	$data = retrieve_mysql('users', 'akey', "''", 'id, username, realname, password, emailreg, secretQuestion, secretAnswer');
	 	$akey = gen_PKey(64);
		$user = rdecrypt($data['username'], '', false); $user = rencrypt1($user);
		$real = rdecrypt($data['realname'], '', false); $real = rencrypt1($real);
		$pass = erukka1($data['password']);
		$email = rdecrypt($data['emailreg'], '', false);
		$reg = rencrypt($email, $akey);
		$email = erukka($email, $akey, false);
		$question = rdecrypt($data['secretQuestion'], '', false); $question = rencrypt($question, $akey);
		$answer = rdecrypt($data['secretAnswer'], '', false); $answer = rencrypt($answer, $akey);
		$sql = "UPDATE ".PREFIX."users 
			SET username = '$user', realname = '$real', password = '$pass', akey ='$akey', emailreg = '$reg', 
				email = '$email', avatar = '$email', secretQuestion = '$question', secretAnswer = '$answer'
			WHERE id = ".$data['id'];
		if ($result = db() -> query($sql)) {
			$r = dbfetch($result);
		}
	}
} else {
	unlink('addons/updateusers.php');
	echo '<p>If you can see this message you must delete this addon. Open addons folder and delete this addon "updateusers.php".</p>';
	echo '<p>Try refresh first</p>';
	
}

?>