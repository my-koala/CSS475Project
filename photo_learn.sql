-- Table Structures

DROP TABLE IF EXISTS Users;
CREATE TABLE IF NOT EXISTS Users (
  user_id INT NOT NULL,
  username VARCHAR(40),
  pass_hash VARCHAR(64),
  user_type VARCHAR(10) CHECK (user_type IN ('free', 'premium', 'admin', 'marketing')),
  birthdate DATE,
  email VARCHAR(320),
  join_date DATE,
  experience_level INT,
  private_acc BOOLEAN NOT NULL CHECK (private_acc IN (0, 1)),
  last_ip VARCHAR(16),
  display_name VARCHAR(40),
  profile_photo_id INT
);

DROP TABLE IF EXISTS Subscriptions;
CREATE TABLE IF NOT EXISTS Subscriptions (
  user_id INT NOT NULL,
  date_start DATETIME,
  date_end DATETIME,
  CONSTRAINT CHECK (date_start <= date_end)
);

DROP TABLE IF EXISTS Posts;
CREATE TABLE IF NOT EXISTS Posts (
  post_id INT NOT NULL,
  user_id INT,
  time_stamp DATETIME,
  ctr DECIMAL(3, 2),
  post_text VARCHAR(10000)
);

DROP TABLE IF EXISTS Photos;
CREATE TABLE IF NOT EXISTS Photos (
  photo_id INT NOT NULL,
  user_id INT,
  resolution_x INT CHECK (resolution_x > 0),
  resolution_y INT CHECK (resolution_y > 0),
  camera_model VARCHAR(50),
  image_format VARCHAR(50),
  image_description VARCHAR(256),
  aperture VARCHAR(6),
  shutter_speed VARCHAR(10),
  iso INT,
  focal_length VARCHAR(6),
  geolocation VARCHAR(400)
);

DROP TABLE IF EXISTS CameraModels;
CREATE TABLE IF NOT EXISTS CameraModels (
  camera_model VARCHAR(64) NOT NULL,
  manufacturer VARCHAR(64),
  device VARCHAR(64)
);

DROP TABLE IF EXISTS Bans;
CREATE TABLE IF NOT EXISTS Bans (
  ban_id INT NOT NULL DEFAULT 0,
  reason VARCHAR(256),
  user_id INT,
  ban_start DATE,
  ban_end DATE,
  CONSTRAINT CHECK (ban_start <= ban_end)
);

DROP TABLE IF EXISTS Collections;
CREATE TABLE IF NOT EXISTS Collections (
  collection_id INT NOT NULL DEFAULT 0,
  title VARCHAR(30),
  collection_description VARCHAR(512),
  user_id INT
);

DROP TABLE IF EXISTS Tags;
CREATE TABLE IF NOT EXISTS Tags (
  tag_name VARCHAR(32) NOT NULL
);

CREATE TABLE Campaigns (
  campaign_id INT NOT NULL,
  title VARCHAR(30),
  campaign_start DATE,
  campaign_end DATE,
  user_id INT
);

CREATE TABLE Comments (
  comment_id INT NOT NULL,
  post_id INT NOT NULL,
  comment_timestamp DATETIME,
  comment_text VARCHAR(1024),
  user_id INT
);

CREATE TABLE Thumbnails (
  resolution_x INT NOT NULL CHECK (resolution_x > 0),
  resolution_y INT NOT NULL CHECK (resolution_y > 0),
  photo_id INT NOT NULL,
  image_path VARCHAR(1024),
  image_format VARCHAR(64)
);

CREATE TABLE PostTags (
  post_id INT NOT NULL,
  tag_name VARCHAR(32) NOT NULL
);

CREATE TABLE PostLikes (
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  like_timestamp DATETIME
);

CREATE TABLE CommentLikes (
  comment_id INT NOT NULL,
  user_id INT NOT NULL,
  like_timestamp DATETIME
);

