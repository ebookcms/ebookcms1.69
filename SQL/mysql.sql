-- ---------------------------------------------------------------------------
--
--					ebookcms 1.69 - All rights reserved
--					Copyright (c) 2015 by Rui Mendes
--					Revision: 0
--
-- ---------------------------------------------------------------------------

-- If you want change collate (I use utf8_general_ci), preferences goes to utf8
-- ALTER DATABASE `your_database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci


SET NAMES 'utf8';

SET storage_engine = INNODB;

CREATE TABLE `config` (
 	`id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
 	`fgroup` tinyint(1) NOT NULL DEFAULT '0',
 	`field` varchar(32) NOT NULL,
 	`value` varchar(128) NOT NULL,
 	`key_security` varchar(16) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `field` (`field`)
);

INSERT INTO `config` VALUES  (1, 1, 'core_version', 'ebookcms_1.69', 'jjHe3GF');
INSERT INTO `config` VALUES  (2, 1, 'web_title', 'eBookCMS 1.69', 'hu3e3GF');
INSERT INTO `config` VALUES  (3, 1, 'web_charset', 'UTF-8', 'h3GFSp');
INSERT INTO `config` VALUES  (4, 1, 'web_keywords', 'cms', 'jhjHG09jk');
INSERT INTO `config` VALUES  (5, 1, 'web_email', 'your_email@your_domain.com', 'jh98GFVs');
INSERT INTO `config` VALUES  (6, 1, 'web_author', 'Your name', 'kjhj8HH2');
INSERT INTO `config` VALUES  (7, 1, 'web_lang', 'EN', 'jjhGGle');
INSERT INTO `config` VALUES  (8, 1, 'description', 'eBook Content Management System', 'jjk4hG');
INSERT INTO `config` VALUES  (9, 1, 'date_format', 'Y-m-d', 'jj6GGa');
INSERT INTO `config` VALUES  (10,1, 'registration', '1', 'g6FAdd');
INSERT INTO `config` VALUES  (11,2, 'root_access', '01', 'dd3Ja');
INSERT INTO `config` VALUES  (12,2, 'bigroot_access', '01', 'Fjh78A');
INSERT INTO `config` VALUES  (13,2, 'config_access', '01', 'oLks23');
INSERT INTO `config` VALUES  (14,2, 'lang_access', '01', 'ds7haGG');
INSERT INTO `config` VALUES  (15,2, 'section_access', '01', 'fgY2yu');
INSERT INTO `config` VALUES  (16,2, 'users_access', '01', 'wer4jHa');
INSERT INTO `config` VALUES  (17,2, 'plugins_install', '01', 'JyyUyg12');
INSERT INTO `config` VALUES  (18,2, 'plugins_access', '01', '34HHAa');
INSERT INTO `config` VALUES  (19,2, 'comments_access', '01', 'h5FGFafd');
INSERT INTO `config` VALUES  (20,2, 'enable_poptions', '1', 'h6ffAMNi7');
INSERT INTO `config` VALUES  (21,3, 'langs_limit', '10', 'jh7GGb');
INSERT INTO `config` VALUES  (22,3, 'sections_limit', '10', 'je6ggfCX');
INSERT INTO `config` VALUES  (23,3, 'users_limit', '10', 'jdfiHJHw');
INSERT INTO `config` VALUES  (24,3, 'plugins_limit', '10', 'huyGjqa');
INSERT INTO `config` VALUES  (25,3, 'pages_limit', '10', 'jhjGhgf');
INSERT INTO `config` VALUES  (26,3, 'comments_limit', '10', 'ewe34aJ');
INSERT INTO `config` VALUES  (27,4, 'users_edit', '01', '4scgJgdyT');
INSERT INTO `config` VALUES  (28,4, 'users_delete', '01', 'dJh873HGF');
INSERT INTO `config` VALUES  (29,4, 'user_active', '2', '5kkjFdp');
INSERT INTO `config` VALUES  (30,4, 'user_member', '3', 'dds4Nhj2');
INSERT INTO `config` VALUES  (31,5, 'auto_approve_msg', '0', 'hFaghe4');
INSERT INTO `config` VALUES  (32,5, 'comments_order', 'ASC', 'jhjGa9');
INSERT INTO `config` VALUES  (33,5, 'comments_logged', '1', 'g1hGa');
INSERT INTO `config` VALUES  (34,5, 'level_comments', '1', 'Hj21h9j');
INSERT INTO `config` VALUES  (35,5, 'admin_messages', '01', 'hjHay6TA');
INSERT INTO `config` VALUES  (36,5, 'block_messages', '01', 'ggYT2ui');
INSERT INTO `config` VALUES  (37,5, 'archive_messages', '01', 'jH2jha');
INSERT INTO `config` VALUES  (38,5, 'spam_messages', '01', 'GY2FF72j');
INSERT INTO `config` VALUES  (39,3, 'pmsg_limit', '10', 'je6ggfCX');

