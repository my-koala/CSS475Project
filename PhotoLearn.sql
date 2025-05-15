CREATE TABLE Users (
  user_id INT PRIMARY KEY,
  username VARCHAR(40),
  pass_hash VARCHAR(64),
  sub_plan BOOLEAN NOT NULL CHECK (sub_plan IN (0, 1)),
  sub_start DATE,
  sub_end DATE,
  user_type VARCHAR(10) CHECK (user_type IN ('free', 'premium', 'admin', 'marketing')),
  birthdate DATE,
  email VARCHAR(320), 
  join_date DATE,
  experience_level INT,
  private_acc BOOLEAN NOT NULL CHECK (private_acc IN (0, 1)),
  last_ip VARCHAR(16),
  display_name VARCHAR(40),
  profile_photo_id INT,
  FOREIGN KEY (profile_photo_id) REFERENCES Photos(photo_id)
);

CREATE TABLE Posts (
  post_id INT PRIMARY KEY,
  time_stamp DATE,
  click_through_rate DECIMAL(3, 2),
  impressions INT,
  post_text VARCHAR(30000),
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Photos (
  photo_id INT PRIMARY KEY,
  device_type VARCHAR(50),
  manufacturer VARCHAR(50),
  model VARCHAR(50),
  image_format VARCHAR(50),
  image_description VARCHAR(256),
  aperture VARCHAR(6),
  shutter_speed VARCHAR(10),
  iso INT,
  focal_length VARCHAR(6),
  geolocation VARCHAR(400),
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Bans (
  ban_id INT PRIMARY KEY,
  last_ip VARCHAR(16),
  reason VARCHAR(256),
  ban_start DATE,
  ban_end DATE,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Collections (
  collection_id INT PRIMARY KEY,
  title VARCHAR(30),
  collection_description VARCHAR(512),
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Tags (
  tag_name VARCHAR(30) PRIMARY KEY
);

CREATE TABLE Campaigns (
  campaign_id INT PRIMARY KEY,
  title VARCHAR(30),
  campaign_start DATE,
  campaign_end DATE,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Comments (
  comment_id INT NOT NULL,
  post_id INT NOT NULL,
  comment_timestamp DATE,
  comment_text VARCHAR(1024),
  user_id INT,
  FOREIGN KEY (post_id) REFERENCES Posts(post_id),
  FOREIGN KEY (user_id) REFERENCES Users(user_id),
  PRIMARY KEY (comment_id, post_id)
);

CREATE TABLE Thumbnails (
  resolution VARCHAR(16) NOT NULL,
  photo_id INT NOT NULL,
  image_path VARCHAR(256),
  image_format VARCHAR(50),
  FOREIGN KEY (photo_id) REFERENCES Photos(photo_id),
  PRIMARY KEY (resolution, photo_id)
);

CREATE TABLE PostTags (
  post_id INT NOT NULL,
  tag_name VARCHAR(30) NOT NULL,
  FOREIGN KEY (post_id) REFERENCES Posts(post_id),
  FOREIGN KEY (tag_name) REFERENCES Tags(tag_name),
  PRIMARY KEY (post_id, tag_name)
);

CREATE TABLE PostLikes (
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (post_id) REFERENCES Posts(post_id),
  FOREIGN KEY (user_id) REFERENCES Users(user_id),
  PRIMARY KEY (post_id, user_id)
);

CREATE TABLE CommentLikes (
  comment_id INT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (comment_id) REFERENCES Posts(comment_id),
  FOREIGN KEY (user_id) REFERENCES Users(user_id),
  PRIMARY KEY (comment_id, user_id)
);

CREATE TABLE PhotoUserTags (
  photo_id INT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (photo_id) REFERENCES Photos(photo_id),
  FOREIGN KEY (user_id) REFERENCES Users(user_id),
  PRIMARY KEY (photo_id, user_id)
);

CREATE TABLE PostPhotos (
  post_id INT NOT NULL,
  photo_id INT NOT NULL,
  FOREIGN KEY (post_id) REFERENCES Posts(post_id),
  FOREIGN KEY (photo_id) REFERENCES Photos(photo_id),
  PRIMARY KEY (post_id, photo_id)
);

CREATE TABLE CampaignPosts (
  campaign_id INT NOT NULL,
  post_id INT NOT NULL,
  FOREIGN KEY (campaign_id) REFERENCES Campaigns(campaign_id),
  FOREIGN KEY (post_id) REFERENCES Posts(post_id),
  PRIMARY KEY (campaign_id, post_id)
);

CREATE TABLE Blocks (
  blocker_id INT NOT NULL,
  blockee_id INT NOT NULL,
  FOREIGN KEY (blocker_id) REFERENCES Users(blocker_id),
  FOREIGN KEY (blockee_id) REFERENCES Users(blockee_id),
  PRIMARY KEY (blocker_id, blockee_id)
);

CREATE TABLE Follows (
  follower_id INT NOT NULL,
  followee_id INT NOT NULL,
  FOREIGN KEY (follower_id) REFERENCES Users(follower_id),
  FOREIGN KEY (followee_id) REFERENCES Users(followee_id),
  PRIMARY KEY (follower_id, followee_id)
);

CREATE TABLE CollectionPosts (
  collection_id INT NOT NULL,
  post_id INT NOT NULL,
  post_description VARCHAR(10000),
  FOREIGN KEY (collection_id) REFERENCES Collections(collection_id),
  FOREIGN KEY (post_id) REFERENCES Posts(post_id),
  PRIMARY KEY (collection_id, post_id)
);