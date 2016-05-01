-- ---------------------------------------------------------------------------
--
--					ebookcms Update sQlite (FROM 1.64) - All rights reserved
--					Copyright (c) 2015 by Rui Mendes
--					Revision: 0
--
-- ---------------------------------------------------------------------------

-- UPDATE core_version
UPDATE [config] SET value = 'ebookcms_1.69' WHERE id = 1;

-- CONFIG
INSERT INTO [config] VALUES  (39,3, 'pmsg_limit', '10', 'je6ggfCX');
-- eBOOKCMS 1.69
INSERT INTO [config] VALUES  (40,2, 'uplimg_access', '01', 'h5Fds');
INSERT INTO [config] VALUES  (41,6, 'path_images', 'images', '9GGf7g');
INSERT INTO [config] VALUES  (42,6, 'thumbWidth', '100', '4ggTGNL8');
INSERT INTO [config] VALUES  (43,6, 'thumbHeight', '100', 'trs5FD');

-- COPY LANGUAGES TO TEMP
ALTER TABLE [languages] RENAME TO [templng];
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
	[csymbol] VARCHAR(10) DEFAULT '&euro;' NOT NULL,
	[cdecimal] TINYINT  DEFAULT '2' NOT NULL,
	[dec_point] VARCHAR(2) DEFAULT '.' NOT NULL,
	[thousands] VARCHAR(8) DEFAULT ' ' NOT NULL,
	-- KEYS
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);
INSERT INTO [languages] ([order_content], [enabled], [title], [seftitle], [use_prefix], [prefixo], [page_id], [image_li], [charset], [key_security], [del_key])
	SELECT [order_content], [enabled], [title], [seftitle], [use_prefix], [prefixo], [page_id], [image_li], [charset], [key_security], [del_key]
FROM [templng];
	DROP TABLE IF EXISTS [templng];

-- COPY SECTIONS TO TEMP
ALTER TABLE [sections] RENAME TO [tempsec];
CREATE TABLE [sections] (
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
	-- image
	[option_img] TINYINT DEFAULT '0' NOT NULL,
	[image_folder] VARCHAR(64)  DEFAULT 'images' NOT NULL,
	-- Multi-text
	[option_txt1] TINYINT DEFAULT '1' NOT NULL,
	[option_txt2] TINYINT DEFAULT '0' NOT NULL,
	-- options
	[show_lang] TINYINT DEFAULT '0' NOT NULL,
	[show_date] TINYINT DEFAULT '0' NOT NULL,
	[show_pmsg] TINYINT DEFAULT '0' NOT NULL,
	[show_plugin] TINYINT DEFAULT '0' NOT NULL,
	[show_content] TINYINT DEFAULT '0' NOT NULL,
	[order_by] TINYINT DEFAULT '0' NOT NULL,
	[cssrule] VARCHAR(16)  DEFAULT '' NOT NULL,
	-- KEYS
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);
INSERT INTO [sections] ([order_content], [lcontent], [admin_by], [add_content], [edit_by], [delete_by], [member_by],
[group_by], [enabled], [title], [seftitle], [sec_type], [plug_id], [only_author], [csstype], [option_img], [image_folder], [option_txt1], [option_txt2], [show_lang], [show_date], [key_security], [del_key])
	SELECT [order_content], [lcontent], [admin_by], [add_content],	[edit_by], [delete_by],	[member_by], 
[group_by], [enabled], [title], [seftitle], [sec_type], [plug_id], [only_author], [csstype],[option_img], [image_folder], [option_txt1], [option_txt2], [show_lang], [show_date], [key_security], [del_key] 
FROM [tempsec];
	DROP TABLE IF EXISTS [tempsec];

-- COPY PAGES TO TEMP
ALTER TABLE [pages] RENAME TO [temppag];
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
INSERT INTO [pages] ([order_content], [ptype], [section_id],	[lang_id], [published],	[date_reg], [date_upd],	[title], 
[showtitle], [seftitle], [image], [dmeta],	[keywords], [texto1], [texto2],	[author_id], [comments], [pcsstype], [key_security], 
[del_key])
	SELECT [order_content], [ptype], [section_id],	[lang_id], [published],	[date_reg], [date_upd],	[title], [showtitle], 
[seftitle], [image], [dmeta],	[keywords], [texto1], [texto2],	[author_id], [comments], [pcsstype], [key_security], [del_key]  
FROM [temppag];
	DROP TABLE IF EXISTS [temppag];

-- COPY USERS TO TEMP
ALTER TABLE [users] RENAME TO [tempusrs];
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
	[regdate] DATE DEFAULT '2015-08-16' NOT NULL ,
	[total_timedate] VARCHAR(16)  DEFAULT '' NOT NULL,
	[vcode] VARCHAR(16)  DEFAULT ''  NOT NULL,
	[ask_sp] TINYINT  DEFAULT '0' NOT NULL,
	[special_num] VARCHAR(32) DEFAULT '' NOT NULL,
	[akey] VARCHAR(64) DEFAULT '' NOT NULL,
	[key_security] VARCHAR(16)  NOT NULL,
	[del_key] VARCHAR(16)  NOT NULL
);
INSERT INTO [users] ([user_active], [user_ip], [username], [realname], [avatar], [password], [gmember], [browser_id], [emailreg],
[email], [website], [first_login], [last_login], [last_logoff], [secretQuestion], [secretAnswer],[regdate],
[total_timedate], [vcode], [key_security], [del_key])
	SELECT [user_active], [user_ip], [username], [realname], [avatar], [password], [gmember], [browser_id], [emailreg],
[email], [website], [first_login], [last_login], [last_logoff], [secretQuestion], [secretAnswer],[regdate],
[total_timedate], [validation_code], [key_security], [del_key] 
FROM [tempusrs];
	DROP TABLE IF EXISTS [tempusrs];

-- COPY COMMENTS TO TEMP
ALTER TABLE [comments] RENAME TO [tempucmt];
CREATE TABLE [comments] (
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
);
INSERT INTO [comments] ([ptype], [plug_id], [pcontent_id], [reply_id], [user_id], [user_ip], [name], [avatar_email], [url], [comment],
	[date_reg], [date_upd], [approved], [report_abuse], [spam], [block_user], [block_email], [archive_comment],
	[key_security],	[delkey])
	SELECT [ptype], [plug_id], [pcontent_id], [reply_id], [user_id], [user_ip], [name], [avatar_email], [url], [comment],
	[date_reg], [date_upd], [approved], [report_abuse], [spam], [block_user], [block_email], [archive_comment],
	[key_security],	[delkey] FROM [tempucmt];
	DROP TABLE IF EXISTS [tempucmt];


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