-- ---------------------------------------------------------------------------
--
--					ebookcms Update mySQL (FROM 1.64) - All rights reserved
--					Copyright (c) 2015 by Rui Mendes
--					Revision: 0
--
-- ---------------------------------------------------------------------------

-- If you want change collate (I use utf8_general_ci), preferences goes to utf8
-- ALTER DATABASE `your_database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci


SET NAMES 'utf8';

SET storage_engine = INNODB;

-- UPDATE core_version
UPDATE `config` SET value = 'ebookcms_1.69' WHERE id = 1;

--CONFIG
INSERT INTO `config` VALUES  (39,3, 'pmsg_limit', '10', 'je6ggfCX');
-- eBOOKCMS 1.69
INSERT INTO `config` VALUES  (40,2, 'uplimg_access', '01', 'h5Fds');
INSERT INTO `config` VALUES  (41,6, 'path_images', 'images', '9GGf7g');
INSERT INTO `config` VALUES  (42,6, 'thumbWidth', '100', '4ggTGNL8');
INSERT INTO `config` VALUES  (43,6, 'thumbHeight', '100', 'trs5FD');

-- COPY LANGUAGES TO TEMP
ALTER TABLE `languages` RENAME TO `templng`;
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
INSERT INTO `languages` (`order_content`, `enabled`, `title`, `seftitle`, `use_prefix`, `prefixo`, `page_id`, `image_li`, `charset`, `key_security`, `del_key`)
	SELECT `order_content`, `enabled`, `title`, `seftitle`, `use_prefix`, `prefixo`, `page_id`, `image_li`, `charset`, `key_security`, `del_key` FROM `templng`;
	DROP TABLE IF EXISTS `templng`;
	
	
-- COPY SECTIONS TO TEMP
ALTER TABLE `sections` RENAME TO `tempsec`;
CREATE TABLE `sections` (
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
	`show_pmsg` tinyint(1) NOT NULL DEFAULT '0',
	`show_plugin` tinyint(1) NOT NULL DEFAULT '0',
	`show_content` tinyint(1) NOT NULL DEFAULT '0',
	`order_by` tinyint(1) NOT NULL DEFAULT '0',
	`cssrule` varchar(16)  DEFAULT '' NOT NULL,
	-- KEYS
	`key_security` varchar(16) NOT NULL DEFAULT '',
	`del_key` varchar(16) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `seftitle` (`seftitle`)
);

INSERT INTO `sections` (`order_content`, `lcontent`, `admin_by`, `add_content`, `edit_by`, `delete_by`, `member_by`,
`group_by`, `enabled`, `title`, `seftitle`, `sec_type`, `plug_id`, `only_author`, `csstype`, `option_img`, `image_folder`, `option_txt1`, `option_txt2`, `show_lang`, `show_date`, `key_security`, `del_key`)
	SELECT `order_content`, `lcontent`, `admin_by`, `add_content`,	`edit_by`, `delete_by`,	`member_by`, 
`group_by`, `enabled`, `title`, `seftitle`, `sec_type`, `plug_id`, `only_author`, `csstype`,`option_img`, `image_folder`, `option_txt1`, `option_txt2`, `show_lang`, `show_date`, `key_security`, `del_key` 
FROM `tempsec`;
	DROP TABLE IF EXISTS `tempsec`;

-- COPY PAGES TO TEMP
ALTER TABLE `pages` RENAME TO `temppag`;
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
INSERT INTO `pages` (`order_content`, `ptype`, `section_id`,	`lang_id`, `published`,	`date_reg`, `date_upd`,	`title`, 
`showtitle`, `seftitle`, `image`, `dmeta`,	`keywords`, `texto1`, `texto2`,	`author_id`, `comments`, `pcsstype`, `key_security`, 
`del_key`)
	SELECT `order_content`, `ptype`, `section_id`,	`lang_id`, `published`,	`date_reg`, `date_upd`,	`title`, `showtitle`, 
`seftitle`, `image`, `dmeta`,	`keywords`, `texto1`, `texto2`,	`author_id`, `comments`, `pcsstype`, `key_security`, `del_key`  
FROM `temppag`;
	DROP TABLE IF EXISTS `temppag`;

-- COPY USERS TO TEMP
ALTER TABLE `users` RENAME TO `tempusrs`;
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
	`regdate` date NOT NULL DEFAULT '2015-08-16',
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
INSERT INTO `users` (`user_active`, `user_ip`, `username`, `realname`, `avatar`, `password`, `gmember`, `browser_id`, `emailreg`,
`email`, `website`, `first_login`, `last_login`, `last_logoff`, `secretQuestion`, `secretAnswer`,`regdate`,
`total_timedate`, `vcode`, `key_security`, `del_key`)
	SELECT `user_active`, `user_ip`, `username`, `realname`, `avatar`, `password`, `gmember`, `browser_id`, `emailreg`,
`email`, `website`, `first_login`, `last_login`, `last_logoff`, `secretQuestion`, `secretAnswer`,`regdate`,
`total_timedate`, `validation_code`, `key_security`, `del_key` 
FROM `tempusrs`;
	DROP TABLE IF EXISTS `tempusrs`;

-- COPY COMMENTS TO TEMP
ALTER TABLE `comments` RENAME TO `tempucmt`;
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
);
INSERT INTO `comments` (`ptype`, `plug_id`, `pcontent_id`, `reply_id`, `user_id`, `user_ip`, `name`, `avatar_email`, `url`, `comment`,
	`date_reg`, `date_upd`, `approved`, `report_abuse`, `spam`, `block_user`, `block_email`, `archive_comment`,
	`key_security`,	`delkey`)
	SELECT `ptype`, `plug_id`, `pcontent_id`, `reply_id`, `user_id`, `user_ip`, `name`, `avatar_email`, `url`, `comment`,
	`date_reg`, `date_upd`, `approved`, `report_abuse`, `spam`, `block_user`, `block_email`, `archive_comment`,
	`key_security`,	`delkey` FROM `tempucmt`;
	DROP TABLE IF EXISTS `tempucmt`;


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