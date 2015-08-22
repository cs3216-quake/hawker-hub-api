-- phpMyAdmin SQL Dump
-- version 4.4.13.1
-- http://www.phpmyadmin.net
--
-- Host: 192.168.59.103:3307
-- Generation Time: Aug 20, 2015 at 08:15 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hawker-hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `Approve`
--

CREATE TABLE IF NOT EXISTS `Approve` (
  `likeId` int(11) NOT NULL,
  `likeDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int(11) NOT NULL,
  `itemId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `commentId` int(11) NOT NULL,
  `commentDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `message` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `Item`
--

CREATE TABLE IF NOT EXISTS `Item` (
  `itemId` int(11) NOT NULL,
  `addedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `itemName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `photoURL` varchar(255) COLLATE utf8_bin NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `longtitude` decimal(10,6) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Provider`
--

CREATE TABLE IF NOT EXISTS `Provider` (
  `providerId` int(11) NOT NULL,
  `providerName` varchar(255) COLLATE utf8_bin NOT NULL,
  `providerSite` varchar(255) COLLATE utf8_bin NOT NULL,
  `providerAppId` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `userId` int(11) NOT NULL,
  `displayName` varchar(255) COLLATE utf8_bin NOT NULL,
  `profilePictureURL` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `providerId` int(11) DEFAULT NULL,
  `providerUserId` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Approve`
--
ALTER TABLE `Approve`
  ADD PRIMARY KEY (`likeId`),
  ADD KEY `userId_idxfk` (`userId`),
  ADD KEY `itemId_idxfk` (`itemId`);

--
-- Indexes for table `Comment`
--
ALTER TABLE `Comment`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `userId_idxfk_2` (`userId`),
  ADD KEY `itemId_idxfk_1` (`itemId`);

--
-- Indexes for table `Item`
--
ALTER TABLE `Item`
  ADD PRIMARY KEY (`itemId`),
  ADD KEY `userId_idxfk_1` (`userId`);

--
-- Indexes for table `Provider`
--
ALTER TABLE `Provider`
  ADD PRIMARY KEY (`providerId`),
  ADD UNIQUE KEY `providerName` (`providerName`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `displayName` (`displayName`),
  ADD UNIQUE KEY `providerUserId` (`providerUserId`),
  ADD KEY `providerId_idxfk` (`providerId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Approve`
--
ALTER TABLE `Approve`
  MODIFY `likeId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Comment`
--
ALTER TABLE `Comment`
  MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Item`
--
ALTER TABLE `Item`
  MODIFY `itemId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Provider`
--
ALTER TABLE `Provider`
  MODIFY `providerId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Approve`
--
ALTER TABLE `Approve`
  ADD CONSTRAINT `Approve_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `Approve_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `Item` (`itemId`);

--
-- Constraints for table `Comment`
--
ALTER TABLE `Comment`
  ADD CONSTRAINT `Comment_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `Comment_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `Item` (`itemId`);

--
-- Constraints for table `Item`
--
ALTER TABLE `Item`
  ADD CONSTRAINT `Item_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`);

--
-- Constraints for table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`providerId`) REFERENCES `Provider` (`providerId`);
