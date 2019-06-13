-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 13, 2019 at 08:03 PM
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
-- Table structure for table `patent_issue`
--

CREATE TABLE IF NOT EXISTS `patent_issue` (
`id_issue` bigint(20) unsigned NOT NULL,
  `issue_source` int(11) NOT NULL,
  `issue_year` char(10) NOT NULL,
  `issue_number` char(10) NOT NULL,
  `issue_published` date NOT NULL,
  `issue_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `patent_issue`
--

INSERT INTO `patent_issue` (`id_issue`, `issue_source`, `issue_year`, `issue_number`, `issue_published`, `issue_created`) VALUES
(1, 1, '2019', '2527', '2019-06-11', '2019-06-13 14:24:38');

-- --------------------------------------------------------

--
-- Table structure for table `patent_section`
--

CREATE TABLE IF NOT EXISTS `patent_section` (
`id_ps` bigint(20) unsigned NOT NULL,
  `ps_name` char(200) NOT NULL,
  `ps_acronic` char(20) NOT NULL,
  `ps_description` text NOT NULL,
  `ps_source` int(11) NOT NULL,
  `ps_active` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `patent_section`
--

INSERT INTO `patent_section` (`id_ps`, `ps_name`, `ps_acronic`, `ps_description`, `ps_source`, `ps_active`) VALUES
(1, 'Publicação Internacional – PCT. Apresentação de petição de requerimento de entrada na fase nacional\r\n', '1.1', 'Comunicação da publicação internacional do pedido internacional nos termos do Tratado de Cooperação em matéria de Patentes – PCT e da apresentação de petição de requerimento de entrada na fase nacional.\r\nDocumento publicado disponível no endereço eletrônico http://www.wipo.int/pct/en do sistema PATENTSCOPE® Search Service da Organização Mundial de Propriedade Intelectual – OMPI.\r\n', 1, 1),
(2, 'Retificação', '1.1.1', 'Retificação da notificação da publicação internacional e da apresentação de petição de requerimento de entrada na fase nacional por ter sido efetuada com incorreção.\r\n', 1, 1),
(3, 'PR - Recursos', 'PR - Recursos', '???', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `patent_source`
--

CREATE TABLE IF NOT EXISTS `patent_source` (
`id_ps` bigint(20) unsigned NOT NULL,
  `ps_name` char(100) NOT NULL,
  `ps_abreviatura` char(15) NOT NULL,
  `ps_url` char(100) NOT NULL,
  `ps_url_oai` char(100) NOT NULL,
  `ps_status` int(11) NOT NULL DEFAULT '1',
  `ps_last_harvesting` date NOT NULL,
  `ps_method` char(10) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `patent_source`
--

INSERT INTO `patent_source` (`id_ps`, `ps_name`, `ps_abreviatura`, `ps_url`, `ps_url_oai`, `ps_status`, `ps_last_harvesting`, `ps_method`) VALUES
(1, 'Revista da Propriedade Industrial', 'RPI', 'http://revistas.inpi.gov.br/rpi/', '', 1, '0000-00-00', 'INPI');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patent_issue`
--
ALTER TABLE `patent_issue`
 ADD UNIQUE KEY `id_issue` (`id_issue`);

--
-- Indexes for table `patent_section`
--
ALTER TABLE `patent_section`
 ADD UNIQUE KEY `id_ps` (`id_ps`);

--
-- Indexes for table `patent_source`
--
ALTER TABLE `patent_source`
 ADD UNIQUE KEY `id_ps` (`id_ps`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patent_issue`
--
ALTER TABLE `patent_issue`
MODIFY `id_issue` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `patent_section`
--
ALTER TABLE `patent_section`
MODIFY `id_ps` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `patent_source`
--
ALTER TABLE `patent_source`
MODIFY `id_ps` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
