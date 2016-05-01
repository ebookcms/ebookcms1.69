-- ---------------------------------------------------------------------------
--
--					ebookcms 1.69 for SQLite - All rights reserved
--					Copyright (c) 2015 by Rui Mendes
--					Revision: 0 
--
-- ---------------------------------------------------------------------------

CREATE TABLE [config] (
	[id] INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	[fgroup] INTEGER  DEFAULT '0' NOT NULL,
	[field] VARCHAR(32)  NOT NULL,
	[value] VARCHAR(128)  NOT NULL,
	[key_security] VARCHAR(16)  NOT NULL
);
INSERT INTO [config] VALUES  (1, 1, 'core_version', 'ebookcms_1.69', 'jjHe3GF');
INSERT INTO [config] VALUES  (2, 1, 'web_title', 'eBookCMS 1.69', 'hu3e3GF');
INSERT INTO [config] VALUES  (3, 1, 'web_charset', 'UTF-8', 'h3GFSp');
INSERT INTO [config] VALUES  (4, 1, 'web_keywords', 'cms', 'jhjHG09jk');
INSERT INTO [config] VALUES  (5, 1, 'web_email', 'your_email@your_domain.com', 'jh98GFVs');
INSERT INTO [config] VALUES  (6, 1, 'web_author', 'Your name', 'kjhj8HH2');
INSERT INTO [config] VALUES  (7, 1, 'web_lang', 'EN', 'jjhGGle');
INSERT INTO [config] VALUES  (8, 1, 'description', 'eBook Content Management System', 'jjk4hG');
INSERT INTO [config] VALUES  (9, 1, 'date_format', 'Y-m-d', 'jj6GGa');
INSERT INTO [config] VALUES  (10,1, 'registration', '1', 'g6FAdd');
INSERT INTO [config] VALUES  (11,2, 'root_access', '01', 'dd3Ja');
INSERT INTO [config] VALUES  (12,2, 'bigroot_access', '01', 'Fjh78A');
INSERT INTO [config] VALUES  (13,2, 'config_access', '01', 'oLks23');
INSERT INTO [config] VALUES  (14,2, 'lang_access', '01', 'ds7haGG');
INSERT INTO [config] VALUES  (15,2, 'section_access', '01', 'fgY2yu');
INSERT INTO [config] VALUES  (16,2, 'users_access', '01', 'wer4jHa');
INSERT INTO [config] VALUES  (17,2, 'plugins_install', '01', 'JyyUyg12');
INSERT INTO [config] VALUES  (18,2, 'plugins_access', '01', '34HHAa');
INSERT INTO [config] VALUES  (19,2, 'comments_access', '01', 'h5FGFafd');
INSERT INTO [config] VALUES  (20,2, 'enable_poptions', '1', 'h6ffAMNi7');
INSERT INTO [config] VALUES  (21,3, 'langs_limit', '10', 'jh7GGb');
INSERT INTO [config] VALUES  (22,3, 'sections_limit', '10', 'je6ggfCX');
INSERT INTO [config] VALUES  (23,3, 'users_limit', '10', 'jdfiHJHw');
INSERT INTO [config] VALUES  (24,3, 'plugins_limit', '10', 'huyGjqa');
INSERT INTO [config] VALUES  (25,3, 'pages_limit', '10', 'jhjGhgf');
INSERT INTO [config] VALUES  (26,3, 'comments_limit', '10', 'ewe34aJ');
INSERT INTO [config] VALUES  (27,4, 'users_edit', '01', '4scgJgdyT');
INSERT INTO [config] VALUES  (28,4, 'users_delete', '01', 'dJh873HGF');
INSERT INTO [config] VALUES  (29,4, 'user_active', '2', '5kkjFdp');
INSERT INTO [config] VALUES  (30,4, 'user_member', '3', 'dds4Nhj2');
INSERT INTO [config] VALUES  (31,5, 'auto_approve_msg', '0', 'hFaghe4');
INSERT INTO [config] VALUES  (32,5, 'comments_order', 'ASC', 'jhjGa9');
INSERT INTO [config] VALUES  (33,5, 'comments_logged', '1', 'g1hGa');
INSERT INTO [config] VALUES  (34,5, 'level_comments', '1', 'Hj21h9j');
INSERT INTO [config] VALUES  (35,5, 'admin_messages', '01', 'hjHay6TA');
INSERT INTO [config] VALUES  (36,5, 'block_messages', '01', 'ggYT2ui');
INSERT INTO [config] VALUES  (37,5, 'archive_messages', '01', 'jH2jha');
INSERT INTO [config] VALUES  (38,5, 'spam_messages', '01', 'GY2FF72j');
INSERT INTO [config] VALUES  (39,3, 'pmsg_limit', '10', 'je6ggfCX');
INSERT INTO [config] VALUES  (40,2, 'uplimg_access', '01', 'h5Fds');
INSERT INTO [config] VALUES  (41,6, 'path_images', 'images', '9GGf7g');
INSERT INTO [config] VALUES  (42,6, 'thumbWidth', '100', '4ggTGNL8');
INSERT INTO [config] VALUES  (43,6, 'thumbHeight', '100', 'trs5FD');