-- LANGUAGE
CREATE TABLE `languages` (
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
	-- NEW V1.65
	`csymbol` varchar(10) NOT NULL DEFAULT '&euro;',
	`cdecimal` tinyint(1)  NOT NULL DEFAULT '2',
	`dec_point` varchar(2) NOT NULL DEFAULT  '.',
	`thousands` varchar(8) NOT NULL DEFAULT ' ',
	-- KEYS
	`key_security` varchar(16) NOT NULL DEFAULT '',
	`del_key` varchar(16) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `seftitle` (`seftitle`),
	UNIQUE KEY `prefixo` (`prefixo`)
);
INSERT INTO `languages` (`id`, `order_content`, `enabled`, `title`, `seftitle`, `use_prefix`, `prefixo`, `page_id`, 
	`image_li`, `charset`, `key_security`, `del_key`)
VALUES (1, 1, 1,  'English', 'english', 1, 'EN', 1, 'js/langs/en.png', 'UTF-8', 'jhh7UHG2h', 'gh2vcA6t');

-- SECTIONS
CREATE TABLE `sections` (
 	`id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
 	`order_content` smallint(2) unsigned NOT NULL DEFAULT '0',
	`lcontent` tinyint(1) NOT NULL DEFAULT '1',
	`admin_by` varchar(255) DEFAULT '01' NOT NULL,
	`add_content` varchar(255) DEFAULT '01' NOT NULL,
	`edit_by` varchar(255) DEFAULT '01' NOT NULL,
	`delete_by` varchar(255) DEFAULT '01' NOT NULL,
 	`member_by` varchar(255) DEFAULT '00' NOT NULL,
 	`options_by` varchar(255)  DEFAULT '01' NOT NULL,	-- 	NEW 1.65
	`group_by` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`enabled` tinyint(1) NOT NULL DEFAULT '1',
	`title` varchar(32) DEFAULT NULL,
	`seftitle` varchar(32) DEFAULT NULL,
	`sec_type` tinyint(1) NOT NULL DEFAULT '0',
		-- 0 none
		-- 1 pages
		-- 2 plugin
	`plug_id` smallint(2) unsigned NOT NULL DEFAULT '0',
	`only_author` tinyint(1) NOT NULL DEFAULT '1',
	`csstype` varchar(16)  DEFAULT '' NOT NULL,
	-- image
	`option_img` tinyint(1) NOT NULL DEFAULT '0',
	`image_folder` varchar(64)  DEFAULT '' NOT NULL,
	-- Multi-text
	`option_txt1` tinyint(1) NOT NULL DEFAULT '1',
	`option_txt2` tinyint(1) NOT NULL DEFAULT '0',
	-- options
	`show_lang` tinyint(1) NOT NULL DEFAULT '0',
	`show_date` tinyint(1) NOT NULL DEFAULT '0',
	`show_pmsg` tinyint(1) NOT NULL DEFAULT '0', 		-- NEW 1.65
	`show_plugin` tinyint(1) NOT NULL DEFAULT '0', 		-- NEW 1.65
	`show_content` tinyint(1) NOT NULL DEFAULT '0', 	-- NEW 1.65
	`order_by` tinyint(1) NOT NULL DEFAULT '0', 		-- NEW 1.65
	`cssrule` varchar(16)  DEFAULT '' NOT NULL, 		-- NEW 1.65
	-- KEYS
	`key_security` varchar(16) NOT NULL DEFAULT '',
	`del_key` varchar(16) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `seftitle` (`seftitle`)
);
INSERT INTO `sections` (`order_content`, `title`, `seftitle`, `group_by`, `sec_type`, `key_security`, `del_key`) 
VALUES (1, 'Top-links', 'top-links', 1, 1, 'S3FIRs', 'yTp6zPsJK');

-- PAGES
CREATE TABLE `pages` (
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
	-- NEW V1.65
	`show_pmsg` tinyint(1) NOT NULL DEFAULT '0',
	`plugin_id` smallint(2) NOT NULL DEFAULT '0',
	`content_id` smallint(2) NOT NULL DEFAULT '0',
	-- keys
	`key_security` varchar(16)  NOT NULL,
	`del_key` varchar(16)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `seftitle` (`seftitle`)
);
INSERT INTO `pages` (`id`,`order_content`,`ptype`,`lang_id`,`section_id`,`published`,`date_reg`,`date_upd`,`title`,`showtitle`,`seftitle`,`dmeta`,`keywords`,`texto1`,`author_id`,`comments`,`key_security`,`del_key`) VALUES
('1', '1', '0', '1', '1', '1', '2015-09-16 12:00:00', '2015-09-16 12:00:00', 'Home', '1', 'home', 'Content Management System', 'ebookcms,cms', '<p>If you are seeing this page, you have installed <strong>ebookcms 1.69</strong> and it is connected to the database.</p><p>It is <strong>strongly</strong> suggested that you Login right away, then go to Personal Data and you will find <em>Change my Password</em>. <strong>Do it</strong> and make the password hard to guess.</p>', '1', '0', '2wpRPL', '1m8lxAW');

-- USERS
CREATE TABLE `users` (
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
	`regdate` date NOT NULL DEFAULT '2015-09-16',
	`total_timedate` varchar(16)  DEFAULT '0' NOT NULL,
	-- `validation_code` varchar(16)  DEFAULT ''  NOT NULL,	delete and changed to vcode
	`vcode` varchar(16)  DEFAULT ''  NOT NULL,
	`ask_sp` tinyint(1)  DEFAULT '0' NOT NULL,			-- NEW 1.65
	`special_num` varchar(32)  DEFAULT ''  NOT NULL, 	-- NEW 1.65
	`akey` varchar(64)  DEFAULT ''  NOT NULL,			-- NEW 1.65
	`key_security` varchar(16)  NOT NULL,
	`del_key` varchar(16)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`)
);
INSERT INTO `users` (`id`,`user_active`,`username`,`realname`, `avatar`,`password`,`gmember`,`browser_id`,
`emailreg`,`email`,`website`,`first_login`,`last_login`,`last_logoff`,`secretQuestion`,`secretAnswer`,`regdate`,
`total_timedate`,`vcode`,`key_security`,`del_key`)
VALUES (1, 2, '12c1582540f906c', '12824a19b0e408411016a11507300e0b80e2141', '12824a19b0e408411016a11507300e0b80e2141', '098f6bcd4621d373cade4e832627b4f6', '1',  '651','0e70e20dc0730de05f21706d0e412c00423019b07d1250931aa27206f0d0', '', '', 1, '2015-09-16 12:00:00', '2015-09-16 12:01:00', '0e80e20dc0730e60c609427210a0840ca2170bc', '12c2302720730fa0b8', '2015-09-16', '0', 'jh2G12u', '9hafHq2', 'jkhdHj337ghs');

