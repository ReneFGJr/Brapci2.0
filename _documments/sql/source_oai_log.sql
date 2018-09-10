-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 07, 2018 at 10:57 PM
-- Server version: 5.6.20-log
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `brapci`
--

-- --------------------------------------------------------

--
-- Table structure for table `source_oai_log`
--

CREATE TABLE IF NOT EXISTS `source_oai_log` (
`id_log` bigint(20) unsigned NOT NULL,
  `log_id_jnl` int(11) NOT NULL,
  `log_cmd` char(3) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_status` int(11) NOT NULL DEFAULT '1',
  `log_total` int(11) NOT NULL DEFAULT '0',
  `log_new` int(11) NOT NULL DEFAULT '0',
  `log_del` int(11) NOT NULL DEFAULT '0',
  `log_sucessul` int(11) NOT NULL DEFAULT '0',
  `log_r` char(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `source_oai_log`
--
ALTER TABLE `source_oai_log`
 ADD UNIQUE KEY `id_log` (`id_log`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `source_oai_log`
--
ALTER TABLE `source_oai_log`
MODIFY `id_log` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
