ALTER TABLE posts
ADD COLUMN `tzoffset` int(11) NOT NULL DEFAULT 0 AFTER post_date;