-- LOGS
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
	`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
	`guest_ip` varchar(255)  DEFAULT '' NOT NULL,
	`browser_id` varchar(40)  DEFAULT '' NOT NULL,
	`fail` INTEGER  DEFAULT '1' NOT NULL,
	`fail_date` datetime DEFAULT NULL,
	`log_date` date DEFAULT NULL,
	PRIMARY KEY (`id`)
);

-- COMMENTS
CREATE TABLE `comments` (
 	`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
 	`ptype` tinyint(1) NOT NULL DEFAULT '0',
		-- 0 page
		-- 1 plugin
 	`plug_id` int(4) DEFAULT '0',
 	`pcontent_id` int(4) DEFAULT '0',
 	`reply_id` int(4) DEFAULT '0',
 	`user_id` int(4) DEFAULT '0',
 	`user_ip` varchar(120) NOT NULL DEFAULT '',
 	`name` varchar(255) NOT NULL,
 	`avatar_email` varchar(255) NOT NULL,
 	`url` varchar(255) NOT NULL,
 	`comment` text,
 	`date_reg` datetime NOT NULL DEFAULT '2015-09-16 12:00:00',
 	`date_upd` datetime NOT NULL DEFAULT '2015-09-16 12:00:00',
 	`approved` tinyint(1) NOT NULL DEFAULT '0',
 	`report_abuse` tinyint(1) NOT NULL DEFAULT '0',
 	`spam` tinyint(1) NOT NULL DEFAULT '0',
 	`block_user` tinyint(1) NOT NULL DEFAULT '0',
 	`block_email` tinyint(1) NOT NULL DEFAULT '0',
 	`archive_comment` tinyint(1) NOT NULL DEFAULT '0',
	`key_security` varchar(16) NOT NULL DEFAULT '',
	`delkey` varchar(16) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
);

