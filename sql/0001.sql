ALTER TABLE posts
DROP COLUMN parent_id,
CHANGE COLUMN domain post_author VARCHAR(255) DEFAULT NULL,
ADD COLUMN in_reply_to VARCHAR(512) DEFAULT NULL,
DROP COLUMN points,
DROP COLUMN score,
ADD COLUMN source_url VARCHAR(512) DEFAULT NULL;

UPDATE posts
SET source_url = href;

UPDATE posts
SET post_author = CONCAT("http://", post_author);

DROP TABLE last_computed;

ALTER TABLE users
CHANGE COLUMN domain url VARCHAR(255) DEFAULT NULL;
