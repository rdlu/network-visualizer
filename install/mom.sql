-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 30, 2011 at 03:06 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mom_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `dyndata`
--

CREATE TABLE IF NOT EXISTS `dyndata` (
  `username` varchar(16) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `cellid` int(10) unsigned NOT NULL,
  `loss_down` int(10) unsigned NOT NULL,
  `loss_up` int(10) unsigned NOT NULL,
  `jitter_down` float NOT NULL,
  `jitter_up` float NOT NULL,
  `pom_down` int(10) unsigned NOT NULL,
  `pom_up` int(10) unsigned NOT NULL,
  `throughput_down` float NOT NULL,
  `throughput_up` float NOT NULL,
  `rtt` float unsigned NOT NULL,
  `throughputtcp_down` float NOT NULL,
  `throughputtcp_up` float NOT NULL,
  KEY `username` (`username`),
  KEY `cellid` (`cellid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE IF NOT EXISTS `entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `ipaddress` varchar(255) NOT NULL,
  `description` longtext,
  `added` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL,
  `type` smallint(6) NOT NULL,
  `zip` varchar(9) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `addressnum` varchar(20) DEFAULT NULL,
  `district` varchar(250) DEFAULT NULL,
  `city` varchar(250) DEFAULT NULL,
  `state` varchar(250) DEFAULT NULL,
  `latitude` varchar(10) DEFAULT NULL,
  `longitude` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entities_name_uniq` (`name`),
  UNIQUE KEY `ipUnique` (`ipaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `metrics`
--

CREATE TABLE IF NOT EXISTS `metrics` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `plugin` varchar(20) NOT NULL,
  `desc` varchar(50) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `reverse` tinyint(1) NOT NULL DEFAULT '0',
  `order` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `uniqueProfileMetric` (`id`,`profile_id`),
  KEY `profile_id` (`profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `metrics_processes`
--

CREATE TABLE IF NOT EXISTS `metrics_processes` (
  `process_id` int(3) DEFAULT NULL,
  `metric_id` int(1) DEFAULT NULL,
  `limit` int(10) DEFAULT NULL,
  UNIQUE KEY `UniqueMetricProcess` (`process_id`,`metric_id`),
  KEY `FK_metrics_processes2` (`metric_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `processes`
--

CREATE TABLE IF NOT EXISTS `processes` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `added` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `threshold_id` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UniqueProfileDestSource` (`profile_id`,`source_id`,`destination_id`),
  KEY `processes_source_id_idx` (`source_id`),
  KEY `processes_destination_id_idx` (`destination_id`),
  KEY `processes_profile_id_idx` (`profile_id`),
  KEY `FK_processes321` (`threshold_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=160 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `polling` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `probeCount` int(11) NOT NULL,
  `probeSize` int(11) NOT NULL,
  `gap` int(11) NOT NULL,
  `qosType` tinyint(1) NOT NULL,
  `qosValue` smallint(6) NOT NULL,
  `timeout` int(11) NOT NULL,
  `protocol` tinyint(1) NOT NULL DEFAULT '0',
  `description` text,
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profiles_name_uniq` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `thresholdprofiles`
--

CREATE TABLE IF NOT EXISTS `thresholdprofiles` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET latin1 NOT NULL,
  `desc` text CHARACTER SET latin1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `thresholdvalues`
--

CREATE TABLE IF NOT EXISTS `thresholdvalues` (
  `thresholdprofile_id` int(1) unsigned NOT NULL,
  `metric_id` int(1) NOT NULL,
  `min` double NOT NULL,
  `max` double NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Uniquethresmetric` (`thresholdprofile_id`,`metric_id`),
  KEY `FK_thresholdvalues` (`metric_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL,
  `last_password` char(64) DEFAULT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `last_pchange` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `metrics`
--
ALTER TABLE `metrics`
  ADD CONSTRAINT `metrics_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `metrics_processes`
--
ALTER TABLE `metrics_processes`
  ADD CONSTRAINT `FK_metrics_processes` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_metrics_processes2` FOREIGN KEY (`metric_id`) REFERENCES `metrics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `processes`
--
ALTER TABLE `processes`
  ADD CONSTRAINT `processes_ibfk_1` FOREIGN KEY (`source_id`) REFERENCES `entities` (`id`),
  ADD CONSTRAINT `processes_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `entities` (`id`),
  ADD CONSTRAINT `processes_ibfk_3` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`);

--
-- Constraints for table `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thresholdvalues`
--
ALTER TABLE `thresholdvalues`
  ADD CONSTRAINT `FK_thresholdvalues` FOREIGN KEY (`metric_id`) REFERENCES `metrics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_thresholdvalues1` FOREIGN KEY (`thresholdprofile_id`) REFERENCES `thresholdprofiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
