-- ---------------------------------------------------------------------------
--
--					ebookcms Update sQlite (FROM 1.67) - All rights reserved
--					Copyright (c) 2015 by Rui Mendes
--					Revision: 0 
--
-- ---------------------------------------------------------------------------

-- UPDATE core_version
UPDATE [config] SET [field] = 'core_version', value = 'ebookcms_1.69' WHERE id = 1;
-- eBOOKCMS 1.69
INSERT INTO [config] VALUES  (40,2, 'uplimg_access', '01', 'h5Fds');
INSERT INTO [config] VALUES  (41,6, 'path_images', 'images', '9GGf7g');
INSERT INTO [config] VALUES  (42,6, 'thumbWidth', '100', '4ggTGNL8');
INSERT INTO [config] VALUES  (43,6, 'thumbHeight', '100', 'trs5FD');
