-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2011 at 11:39 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.3

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
-- Table structure for table `bairros`
--

CREATE TABLE IF NOT EXISTS `bairros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cidade_id` int(11) DEFAULT NULL,
  `nome` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bairros_cidade_id_idx` (`cidade_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49898 ;

-- --------------------------------------------------------

--
-- Table structure for table `cidades`
--

CREATE TABLE IF NOT EXISTS `cidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uf_id` int(11) DEFAULT NULL,
  `nome` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cidades_uf_id_idx` (`uf_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11241 ;

-- --------------------------------------------------------

--
-- Table structure for table `logradouros`
--

CREATE TABLE IF NOT EXISTS `logradouros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bairro_id` int(11) DEFAULT NULL,
  `nome` varchar(250) NOT NULL,
  `cep` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `logradouros_bairro_id_idx` (`bairro_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=735230 ;

-- --------------------------------------------------------

--
-- Table structure for table `uf`
--

CREATE TABLE IF NOT EXISTS `uf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) NOT NULL,
  `sigla` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bairros`
--
ALTER TABLE `bairros`
  ADD CONSTRAINT `bairros_ibfk_1` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`);

--
-- Constraints for table `cidades`
--
ALTER TABLE `cidades`
  ADD CONSTRAINT `cidades_ibfk_1` FOREIGN KEY (`uf_id`) REFERENCES `uf` (`id`);

--
-- Constraints for table `logradouros`
--
ALTER TABLE `logradouros`
  ADD CONSTRAINT `logradouros_ibfk_1` FOREIGN KEY (`bairro_id`) REFERENCES `bairros` (`id`);
