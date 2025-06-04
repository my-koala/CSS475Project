-- Table Structures

DROP TABLE IF EXISTS Users;
CREATE TABLE IF NOT EXISTS Users (
  user_id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(40),
  pass_hash VARCHAR(64),
  birthdate DATE,
  email VARCHAR(320),
  join_date DATE,
  private_acc BOOLEAN NOT NULL CHECK (private_acc IN (0, 1)),
  display_name VARCHAR(40),
  profile_photo_id INT,
  PRIMARY KEY (user_id)
);

DROP TABLE IF EXISTS UserIPs;
CREATE TABLE IF NOT EXISTS UserIPs (
  user_id INT NOT NULL,
  ip VARCHAR(16),
  PRIMARY KEY (user_id, ip)
);

DROP TABLE IF EXISTS UserBlocks;
CREATE TABLE IF NOT EXISTS UserBlocks (
  blocker_id INT NOT NULL,
  blockee_id INT NOT NULL,
  PRIMARY KEY (blocker_id, blockee_id)
);

DROP TABLE IF EXISTS UserFollows;
CREATE TABLE IF NOT EXISTS UserFollows (
  follower_id INT NOT NULL,
  followee_id INT NOT NULL,
  PRIMARY KEY (follower_id, followee_id)
);

DROP TABLE IF EXISTS Subscriptions;
CREATE TABLE IF NOT EXISTS Subscriptions (
  subscription_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  plan VARCHAR(16) NOT NULL CHECK (plan IN ('premium', 'marketing', 'admin')),
  date_start DATETIME,
  date_end DATETIME,
  CONSTRAINT CHECK (date_start <= date_end),
  PRIMARY KEY (subscription_id)
);

DROP TABLE IF EXISTS Bans;
CREATE TABLE IF NOT EXISTS Bans (
  ban_id INT NOT NULL AUTO_INCREMENT,
  reason VARCHAR(256),
  user_id INT,
  ban_start DATETIME,
  ban_end DATETIME,
  CONSTRAINT CHECK (ban_start <= ban_end),
  PRIMARY KEY (ban_id)
);

DROP TABLE IF EXISTS Photos;
CREATE TABLE IF NOT EXISTS Photos (
  photo_id INT NOT NULL AUTO_INCREMENT,
  user_id INT,
  resolution_x INT CHECK (resolution_x > 0),
  resolution_y INT CHECK (resolution_y > 0),
  camera_model VARCHAR(64),
  camera_manufacturer VARCHAR(64),
  image_format VARCHAR(16),
  image_description VARCHAR(256),
  image_path VARCHAR(256),
  aperture VARCHAR(6),
  shutter_speed VARCHAR(16),
  iso INT,
  focal_length VARCHAR(6),
  geolocation VARCHAR(256),
  PRIMARY KEY (photo_id)
);

DROP TABLE IF EXISTS Thumbnails;
CREATE TABLE IF NOT EXISTS Thumbnails (
  photo_id INT NOT NULL,
  resolution_x INT NOT NULL CHECK (resolution_x > 0),
  resolution_y INT NOT NULL CHECK (resolution_y > 0),
  image_path VARCHAR(1024),
  image_format VARCHAR(64),
  PRIMARY KEY (photo_id, resolution_x, resolution_y)
);

DROP TABLE IF EXISTS CameraModels;
CREATE TABLE IF NOT EXISTS CameraModels (
  camera_model VARCHAR(64) NOT NULL,
  camera_manufacturer VARCHAR(64) NOT NULL,
  device VARCHAR(64),
  PRIMARY KEY (camera_model, camera_manufacturer)
);

DROP TABLE IF EXISTS PhotoUserTags;
CREATE TABLE IF NOT EXISTS PhotoUserTags (
  photo_id INT NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (photo_id, user_id)
);

DROP TABLE IF EXISTS Tags;
CREATE TABLE IF NOT EXISTS Tags (
  tag_name VARCHAR(32) NOT NULL,
  PRIMARY KEY (tag_name)
);