CREATE TABLE `plugins` (
	`id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(80) DEFAULT NULL,
	`author` varchar(80) DEFAULT NULL,
	`description` varchar(255) DEFAULT NULL,
	`version` varchar(8) DEFAULT NULL,
	`auto_load` tinyint(1) NOT NULL DEFAULT '1',
	`enabled` tinyint(1) NOT NULL DEFAULT '1',
	`preinstalled` tinyint(1) NOT NULL DEFAULT '1',
	`installed` tinyint(1) NOT NULL DEFAULT '0',
	`admin_by` varchar(255) NOT NULL DEFAULT '01',
	`add_content` varchar(255) NOT NULL DEFAULT '01',
	`edit_by` varchar(255) NOT NULL DEFAULT '01',
	`delete_by` varchar(255) NOT NULL DEFAULT '01',
	`public_by` varchar(255) NOT NULL DEFAULT '00',
	`admin_func` varchar(64) DEFAULT NULL,
	`public_func` varchar(64) DEFAULT NULL,
	`sef_file` varchar(64) DEFAULT NULL,
	`filename` varchar(64) DEFAULT NULL,
	`install_table` varchar(40) DEFAULT NULL,
	`xtxt1` varchar(32) DEFAULT NULL,
	`xstr1` varchar(64) DEFAULT NULL,
	`xtxt2` varchar(32) DEFAULT NULL,
	`xint1` int(4) DEFAULT '0' NOT NULL,
	`xtxt3` varchar(32) DEFAULT NULL,
	`xdate1` datetime DEFAULT NULL,
	`key_security` varchar(16) NOT NULL DEFAULT '',
	`delkey` varchar(16) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `filename` (`filename`)
);

-- PERSONAL MESSAGES 1.65
CREATE TABLE `pmessages` (
	`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
	`ptype` tinyint(1) NOT NULL DEFAULT '0',
		-- 0 GUEST SEND MESSAGE TO ADMIN
	`user_id` int(4) DEFAULT '0' NOT NULL,
		-- 0 GUEST
	`suser_id` int(4) DEFAULT '1' NOT NULL,
		-- 0 Message for everybody, only Admin can delete
		-- n for specific user
	`gmember` tinyint(1) unsigned  DEFAULT '1' NOT NULL,
		-- 0 All groups
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
);