CREATE TABLE PhotoUserTags (
  photo_id INT NOT NULL,
  user_id INT NOT NULL
);

CREATE TABLE PostPhotos (
  post_id INT NOT NULL,
  photo_id INT NOT NULL
);

CREATE TABLE CampaignPosts (
  campaign_id INT NOT NULL,
  post_id INT NOT NULL
);

CREATE TABLE Blocks (
  blocker_id INT NOT NULL,
  blockee_id INT NOT NULL
);

CREATE TABLE Follows (
  follower_id INT NOT NULL,
  followee_id INT NOT NULL
);

CREATE TABLE CollectionPosts (
  collection_id INT NOT NULL,
  post_id INT NOT NULL,
  post_description VARCHAR(10000)
);

-- Table Indexes

ALTER TABLE Users ADD PRIMARY KEY (user_id);

ALTER TABLE Subscriptions ADD PRIMARY KEY (user_id);

ALTER TABLE Posts ADD PRIMARY KEY (post_id);

ALTER TABLE Photos ADD PRIMARY KEY (photo_id);

ALTER TABLE CameraModels ADD PRIMARY KEY (camera_model);

ALTER TABLE Bans ADD PRIMARY KEY (ban_id);

ALTER TABLE Collections ADD PRIMARY KEY (collection_id);

ALTER TABLE Tags ADD PRIMARY KEY (tag_name);

ALTER TABLE Campaigns ADD PRIMARY KEY (campaign_id);

ALTER TABLE Comments ADD PRIMARY KEY (comment_id, post_id);

ALTER TABLE Thumbnails ADD PRIMARY KEY (photo_id, resolution_x, resolution_y);

ALTER TABLE PostTags ADD PRIMARY KEY (post_id, tag_name);

ALTER TABLE PostLikes ADD PRIMARY KEY (post_id, user_id);

ALTER TABLE CommentLikes ADD PRIMARY KEY (comment_id, user_id);

ALTER TABLE PhotoUserTags ADD PRIMARY KEY (photo_id, user_id);

ALTER TABLE PostPhotos ADD PRIMARY KEY (post_id, photo_id);

ALTER TABLE CampaignPosts ADD PRIMARY KEY (campaign_id, post_id);

ALTER TABLE Blocks ADD PRIMARY KEY (blocker_id, blockee_id);

ALTER TABLE Follows ADD PRIMARY KEY (follower_id, followee_id);

ALTER TABLE CollectionPosts ADD PRIMARY KEY (collection_id, post_id);

-- Table Constraints

ALTER TABLE Users ADD FOREIGN KEY (profile_photo_id) REFERENCES Photos(photo_id);

ALTER TABLE Subscriptions ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Posts ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Photos ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE Bans ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Collections ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Campaigns ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Comments ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE Comments ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE Thumbnails ADD FOREIGN KEY (photo_id) REFERENCES Photos(photo_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE PostTags ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PostTags ADD FOREIGN KEY (tag_name) REFERENCES Tags(tag_name)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE PostLikes ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PostLikes ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE CommentLikes ADD FOREIGN KEY (comment_id) REFERENCES Comments(comment_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE CommentLikes ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE PhotoUserTags ADD FOREIGN KEY (photo_id) REFERENCES Photos(photo_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PhotoUserTags ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE PostPhotos ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PostPhotos ADD FOREIGN KEY (photo_id) REFERENCES Photos(photo_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE CampaignPosts ADD FOREIGN KEY (campaign_id) REFERENCES Campaigns(campaign_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE CampaignPosts ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Blocks ADD FOREIGN KEY (blocker_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE Blocks ADD FOREIGN KEY (blockee_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Follows ADD FOREIGN KEY (follower_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE Follows ADD FOREIGN KEY (followee_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE CollectionPosts ADD FOREIGN KEY (collection_id) REFERENCES Collections(collection_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE CollectionPosts ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