DROP TABLE IF EXISTS Posts;
CREATE TABLE IF NOT EXISTS Posts (
  post_id INT NOT NULL AUTO_INCREMENT,
  user_id INT,
  time_stamp DATETIME,
  ctr DECIMAL(3, 2),
  post_text VARCHAR(10000),
  PRIMARY KEY (post_id)
);

DROP TABLE IF EXISTS PostPhotos;
CREATE TABLE IF NOT EXISTS PostPhotos (
  post_id INT NOT NULL,
  photo_id INT NOT NULL,
  PRIMARY KEY (post_id, photo_id)
);

DROP TABLE IF EXISTS PostTags;
CREATE TABLE IF NOT EXISTS PostTags (
  post_id INT NOT NULL,
  tag_name VARCHAR(32) NOT NULL,
  PRIMARY KEY (post_id, tag_name)
);

DROP TABLE IF EXISTS PostLikes;
CREATE TABLE IF NOT EXISTS PostLikes (
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  like_timestamp DATETIME,
  PRIMARY KEY (post_id, user_id)
);

DROP TABLE IF EXISTS PostComments;
CREATE TABLE IF NOT EXISTS PostComments (
  comment_id INT NOT NULL,
  post_id INT NOT NULL,
  user_id INT,
  comment_timestamp DATETIME,
  comment_text VARCHAR(1024),
  PRIMARY KEY (comment_id)
);

DROP TABLE IF EXISTS PostCommentLikes;
CREATE TABLE IF NOT EXISTS PostCommentLikes (
  comment_id INT NOT NULL,
  user_id INT NOT NULL,
  like_timestamp DATETIME,
  PRIMARY KEY (comment_id, user_id)
);

DROP TABLE IF EXISTS Collections;
CREATE TABLE IF NOT EXISTS Collections (
  collection_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  title VARCHAR(32),
  collection_description VARCHAR(512),
  PRIMARY KEY (collection_id)
);

DROP TABLE IF EXISTS CollectionPosts;
CREATE TABLE IF NOT EXISTS CollectionPosts (
  collection_id INT NOT NULL,
  post_id INT NOT NULL,
  post_description VARCHAR(10000),
  PRIMARY KEY (collection_id, post_id)
);

DROP TABLE IF EXISTS Campaigns;
CREATE TABLE IF NOT EXISTS Campaigns (
  campaign_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  title VARCHAR(32),
  campaign_start DATE,
  campaign_end DATE,
  PRIMARY KEY (campaign_id)
);

DROP TABLE IF EXISTS CampaignPosts;
CREATE TABLE IF NOT EXISTS CampaignPosts (
  campaign_id INT NOT NULL,
  post_id INT NOT NULL,
  PRIMARY KEY (campaign_id, post_id)
);

-- Table Values

