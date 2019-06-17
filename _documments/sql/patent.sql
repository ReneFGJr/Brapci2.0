-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 17, 2019 at 10:50 AM
-- Server version: 5.7.11
-- PHP Version: 5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `patent`
--

-- --------------------------------------------------------

--
-- Table structure for table `patent`
--

CREATE TABLE `patent` (
  `id_p` bigint(20) UNSIGNED NOT NULL,
  `p_nr` char(20) NOT NULL,
  `p_dt_deposito` date NOT NULL,
  `p_dt_publicacao` date NOT NULL,
  `p_dt_concessao` date NOT NULL,
  `p_title` text NOT NULL,
  `p_resumo` text NOT NULL,
  `p_situacao` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patent_agent`
--

CREATE TABLE `patent_agent` (
  `id_pa` bigint(20) UNSIGNED NOT NULL,
  `pa_nome` text NOT NULL,
  `pa_pais` char(2) NOT NULL,
  `pa_estado` char(2) NOT NULL,
  `pa_tipo` char(1) NOT NULL,
  `pa_cnpj` char(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patent_agent_relation`
--

CREATE TABLE `patent_agent_relation` (
  `id_rl` bigint(20) UNSIGNED NOT NULL,
  `rl_patent` int(11) NOT NULL,
  `rl_agent` int(11) NOT NULL,
  `rl_relation` char(1) COLLATE utf8_bin NOT NULL,
  `rl_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rl_seq` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `patent_class`
--

CREATE TABLE `patent_class` (
  `id_cc` bigint(20) UNSIGNED NOT NULL,
  `cc_class` char(14) COLLATE utf8_bin NOT NULL,
  `cc_cod` char(7) COLLATE utf8_bin NOT NULL,
  `cc_c1` char(1) COLLATE utf8_bin NOT NULL,
  `cc_c2` char(2) COLLATE utf8_bin NOT NULL,
  `cc_c3` char(1) COLLATE utf8_bin NOT NULL,
  `cc_c4` int(11) NOT NULL DEFAULT '0',
  `cc_c5` int(11) NOT NULL DEFAULT '0',
  `cc_name` char(255) COLLATE utf8_bin NOT NULL,
  `cc_description` text COLLATE utf8_bin NOT NULL,
  `cc_language` char(5) COLLATE utf8_bin NOT NULL DEFAULT 'pt_BR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `patent_classification`
--

CREATE TABLE `patent_classification` (
  `id_c` bigint(20) UNSIGNED NOT NULL,
  `c_patent` int(11) NOT NULL,
  `c_class` char(8) COLLATE utf8_bin NOT NULL,
  `c_subclass` char(8) COLLATE utf8_bin NOT NULL,
  `c_c` char(12) COLLATE utf8_bin NOT NULL,
  `c_cod` char(7) COLLATE utf8_bin NOT NULL,
  `c_seq` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `patent_despacho`
--

CREATE TABLE `patent_despacho` (
  `id_pd` bigint(20) UNSIGNED NOT NULL,
  `pd_patent` int(11) NOT NULL,
  `pd_issue` int(11) NOT NULL,
  `pd_section` char(10) COLLATE utf8_bin NOT NULL,
  `pd_comentario` text COLLATE utf8_bin NOT NULL,
  `pd_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pd_method` char(5) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `patent_issue`
--

CREATE TABLE `patent_issue` (
  `id_issue` bigint(20) UNSIGNED NOT NULL,
  `issue_source` int(11) NOT NULL,
  `issue_year` char(10) NOT NULL,
  `issue_number` char(10) NOT NULL,
  `issue_published` date NOT NULL,
  `issue_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patent_prioridade`
--

CREATE TABLE `patent_prioridade` (
  `id_prior` bigint(20) UNSIGNED NOT NULL,
  `prior_seq` int(11) NOT NULL,
  `prior_numero_prioridade` char(50) COLLATE utf8_bin NOT NULL,
  `prior_sigla_pais` char(2) COLLATE utf8_bin NOT NULL,
  `prior_data_prioridade` date NOT NULL,
  `prior_patent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `patent_section`
--

CREATE TABLE `patent_section` (
  `id_ps` bigint(20) UNSIGNED NOT NULL,
  `ps_name` char(200) NOT NULL,
  `ps_acronic` char(20) NOT NULL,
  `ps_description` text NOT NULL,
  `ps_source` int(11) NOT NULL,
  `ps_active` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patent_source`
--

CREATE TABLE `patent_source` (
  `id_ps` bigint(20) UNSIGNED NOT NULL,
  `ps_name` char(100) NOT NULL,
  `ps_abreviatura` char(15) NOT NULL,
  `ps_url` char(100) NOT NULL,
  `ps_url_oai` char(100) NOT NULL,
  `ps_status` int(11) NOT NULL DEFAULT '1',
  `ps_last_harvesting` date NOT NULL,
  `ps_method` char(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patent_source`
--

INSERT INTO `patent_source` (`id_ps`, `ps_name`, `ps_abreviatura`, `ps_url`, `ps_url_oai`, `ps_status`, `ps_last_harvesting`, `ps_method`) VALUES
(1, 'Revista da Propriedade Industrial', 'RPI', 'http://revistas.inpi.gov.br/rpi/', '', 1, '0000-00-00', 'INPI');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patent`
--
ALTER TABLE `patent`
  ADD UNIQUE KEY `id_p` (`id_p`),
  ADD KEY `patent_nr` (`p_nr`);

--
-- Indexes for table `patent_agent`
--
ALTER TABLE `patent_agent`
  ADD UNIQUE KEY `id_pa` (`id_pa`),
  ADD KEY `pa_agent` (`pa_nome`(20));

--
-- Indexes for table `patent_agent_relation`
--
ALTER TABLE `patent_agent_relation`
  ADD UNIQUE KEY `id_rl` (`id_rl`),
  ADD UNIQUE KEY `relacao_patent` (`rl_patent`,`rl_agent`,`rl_relation`) USING BTREE;

--
-- Indexes for table `patent_class`
--
ALTER TABLE `patent_class`
  ADD UNIQUE KEY `id_cc` (`id_cc`);

--
-- Indexes for table `patent_classification`
--
ALTER TABLE `patent_classification`
  ADD UNIQUE KEY `id_c` (`id_c`),
  ADD UNIQUE KEY `classes` (`c_patent`,`c_class`,`c_subclass`);

--
-- Indexes for table `patent_despacho`
--
ALTER TABLE `patent_despacho`
  ADD UNIQUE KEY `id_pd` (`id_pd`);

--
-- Indexes for table `patent_issue`
--
ALTER TABLE `patent_issue`
  ADD UNIQUE KEY `id_issue` (`id_issue`);

--
-- Indexes for table `patent_prioridade`
--
ALTER TABLE `patent_prioridade`
  ADD UNIQUE KEY `id_prior` (`id_prior`);

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
-- AUTO_INCREMENT for table `patent`
--
ALTER TABLE `patent`
  MODIFY `id_p` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_agent`
--
ALTER TABLE `patent_agent`
  MODIFY `id_pa` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_agent_relation`
--
ALTER TABLE `patent_agent_relation`
  MODIFY `id_rl` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_class`
--
ALTER TABLE `patent_class`
  MODIFY `id_cc` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_classification`
--
ALTER TABLE `patent_classification`
  MODIFY `id_c` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_despacho`
--
ALTER TABLE `patent_despacho`
  MODIFY `id_pd` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_issue`
--
ALTER TABLE `patent_issue`
  MODIFY `id_issue` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_prioridade`
--
ALTER TABLE `patent_prioridade`
  MODIFY `id_prior` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_section`
--
ALTER TABLE `patent_section`
  MODIFY `id_ps` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_source`
--
ALTER TABLE `patent_source`
  MODIFY `id_ps` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
