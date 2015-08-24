/* SQLEditor (MySQL (2))*/

CREATE TABLE Provider
(
providerId INT AUTO_INCREMENT,
providerName VARCHAR(255) UNIQUE,
providerSite VARCHAR(255),
providerAppId VARCHAR(255),
PRIMARY KEY (providerId)
);

CREATE TABLE User
(
userId INT AUTO_INCREMENT,
displayName VARCHAR(255) UNIQUE,
profilePictureURL VARCHAR(255),
providerId INT,
providerUserId VARCHAR(255),
providerAccessToken VARCHAR(255),
PRIMARY KEY (userId)
);

CREATE TABLE `Like`
(
likeId INT AUTO_INCREMENT,
likeDate DATETIME,
userId INT,
postId INT,
PRIMARY KEY (likeId)
);

CREATE TABLE Item
(
itemId INT AUTO_INCREMENT,
addedDate DATETIME,
itemName VARCHAR(255),
photoURL VARCHAR(255),
caption VARCHAR(255),
longtitude DECIMAL,
latitude DECIMAL,
userId INT,
PRIMARY KEY (itemId)
);

CREATE TABLE Comment
(
commentId INT AUTO_INCREMENT,
commentDate DATETIME,
userId INT,
postId INT,
message VARCHAR(255),
PRIMARY KEY (commentId)
);

ALTER TABLE User ADD FOREIGN KEY providerId_idxfk (providerId) REFERENCES Provider (providerId);

ALTER TABLE `Like` ADD FOREIGN KEY userId_idxfk (userId) REFERENCES User (userId);

ALTER TABLE `Like` ADD FOREIGN KEY postId_idxfk (postId) REFERENCES Item (itemId);

ALTER TABLE Item ADD FOREIGN KEY userId_idxfk_1 (userId) REFERENCES User (userId);

ALTER TABLE Comment ADD FOREIGN KEY userId_idxfk_2 (userId) REFERENCES User (userId);

ALTER TABLE Comment ADD FOREIGN KEY postId_idxfk_1 (postId) REFERENCES Item (itemId);
