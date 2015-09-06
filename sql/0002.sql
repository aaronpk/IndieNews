ALTER TABLE posts
ADD COLUMN `lang` char(2) DEFAULT 'en' AFTER id;