INSERT INTO Users (user_id, username, pass_hash, birthdate, email, join_date, private_acc, display_name, profile_photo_id) VALUES
(1, 'tim_huynh', 'fa39223abacabf18c7732bcee269d084895286162186ed574ec169e47c5cb3b9', '2004-08-23', 'timhuynh@outlook.com', '2025-04-20', 1, 'master_splinter', 1),
(2, 'james', '0f7e5a8fb3f726be65110cb51bae58ba9f7227ec7ec0526a5b1087060784edb0', '1909-09-09', 'jbruse@uw.edu', '2025-05-01', 0, 'my_koala', 5),
(3, 'mario', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', '2002-07-18', 'houming@uw.edu', '2025-05-01', 1, 'mario', 2),
(4, 'yousuf', '782afecf90a8dc95ad44ad31f099a3c5237e0afe3ca2ff009d97cae053a2437d', '2006-07-12', 'yalbass@uw.edu', '2025-05-01', 1, 'bassuni', 4),
(5, 'anoop', '632f42619b422a30c850a2d098e7e8431c321cab28a364b0b1cb90f1cb765058', '2004-03-14', 'anoopp@uw.edu', '2025-05-01', 0, 'ap!', 3),
(6, 'elon_musk', 'cebe3d9d614ba5c19f633566104315854a11353a333bf96f16b5afa0e90abdc4', '1971-06-28', 'elonm@gmail.com', '2025-05-10', 1, 'madatgascar', NULL),
(7, 'barack_obama', 'fa88d374b9cf5e059fad4a2fe406feae4c49cbf4803083ec521d3c75ee22557c', '1961-08-04', 'barack_obama@gmail.com', '2025-05-12', 0, 'myfellowamericans', NULL);

INSERT INTO UserIPs (user_id, ip) VALUES
(1, '192.84.133.219'),
(2, '10.237.65.172'),
(3, '172.46.201.89'),
(4, '8.123.47.250'),
(5, '203.91.7.166'),
(6, '191.9.4.2.195'),
(7, '132.1.5.7.42');

INSERT INTO UserBlocks (blocker_id, blockee_id) VALUES
(2, 1),
(3, 1),
(6, 1),
(4, 1),
(7, 1),
(7, 3),
(3, 5),
(2, 6),
(7, 6);

INSERT INTO UserFollows (follower_id, followee_id) VALUES
(3, 6),
(1, 2),
(4, 2),
(5, 2),
(4, 7),
(5, 7),
(5, 3),
(2, 3);

INSERT INTO Subscriptions (subscription_id, user_id, plan, date_start, date_end) VALUES
(1, 2, 'admin', '2025-05-01', NULL),
(2, 3, 'admin', '2025-05-01', NULL),
(3, 4, 'admin', '2025-05-01', NULL),
(4, 5, 'admin', '2025-05-01', NULL),
(5, 2, 'premium', '2025-05-11', '2025-06-11'),
(6, 6, 'marketing', '2025-05-13', NULL),
(7, 7, 'premium', '2025-06-03', '2025-07-03'),
(8, 6, 'premium', '2025-06-03', '2026-06-03');

INSERT INTO Bans (ban_id, user_id, reason, ban_start, ban_end) VALUES
(1, 1, 'explicit language', '2025-05-15 07:15:38', '2025-05-16 00:00:00'),
(2, 1, 'harassment', '2025-05-16 15:59:23', '2025-06-16 00:00:00'),
(4, 5, 'illegal', '2025-05-20 20:05:03', '2025-06-20 00:00:00'),
(5, 2, 'stalking', '2025-06-01 00:17:48', '2035-06-01 00:00:00');

INSERT INTO Photos (photo_id, user_id, resolution_x, resolution_y, camera_model, camera_manufacturer, image_format, image_description, image_path, aperture, shutter_speed, iso, focal_length, geolocation) VALUES
(1, 1, 6000, 4000, 'G7 X', 'Canon', 'JPEG', 'selfie of tim', 'images/0.jpg', 'f/2.0', '1/250', 100, '22mm', 'Portland, United States'),
(2, 3, 6000, 4000, 'D3500', 'Nikon', 'png', 'roses closeup', 'images/1.jpg', 'f/5.6', '1/200', 100, '300mm', 'Queenstown, New Zealand'),
(3, 5, 6000, 4000, 'iPhone 15 Pro', 'Apple', 'HEIC', 'cherry blossoms', 'images/2.jpg', 'f/4.0', '1/100', 100, '85mm', 'Tokyo, Japan'),
(4, 4, 6000, 4000, 'X-T4', 'Fujifilm', 'JPEG', 'goose closeup', 'images/3.jpg', 'f/4.0', '1/400', 100, '85mm', 'Vancouver, Canada'),
(5, 2, 4000, 6000, 'a6700', 'Sony', 'png', 'cat portrait', 'images/4.jpg', 'f/1.8', '1/320', 640, '85mm', 'Mill Creek, United States'),
(6, 7, 5900, 2190, 'EOS R10', 'Canon', 'png', 'aurora borealis', 'images/5.jpg', 'f/2.0', '10', 100, '22mm', 'Bothell, United States');

INSERT INTO Thumbnails (photo_id, resolution_x, resolution_y, image_path, image_format) VALUES
(1, 640, 480, 'images/0_640x480.jpg', 'image_format'),
(1, 1024, 768, 'images/0_1024x720.jpg', 'image_format'),
(2, 640, 480, 'images/1_640x480.jpg', 'image_format'),
(2, 1024, 768, 'images/1_1024x720.jpg', 'image_format'),
(3, 640, 480, 'images/2_640x480.jpg', 'image_format'),
(3, 1024, 768, 'images/2_1024x720.jpg', 'image_format'),
(4, 640, 480, 'images/3_640x480.jpg', 'image_format'),
(4, 1024, 768, 'images/3_1024x720.jpg', 'image_format'),
(5, 640, 480, 'images/4_640x480.jpg', 'image_format'),
(5, 1024, 768, 'images/4_1024x720.jpg', 'image_format'),
(6, 640, 480, 'images/5_640x480.jpg', 'image_format'),
(6, 1024, 768, 'images/5_1024x720.jpg', 'image_format');

INSERT INTO CameraModels (camera_model, camera_manufacturer, device) VALUES
('G7 X', 'Canon', 'point_shoot'),
('D3500', 'Nikon', 'dslr'),
('iPhone 15 Pro', 'Apple', 'smartphone'),
('a6700', 'Sony', 'mirrorless'),
('X-T4', 'Fujifilm', 'mirrorless'),
('EOS R10', 'Canon', 'mirrorless');

INSERT INTO PhotoUserTags (photo_id, user_id) VALUES
(1, 1),
(4, 3);

INSERT INTO Tags (tag_name) VALUES
('nature'),
('selfie'),
('animals'),
('telephoto'),
('wideangle');

INSERT INTO Posts (post_id, user_id, time_stamp, ctr, post_text) VALUES
(1, 1, '2025-05-03 09:00:00', 0.01, 'Have you ever wondered how to take great selfies? Try using a wide-angle lens and finding dramatic lighting. Find a colorful background! Use a tripod to keep the camera steady. Have fun!'),
(2, 4, '2025-05-06 11:00:00', 0.25, 'Flowers are a beautiful subject during springtime. Try using a wide aperture and getting close to the flower to focus on small parts of it. Try using a fast shutter speed to avoid shaking from the wind. Get creative with color grading! Remember to take care of your local plants so we can continue to enjoy them year after year.'),
(3, 5, '2025-05-10 10:00:00', 0.50, 'Honk honk! Taking pictures of wild animals can be challenging. Use telephoto lenses to capture detailed images without startling them, and be sure to use a fast shutter speed so you can capture sharp images as they move about.'),
(4, 4, '2025-05-12 22:00:00', 0.32, 'It’s always fun to take pictures of your furry friends, but how can you make them stand out? Try using similar techniques to human portraiture, but with faster shutter speeds to counter their movement. Be patient and wait for the perfect shot that shows off what you love about them. Many pets like this one are waiting to find their forever photographer, too. Adopt today!'),
(5, 7, '2025-05-14 06:00:00', 0.12, 'The Northern Lights are a rare and beautiful phenomenon. Be prepared to take pictures of them when they come around next. Use a wide aperture and long exposure to get in as much light as you can. You can turn up the ISO if needed, but that can make the image look grainy. Use a wide angle lens to capture as much of the aurora as you can. Good luck!');

INSERT INTO PostPhotos (post_id, photo_id) VALUES
(1, 1),
(2, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6);

INSERT INTO PostTags (post_id, tag_name) VALUES
(1, 'selfie'),
(1, 'wideangle'),
(2, 'nature'),
(3, 'animals'),
(3, 'telephoto'),
(3, 'nature'),
(4, 'animals'),
(4, 'telephoto'),
(5, 'nature'),
(5, 'wideangle'); 

INSERT INTO PostLikes (post_id, user_id, like_timestamp) VALUES
(1, 2, '2025-05-04 12:01:52'),
(1, 4, '2025-05-07 17:10:30'),
(2, 4, '2025-05-07 01:55:12'),
(2, 2, '2025-05-15 05:01:33'),
(4, 2, '2025-05-15 05:12:32'),
(5, 2, '2025-05-15 05:59:59'),
(1, 3, '2025-05-15 06:13:42'),
(4, 1, '2025-05-23 18:47:03'),
(5, 3, '2025-06-01 15:36:00'),
(5, 5, '2025-06-02 13:27:23');

INSERT INTO PostComments (comment_id, post_id, user_id, comment_timestamp, comment_text) VALUES
(1, 2, 3, '2025-05-07 13:33:37', 'WOW amazing!'),
(2, 1, 5, '2025-05-07 15:02:58', 'nice selfie'),
(3, 1, 2, '2025-05-09 01:00:32', 'leave some pictures for the rest of us'),
(4, 4, 3, '2025-05-12 12:08:20', 'so what are the tips and tricks?'),
(5, 5, 4, '2025-05-14 23:45:02', 'THIS IS ART!!'),
(6, 4, 7, '2025-05-25 10:23:14', 'photography at its finest');

INSERT INTO PostCommentLikes (comment_id, user_id, like_timestamp) VALUES
(1, 2, '2025-05-08 12:01:52'),
(2, 5, '2025-05-09 17:10:30'),
(2, 1, '2025-05-09 01:55:12'),
(4, 3, '2025-05-14 03:33:33'),
(1, 3, '2025-05-15 05:12:32'),
(2, 4, '2025-05-21 23:59:59'),
(4, 6, '2025-06-01 21:02:42');

INSERT INTO Collections (collection_id, user_id, title, collection_description) VALUES
(1, 6, 'Animals', 'Several illustrative examples on animal photography.'),
(2, 3, 'Closeups', 'Tutorial on taking closeups of various subjects.'),
(3, 5, 'Wide Angle', 'Love wide angle lenses? Learn some techniques for using them!');

INSERT INTO CollectionPosts (collection_id, post_id, post_description) VALUES
(1, 3, 'This post features a great example of how to take good pictures of wild animals using telephoto lenses.'),
(1, 4, 'Here, we can see how patience is key when taking pictures of animals. Wait for the critical moment!'),
(2, 2, 'Macro shots can be tricky, but they’re a great tool for closeups. Try practicing on small subjects, like flowers.'),
(2, 3, 'Telephoto lenses are an alternative method of taking closeups without having to get as physically close to the subject.'),
(3, 1, 'Wide angle lenses are a good choice for selfies when you want to get as much of the background in the image as you can.'),
(3, 5, 'When you’re trying to capture a wide area like a landscape or the sky, wide angle lenses are the way to go.');

INSERT INTO Campaigns (campaign_id, user_id, title, campaign_start, campaign_end) VALUES
(1, 6, 'Nature Conservation', '2025-05-27', '2025-06-27'),
(2, 6, 'Adopt a Pet', '2025-05-28', '2026-01-01');

INSERT INTO CampaignPosts (campaign_id, post_id) VALUES
(1, 2),
(1, 4);

-- Table Foreign Keys

ALTER TABLE Users ADD FOREIGN KEY (profile_photo_id) REFERENCES Photos(photo_id);

ALTER TABLE UserIPs ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE UserBlocks ADD FOREIGN KEY (blocker_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE UserBlocks ADD FOREIGN KEY (blockee_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE UserFollows ADD FOREIGN KEY (follower_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE UserFollows ADD FOREIGN KEY (followee_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Subscriptions ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Bans ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Photos ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
ALTER TABLE Photos ADD FOREIGN KEY (camera_model, camera_manufacturer) REFERENCES CameraModels(camera_model, camera_manufacturer)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE Thumbnails ADD FOREIGN KEY (photo_id) REFERENCES Photos(photo_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE PhotoUserTags ADD FOREIGN KEY (photo_id) REFERENCES Photos(photo_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PhotoUserTags ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Posts ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE PostPhotos ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PostPhotos ADD FOREIGN KEY (photo_id) REFERENCES Photos(photo_id)
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

ALTER TABLE PostComments ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PostComments ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE PostCommentLikes ADD FOREIGN KEY (comment_id) REFERENCES PostComments(comment_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE PostCommentLikes ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Collections ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE CollectionPosts ADD FOREIGN KEY (collection_id) REFERENCES Collections(collection_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE CollectionPosts ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE Campaigns ADD FOREIGN KEY (user_id) REFERENCES Users(user_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE CampaignPosts ADD FOREIGN KEY (campaign_id) REFERENCES Campaigns(campaign_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE CampaignPosts ADD FOREIGN KEY (post_id) REFERENCES Posts(post_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