-- LANGUAGE
CREATE TABLE [languages] (
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
	-- NEW V1.65
	[csymbol] VARCHAR(10) DEFAULT '&euro;' NOT NULL,
	[cdecimal] TINYINT  DEFAULT '2' NOT NULL,
	[dec_point] VARCHAR(2) DEFAULT '.' NOT NULL,
	[thousands] VARCHAR(8) DEFAULT ' ' NOT NULL,
	-- KEYS
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);
INSERT INTO [languages] ([id], [order_content], [enabled], [title], [seftitle], [use_prefix], [prefixo], [page_id], 
	[image_li], [charset], [key_security], [del_key])
VALUES (1, 1, 1,  'English', 'english', 1, 'EN', 1, 'js/langs/en.png', 'UTF-8', 'jhh7UHG2h', 'gh2vcA6t');


-- SECTIONS
CREATE TABLE [sections] (
	[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
	[order_content] INTEGER  DEFAULT '0' NOT NULL,
	[lcontent] INTEGER DEFAULT '0' NOT NULL,
	[admin_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[add_content] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[edit_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[delete_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[member_by] VARCHAR(255)  DEFAULT '00' NOT NULL,
	[options_by] VARCHAR(255)  DEFAULT '01' NOT NULL,	-- 	NEW 1.65
	[group_by] TINYINT DEFAULT '0' NOT NULL,
	[enabled] TINYINT  DEFAULT '1' NOT NULL,
	[title] VARCHAR(32) NOT NULL,
	[seftitle] VARCHAR(32) UNIQUE NOT NULL,
	[sec_type] TINYINT  DEFAULT '0' NOT NULL,
		-- 0 none
		-- 1 pages
		-- 2 plugin
	[plug_id] INTEGER  DEFAULT '0' NOT NULL,
	[only_author] TINYINT DEFAULT '1' NOT NULL,
	[csstype] VARCHAR(16)  DEFAULT '' NOT NULL,
	-- image
	[option_img] TINYINT DEFAULT '0' NOT NULL,
	[image_folder] VARCHAR(64)  DEFAULT 'images' NOT NULL,
	-- Multi-text
	[option_txt1] TINYINT DEFAULT '1' NOT NULL,
	[option_txt2] TINYINT DEFAULT '0' NOT NULL,
	-- options
	[show_lang] TINYINT DEFAULT '0' NOT NULL,
	[show_date] TINYINT DEFAULT '0' NOT NULL,
	[show_pmsg] TINYINT DEFAULT '0' NOT NULL, 		-- NEW 1.65
	[show_plugin] TINYINT DEFAULT '0' NOT NULL, 	-- NEW 1.65
	[show_content] TINYINT DEFAULT '0' NOT NULL, 	-- NEW 1.65
	[order_by] TINYINT DEFAULT '0' NOT NULL, 		-- NEW 1.65
	[cssrule] VARCHAR(16)  DEFAULT '' NOT NULL,		-- NEW 1.65
	-- KEYS
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);
INSERT INTO [sections] ([order_content], [title], [seftitle], [group_by], [sec_type], [key_security], [del_key]) 
VALUES (1, 'Top-links', 'top-links', 1, 1, 'S3FIRs', 'yTp6zPsJK');


-- PAGES
CREATE TABLE [pages] (
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
	-- NEW V1.65
	[show_pmsg] TINYINT  DEFAULT '0' NOT NULL,
	[plugin_id] INTEGER  DEFAULT '0' NOT NULL,
	[content_id] INTEGER  DEFAULT '0' NOT NULL,
	-- KEYS
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);

INSERT INTO [pages] ([id],[order_content],[ptype],[lang_id],[section_id],[published],[date_reg],[date_upd],[title],[showtitle],[seftitle],[dmeta],[keywords],[texto1],[author_id],[comments],[key_security],[del_key]) VALUES
('1', '1', '0', '1', '1', '1', '2015-09-16 12:00:00', '2015-09-16 12:00:00', 'Home', '1', 'home', 'Content Management System', 'ebookcms,cms', '<p>If you are seeing this page, you have installed <strong>eBookCMS 1.69</strong> and it is connected to the database.</p><p>It is <strong>strongly</strong> suggested that you Login right away, then go to Personal Data and you will find <em>Change my Password</em>. <strong>Do it</strong> and make the password hard to guess.</p>', '1', '0', '2wpRPL', '1m8lxAW');


-- USERS
CREATE TABLE [users] (
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
	[regdate] DATE DEFAULT '2015-09-16' NOT NULL ,
	[total_timedate] VARCHAR(16)  DEFAULT '' NOT NULL,
	-- [validation_code] VARCHAR(16)  DEFAULT ''  NOT NULL,	delete and changed to vcode
	[vcode] VARCHAR(16)  DEFAULT ''  NOT NULL,
	[ask_sp] TINYINT  DEFAULT '0' NOT NULL,			-- NEW 1.65
	[special_num] VARCHAR(32) DEFAULT '' NOT NULL, 	-- NEW 1.65
	[akey] VARCHAR(64) DEFAULT '' NOT NULL,			-- NEW 1.65
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);

INSERT INTO [users] ([id],[user_active],[username],[realname], [avatar],[password],[gmember],[browser_id],
[emailreg],[email],[website],[first_login],[last_login],[last_logoff],[secretQuestion],[secretAnswer],[regdate],
[total_timedate],[vcode],[key_security],[del_key])
VALUES (1, 2, '12c1582540f906c', '12824a19b0e408411016a11507300e0b80e2141', '12824a19b0e408411016a11507300e0b80e2141', '098f6bcd4621d373cade4e832627b4f6', '1',  '651','0e70e20dc0730de05f21706d0e412c00423019b07d1250931aa27206f0d0', '', '', 1, '2015-09-16 12:00:00', '2015-09-16 12:01:00', '0e80e20dc0730e60c609427210a0840ca2170bc', '12c2302720730fa0b8', '2015-09-16', '0', 'jh2G12u', '9hafHq2', 'jkhdHj337ghs');

-- LOGS
CREATE TABLE [logs] (
	[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
	[guest_ip] VARCHAR(255)  DEFAULT '' NOT NULL,
	[browser_id] VARCHAR(40)  DEFAULT '' NOT NULL,
	[fail] TINYINT  DEFAULT '1' NOT NULL,
	[fail_date] datetime DEFAULT NULL,
	[log_date] date default NULL
);

-- COMMENTS
CREATE TABLE [comments] (
	[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
	[ptype] TINYINT  DEFAULT '0' NOT NULL,
		-- 0 page
		-- 1 plugin
	[plug_id] INTEGER  DEFAULT '0' NOT NULL,
	[pcontent_id] INTEGER  DEFAULT '0' NOT NULL,
	[reply_id] INTEGER  DEFAULT '0' NOT NULL,
	[user_id] INTEGER  DEFAULT '0' NOT NULL,
	[user_ip] VARCHAR(120)  DEFAULT '' NOT NULL,
	[name] VARCHAR(255)  NOT NULL, 
	[avatar_email] VARCHAR(255)  NOT NULL, 
	[url] VARCHAR(255)  NOT NULL, 
	[comment]  TEXT  NULL,
	[date_reg] datetime DEFAULT '2015-09-16 12:00:00' NOT NULL ,
	[date_upd] datetime DEFAULT '2015-09-16 12:00:00' NOT NULL ,
	[approved] TINYINT  DEFAULT '0' NOT NULL,
	[report_abuse] TINYINT  DEFAULT '0' NOT NULL,
	[spam] TINYINT  DEFAULT '0' NOT NULL,
	[block_user] TINYINT  DEFAULT '0' NOT NULL,
	[block_email] TINYINT  DEFAULT '0' NOT NULL,
	[archive_comment] TINYINT  DEFAULT '0' NOT NULL,
	[key_security] VARCHAR(16)  NOT NULL,
	[delkey] VARCHAR(16)  NOT NULL
);

-- PLUGINS
CREATE TABLE [plugins] (
	[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
	[title] VARCHAR(80)  DEFAULT NULL,
	[author] VARCHAR(80)  DEFAULT NULL,
	[description] VARCHAR(255)  DEFAULT NULL,
	[version] VARCHAR(8)  DEFAULT NULL,
	[auto_load] TINYINT  DEFAULT '1' NOT NULL,
	[enabled] TINYINT  DEFAULT '1' NOT NULL,
	[preinstalled] TINYINT  DEFAULT '1' NOT NULL,
	[installed] TINYINT  DEFAULT '0' NOT NULL,
	[admin_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[add_content] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[edit_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[delete_by] VARCHAR(255)  DEFAULT '01' NOT NULL,
	[public_by] VARCHAR(255)  DEFAULT '00' NOT NULL,
	[admin_func] VARCHAR(64)  DEFAULT NULL,
	[public_func] VARCHAR(64)  DEFAULT NULL,
	[sef_file] VARCHAR(64)  DEFAULT NULL,
	[filename] VARCHAR(64)  NOT NULL,
	[install_table] VARCHAR(40)  UNIQUE NOT NULL,
	[xtxt1] VARCHAR(32)  DEFAULT NULL,
	[xstr1] VARCHAR(64)  DEFAULT NULL,
	[xtxt2] VARCHAR(32)  DEFAULT NULL,
	[xint1] INTEGER  DEFAULT '0' NOT NULL,
	[xtxt3] VARCHAR(32)  DEFAULT NULL,
	[xdate1] datetime DEFAULT NULL,
	[key_security] VARCHAR(16)  NOT NULL,
	[delkey] VARCHAR(16)  NOT NULL
);

-- PERSONAL MESSAGES 1.65
CREATE TABLE [pmessages] (
	[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
	[ptype] TINYINT  DEFAULT '0' NOT NULL,
		-- 0 GUEST SEND MESSAGE TO ADMIN
	[user_id] INTEGER  DEFAULT '0' NOT NULL,
		-- 0 GUEST
	[suser_id] INTEGER  DEFAULT '1' NOT NULL,
		-- 0 Message for everybody, only Admin can delete
		-- n for specific user
	[gmember] INTEGER  DEFAULT '1' NOT NULL,
		-- 0 All groups
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
);