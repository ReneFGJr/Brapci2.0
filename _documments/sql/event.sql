-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 24, 2019 at 10:09 PM
-- Server version: 5.7.11
-- PHP Version: 5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brapci`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id_ev` bigint(20) UNSIGNED NOT NULL,
  `ev_name` char(200) NOT NULL,
  `ev_place` char(50) NOT NULL,
  `ev_ative` int(11) NOT NULL DEFAULT '1',
  `ev_data_start` int(11) NOT NULL,
  `ev_data_end` int(11) NOT NULL,
  `ev_local` text NOT NULL,
  `ev_deadline` int(11) NOT NULL,
  `ev_url` char(150) NOT NULL,
  `ev_description` text NOT NULL,
  `ev_image` char(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id_ev`, `ev_name`, `ev_place`, `ev_ative`, `ev_data_start`, `ev_data_end`, `ev_local`, `ev_deadline`, `ev_url`, `ev_description`, `ev_image`) VALUES
(1, 'Ontobras', 'Porto Alegre, RS, Brasil', 1, 20190902, 20190905, 'UFRGS – Instituto de Informática\r\nAvenida Bento Gonçalves, 9500 – Agronomia, Porto Alegre – RS, 91509-900\r\nTelefone: (51) 3308-6168', 20190415, 'http://www.inf.ufrgs.br/ontobras/?lang=pt', 'Ontologia é um campo interdisciplinar preocupado com o estudo de conceitos e teorias que dão embasamento para a construção de conceitualizações compartilhadas de domínios específicos. Em anos recentes, notamos um crescimento no interesse na aplicação de ontologias para a solução de problemas de modelagem e classificação em diversas áreas como: Ciência da Computação, Ciência da Informação, Filosofia, Inteligência Artificial, Linguística, Gerência de Conhecimento, entre outras.\r\n\r\nO Seminário Brasileiro de Ontologias (Ontobras) antevê uma oportunidade e provê um ambiente científico no qual pesquisadores de diversas áreas podem trocar conhecimentos em teorias, metodologias, linguagens, ferramentas e experiências relacionadas ao desenvolvimento e aplicação de ontologias. Em particular, a comunidade se esforçou para integrar eventos brasileiros anteriores sobre Ontologias nos últimos anos, criando um novo fórum internacional único, altamente qualificado cientificamente para apresentação e discussão sobre o assunto no Brasil.', 'img/events/ontobras.jpg'),
(2, 'I Encontro de RDA no Brasil', 'Florianópolis, SC, Brasil', 1, 20190416, 20190418, 'Hotel Quinta da Bica D’Água\r\nRua Capitão Romualdo de Barros, 641 Carvoeira – Florianópolis.', 20190210, 'http://rdanobrasil.org/', '', 'img/events/rdabrasil.jpg'),
(3, 'XX Enancib', 'Florianópolis, SC, Brasil', 1, 20191022, 20191025, 'Universidade Federal de Santa Catarina (UFSC)', 0, '', '', 'img/events/enancib.jpg'),
(4, 'ISKO-Brasil', 'Belém, PA, Brasil', 1, 20190902, 20190903, 'Universidade Federal do Pará', 0, 'http://isko-brasil.org.br/?page_id=1563', '', 'img/events/isko-brasil.jpg'),
(5, 'Conferência Luso-Brasileira de Acesso Aberto (ConfOA)', 'Manaus, AM, Brasil', 1, 20191001, 20191004, '', 0, 'http://confoa.rcaap.pt/2019/', 'A 10ª Conferência Luso-Brasileira de Ciência Aberta (ConfOA) viaja até à Amazónia em 2019. Este ano, a ConfOA é acolhida conjuntamente pela Universidade Federal do Amazonas, a Universidade do Estado do Amazonas e o Instituto Federal do Amazonas. A 10ª ConfOA decorrerá em Manaus, de 1 a 4 de outubro, com abertura e um pré-workshop no dia 1, o programa principal da conferência nos dias 2 e 3, e workshops pós-conferência previstos para 4 de outubro.', 'img/events/confoa.jpg'),
(6, 'Encontro Latinoamericano de Bibliotecários, Arquivistas e Museólogos (EBAM)', 'San Juan, Porto Rico', 1, 20190805, 20190809, '', 0, 'https://ebam2019.wordpress.com/', '', 'img/events/ebam.jpg'),
(7, 'Encontro Ibérico EDICIC 2019', 'Barcelona, Espanha', 1, 20190709, 20190711, '', 0, '', '', 'img/events/edicic.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD UNIQUE KEY `id_ev` (`id_ev`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id_ev` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
