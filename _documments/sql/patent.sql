-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 21, 2019 at 11:27 PM
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
  `p_situacao` int(11) DEFAULT '0',
  `p_pct` char(30) NOT NULL DEFAULT '',
  `p_pct_data` date NOT NULL DEFAULT '0001-01-01',
  `p_pub` char(30) NOT NULL DEFAULT '',
  `p_pub_data` date NOT NULL DEFAULT '0001-01-01',
  `p_fase_nacional` date NOT NULL DEFAULT '0001-01-01'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patent_agent`
--

CREATE TABLE `patent_agent` (
  `id_pa` bigint(20) UNSIGNED NOT NULL,
  `pa_use` int(11) NOT NULL DEFAULT '0',
  `pa_nome` varchar(250) NOT NULL,
  `pa_pais` char(2) NOT NULL,
  `pa_estado` char(2) NOT NULL,
  `pa_tipo` char(1) NOT NULL DEFAULT '0',
  `pa_cnpj` char(20) NOT NULL DEFAULT ''
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
-- Table structure for table `patent_kindcode`
--

CREATE TABLE `patent_kindcode` (
  `id_pk` bigint(20) UNSIGNED NOT NULL,
  `pk_issue` int(11) NOT NULL,
  `pk_code` char(5) NOT NULL,
  `pk_patent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patent_pais_sigla`
--

CREATE TABLE `patent_pais_sigla` (
  `id_ps` bigint(20) UNSIGNED NOT NULL,
  `ps_nome` char(150) NOT NULL,
  `ps_sigla` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patent_pais_sigla`
--

INSERT INTO `patent_pais_sigla` (`id_ps`, `ps_nome`, `ps_sigla`) VALUES
(256, 'ANDORRA', 'AD'),
(257, 'EMIRADOS ARABES UNIDOS', 'AE'),
(258, 'AFEGANISTÃO', 'AF'),
(259, 'ANTÍGUA E BARBUDA', 'AG'),
(260, 'ANGUILLA', 'AI'),
(261, 'ALBÂNIA', 'AL'),
(262, 'ARMÊNIA', 'AM'),
(263, 'ANTILHAS HOLANDESAS', 'AN'),
(264, 'ANGOLA', 'AO'),
(265, 'ANTARTICA', 'AQ'),
(266, 'ARGENTINA', 'AR'),
(267, 'SAMOA AMERICANA', 'AS'),
(268, 'ÁUSTRIA', 'AT'),
(269, 'AUSTRÁLIA', 'AU'),
(270, 'ARUBA', 'AW'),
(271, 'AZERBAIJÃO', 'AZ'),
(272, 'BÓSNIA E HERZEGÓVINA', 'BA'),
(273, 'BARBADOS', 'BB'),
(274, 'BANGLADESH', 'BD'),
(275, 'BÉLGICA', 'BE'),
(276, 'BURKINA FASO', 'BF'),
(277, 'BULGÁRIA', 'BG'),
(278, 'BAREINE', 'BH'),
(279, 'BURUNDI', 'BI'),
(280, 'BENIN', 'BJ'),
(281, 'BERMUDAS', 'BM'),
(282, 'BRUNEI DARUSSALAM', 'BN'),
(283, 'BOLÍVIA', 'BO'),
(284, 'BRASIL', 'BR'),
(285, 'BAHAMAS', 'BS'),
(286, 'BUTÃO', 'BT'),
(287, 'ILHA BOUVET', 'BV'),
(288, 'BOTSUANA', 'BW'),
(289, 'BELARUS', 'BY'),
(290, 'BELIZE', 'BZ'),
(291, 'CANADÁ', 'CA'),
(292, 'ILHAS COCOS', 'CC'),
(293, 'REPÚBLICA CENTRO AFRICANA', 'CF'),
(294, 'CONGO', 'CG'),
(295, 'SUÍÇA', 'CH'),
(296, 'COSTA DO MARFIM', 'CI'),
(297, 'ILHAS COOK', 'CK'),
(298, 'CHILE', 'CL'),
(299, 'CAMARÕES', 'CM'),
(300, 'CHINA', 'CN'),
(301, 'COLÔMBIA', 'CO'),
(302, 'COSTA RICA', 'CR'),
(303, 'CUBA', 'CU'),
(304, 'CABO VERDE', 'CV'),
(305, 'ILHA NATAL', 'CX'),
(306, 'CHIPRE', 'CY'),
(307, 'REPÚBLICA TCHECA', 'CZ'),
(308, 'ALEMANHA', 'DE'),
(309, 'DJIBUTI', 'DJ'),
(310, 'DINAMARCA', 'DK'),
(311, 'DOMINICA', 'DM'),
(312, 'REPÚBLICA DOMINICANA', 'DO'),
(313, 'ARGÉLIA', 'DZ'),
(314, 'EQUADOR', 'EC'),
(315, 'ESTÔNIA', 'EE'),
(316, 'EGITO', 'EG'),
(317, 'SAARA OCIDENTAL', 'EH'),
(319, 'OPÉIA DE PATENTES', 'EU'),
(320, 'ERITRÉIA', 'ER'),
(321, 'ESPANHA', 'ES'),
(322, 'ETIÓPIA', 'ET'),
(323, 'FINLÂNDIA', 'FI'),
(324, 'CHANNEL ISLAND OF GUERNSEY', 'GG'),
(325, 'FIJI', 'FJ'),
(326, 'ILHAS MALVINAS', 'FK'),
(327, 'MICRONÉSIA (EST. DA FEDERAÇÃO)', 'FM'),
(328, 'ILHAS FAROE', 'FO'),
(329, 'FRANÇA', 'FR'),
(330, 'GABÃO', 'GA'),
(331, 'REINO UNIDO', 'GB'),
(332, 'GRANADA', 'GD'),
(333, 'GEÓRGIA', 'GE'),
(334, 'GUIANA FRANCESA', 'GF'),
(335, 'GANA', 'GH'),
(336, 'GIBRALTAR', 'GI'),
(337, 'GROELÂNDIA', 'GL'),
(338, 'GÂMBIA', 'GM'),
(339, 'GUINÉ', 'GN'),
(340, 'GUADALUPE', 'GP'),
(341, 'GUINÉ EQUATORIAL', 'GQ'),
(342, 'GRÉCIA', 'GR'),
(343, 'GEORGIA DO SUL E ILHAS SANDWICH DO SUL', 'GS'),
(344, 'GUATEMALA', 'GT'),
(345, 'GUAM', 'GU'),
(346, 'GUINÉ BISSAU', 'GW'),
(347, 'GUIANA', 'GY'),
(348, 'HONG-KONG', 'HK'),
(349, 'ILHAS HEARD E MC DONALD', 'HM'),
(350, 'HONDURAS', 'HN'),
(351, 'CROÁCIA', 'HR'),
(352, 'HAITI', 'HT'),
(353, 'HUNGRIA', 'HU'),
(354, 'INDONÉSIA', 'ID'),
(355, 'IRLANDA', 'IE'),
(356, 'ISRAEL', 'IL'),
(357, 'ILHA DO HOMEM', 'IM'),
(358, 'ÍNDIA', 'IN'),
(359, 'TERRIT. BRITAN. OCEANO ÍNDICO', 'IO'),
(360, 'IRAQUE', 'IQ'),
(361, 'IRÃ (REPÚBLICA ISLÂMICA DO)', 'IR'),
(362, 'ISLÂNDIA', 'IS'),
(363, 'ITÁLIA', 'IT'),
(364, 'JAMAICA', 'JM'),
(365, 'JORDÂNIA', 'JO'),
(366, 'JAPÃO', 'JP'),
(367, 'QUÊNIA', 'KE'),
(368, 'QUIRGUISTÃO', 'KG'),
(369, 'CAMBOJA', 'KH'),
(370, 'KIRIBATI', 'KI'),
(371, 'COMORES', 'KM'),
(372, 'SÃO CRISTÓVÃO E NEVIS', 'KN'),
(373, 'REPÚBLICA POPULAR DEM. DA CORÉIA', 'KP'),
(374, 'REPÚBLICA DA CORÉIA', 'KR'),
(375, 'KUWAIT', 'KW'),
(376, 'ILHAS CAIMAN', 'KY'),
(377, 'CAZAQUISTÃO', 'KZ'),
(378, 'LAOS', 'LA'),
(379, 'LÍBANO', 'LB'),
(380, 'SANTA LÚCIA', 'LC'),
(381, 'LIECHTENSTEIN', 'LI'),
(382, 'SRI LANKA', 'LK'),
(383, 'LIBÉRIA', 'LR'),
(384, 'LESOTO', 'LS'),
(385, 'LITUÂNIA', 'LT'),
(386, 'LUXEMBURGO', 'LU'),
(387, 'LETÔNIA', 'LV'),
(388, 'LÍBIA', 'LY'),
(389, 'MARROCOS', 'MA'),
(390, 'MÔNACO', 'MC'),
(391, 'REPÚBLICA DA MOLDOVA', 'MD'),
(392, 'MADAGASCAR', 'MG'),
(393, 'ILHAS MARSHALL', 'MH'),
(394, 'ANT.IUGOSLÁVIA (REP.MACEDÔNIA)', 'MK'),
(395, 'MALI', 'ML'),
(396, 'MIANMÁ', 'MM'),
(397, 'MONGÓLIA', 'MN'),
(398, 'MACAU', 'MO'),
(399, 'ILHAS MARIANAS DO NORTE', 'MP'),
(400, 'MARTINICA', 'MQ'),
(401, 'MAURITÂNIA', 'MR'),
(402, 'MONT SERRAT', 'MS'),
(403, 'MALTA', 'MT'),
(404, 'MAURÍCIO', 'MU'),
(405, 'MALDIVAS', 'MV'),
(406, 'MALÁWI', 'MW'),
(407, 'MÉXICO', 'MX'),
(408, 'MALÁSIA', 'MY'),
(409, 'MOÇAMBIQUE', 'MZ'),
(410, 'NAMÍBIA', 'NA'),
(411, 'NOVA CALEDÔNIA', 'NC'),
(412, 'NÍGER', 'NE'),
(413, 'ILHA NORFALK', 'NF'),
(414, 'NIGÉRIA', 'NG'),
(415, 'NICARÁGUA', 'NI'),
(416, 'HOLANDA', 'NL'),
(417, 'NORUEGA', 'NO'),
(418, 'NEPAL', 'NP'),
(419, 'NAURU', 'NR'),
(420, 'NIUE', 'NU'),
(421, 'NOVA ZELÂNDIA', 'NZ'),
(422, 'OMÃ', 'OM'),
(423, 'PANAMÁ', 'PA'),
(424, 'PAÍSES BAIXOS', 'PB'),
(425, 'PERU', 'PE'),
(426, 'POLINÉSIA FRANCESA', 'PF'),
(427, 'PAPUA NOVA GUINÉ', 'PG'),
(428, 'FILIPINAS', 'PH'),
(429, 'PAQUISTÃO', 'PK'),
(430, 'POLÔNIA', 'PL'),
(431, 'SAINT PIERRE E MIQUELON', 'PM'),
(432, 'PITCAIRN', 'PN'),
(433, 'PORTO RICO', 'PR'),
(434, 'TERRITÓRIO OCUPADO PALESTINO', 'PS'),
(435, 'PORTUGAL', 'PT'),
(436, 'PALAU', 'PW'),
(437, 'PARAGUAI', 'PY'),
(438, 'CATAR', 'QA'),
(439, 'REUNIÃO', 'RE'),
(440, 'ROMÊNIA', 'RO'),
(441, 'FEDERAÇÃO RUSSA', 'RU'),
(442, 'RUANDA', 'RW'),
(443, 'ARÁBIA SAUDITA', 'SA'),
(444, 'ILHAS SALOMÃO', 'SB'),
(445, 'SEYCHELLES', 'SC'),
(446, 'SUDÃO', 'SD'),
(447, 'SUÉCIA', 'SE'),
(448, 'SINGAPURA', 'SG'),
(449, 'SANTA HELENA', 'SH'),
(450, 'ESLOVENIA', 'SI'),
(451, 'SVALBARD E JAN MAYEN', 'SJ'),
(452, 'ESLOVÁQUIA', 'SK'),
(453, 'SERRA LEOA', 'SL'),
(454, 'SÃO MARINO', 'SM'),
(455, 'SENEGAL', 'SN'),
(456, 'SOMÁLIA', 'SO'),
(457, 'SURINAME', 'SR'),
(458, 'SÃO TOMÉ E PRÍNCIPE', 'ST'),
(459, 'EL SALVADOR', 'SV'),
(460, 'SÍRIA', 'SY'),
(461, 'SUAZILÂNDIA', 'SZ'),
(462, 'ILHAS TURKS E CAICOS', 'TC'),
(463, 'CHADE', 'TD'),
(464, 'TERRAS AUSTRAIS FRANCESAS', 'TF'),
(465, 'TOGO', 'TG'),
(466, 'TAILÂNDIA', 'TH'),
(467, 'ADJIQUISTÀO', 'T'),
(468, 'TOKELAU', 'TK'),
(469, 'TIMOR-LESTE', 'TL'),
(470, 'TURCOMENISTÃO', 'TM'),
(471, 'TUNÍSIA', 'TN'),
(472, 'TONGA', 'TO'),
(473, 'TURQUIA', 'TR'),
(474, 'TRINIDAD E TOBAGO', 'TT'),
(475, 'TUVALU', 'TV'),
(476, 'TAIWAN, PROVÍNCIA DA', 'TW'),
(477, 'REPÚBLICA UNIDA DA TANZÂNIA', 'TZ'),
(478, 'UCRÂNIA', 'UA'),
(479, 'UGANDA', 'UG'),
(480, 'ILHAS MENORES AFASTADAS / EUA', 'UM'),
(481, 'ESTADOS UNIDOS', 'US'),
(482, 'URUGUAI', 'UY'),
(483, 'UZBEQUISTÃO', 'UZ'),
(484, 'VATICANO', 'VA'),
(485, 'SÃO VICENTE E GRANADINAS', 'VC'),
(486, 'VENEZUELA', 'VE'),
(487, 'ILHAS VIRGENS (BRITÂNICAS)', 'VG'),
(488, 'ILHAS VIRGENS (U.S.)', 'VI'),
(489, 'VIETNÃ', 'VN'),
(490, 'VANUATU', 'VU'),
(491, 'ILHAS WALLIS E FUTURA', 'WF'),
(492, 'SAMOA OCIDENTAL', 'WS'),
(493, 'IÊMEN', 'YE'),
(494, 'MAYOTTE', 'YT'),
(495, 'YUGOSLÁVIA', 'YU'),
(496, 'ÁFRICA DO SUL', 'ZA'),
(497, 'ZÂMBIA', 'ZM'),
(498, 'ZAIRE', 'ZR'),
(499, 'ZIMBÁBUE', 'ZW'),
(500, 'Organização Regional de Propriedade Industrial Africana(Aripo)', 'AP'),
(501, 'Escritório de Marcas e Modelos de Benelux(BOIP)', 'BX'),
(502, 'Escritório Eurasiano de Patentes(EAPO)', 'EA'),
(503, 'Organização Européia de Patentes(EPO)', 'EP'),
(504, 'Conselho de cooperação do Golfo(GCC)', 'GC'),
(505, 'Instituto Internacional de Patentes', 'IB'),
(506, 'Instituto da Propriedade Intelectual da União Européia(EUIPO)', 'EM'),
(507, 'Organização Africana de Propriedade Intelectual(OAPI)', 'OA'),
(508, 'Organização Mundial de Propriedade Intelectual(OMPI)(WIPO)', 'WO'),
(509, 'Instituto Nórdico de Patentes(NPI)', 'XN'),
(510, 'Instituto Visegrad de Patentes(VPI)', 'XV'),
(511, 'ORGANIZAÇÃO EUROPÉIA DE PATENTES', '10');

-- --------------------------------------------------------

--
-- Table structure for table `patent_pct`
--

CREATE TABLE `patent_pct` (
  `id_pct` bigint(20) UNSIGNED NOT NULL,
  `pct_patent` int(11) NOT NULL,
  `pct_nr` char(20) NOT NULL,
  `pct_data` date NOT NULL
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
-- Table structure for table `patent_pub`
--

CREATE TABLE `patent_pub` (
  `id_pub` bigint(20) UNSIGNED NOT NULL,
  `pub_nr` char(20) NOT NULL,
  `pub_data` date NOT NULL,
  `pub_patent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `ps_method` char(10) NOT NULL,
  `ps_issue` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patent_source`
--

INSERT INTO `patent_source` (`id_ps`, `ps_name`, `ps_abreviatura`, `ps_url`, `ps_url_oai`, `ps_status`, `ps_last_harvesting`, `ps_method`, `ps_issue`) VALUES
(1, 'Revista da Propriedade Industrial', 'RPI', 'http://revistas.inpi.gov.br/rpi/', '', 1, '2019-06-21', 'INPI', 1132);

-- --------------------------------------------------------

--
-- Table structure for table `_search`
--

CREATE TABLE `_search` (
  `id_s` bigint(20) UNSIGNED NOT NULL,
  `s_date` date NOT NULL,
  `s_hour` char(8) NOT NULL,
  `s_query` text NOT NULL,
  `s_user` int(11) NOT NULL,
  `s_page` int(11) NOT NULL DEFAULT '0',
  `s_type` int(11) NOT NULL,
  `s_session` int(11) NOT NULL,
  `s_total` int(11) NOT NULL,
  `s_ip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patent`
--
ALTER TABLE `patent`
  ADD UNIQUE KEY `id_p` (`id_p`),
  ADD UNIQUE KEY `patent_nr` (`p_nr`) USING BTREE;

--
-- Indexes for table `patent_agent`
--
ALTER TABLE `patent_agent`
  ADD UNIQUE KEY `id_pa` (`id_pa`),
  ADD UNIQUE KEY `pa_agent` (`pa_nome`,`pa_pais`) USING BTREE;

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
  ADD UNIQUE KEY `id_cc` (`id_cc`),
  ADD UNIQUE KEY `classes` (`cc_class`) USING BTREE;

--
-- Indexes for table `patent_classification`
--
ALTER TABLE `patent_classification`
  ADD UNIQUE KEY `id_c` (`id_c`),
  ADD UNIQUE KEY `classes` (`c_patent`,`c_class`,`c_subclass`) USING BTREE;

--
-- Indexes for table `patent_despacho`
--
ALTER TABLE `patent_despacho`
  ADD UNIQUE KEY `id_pd` (`id_pd`),
  ADD UNIQUE KEY `despache` (`pd_patent`,`pd_issue`,`pd_section`) USING BTREE;

--
-- Indexes for table `patent_issue`
--
ALTER TABLE `patent_issue`
  ADD UNIQUE KEY `id_issue` (`id_issue`);

--
-- Indexes for table `patent_kindcode`
--
ALTER TABLE `patent_kindcode`
  ADD UNIQUE KEY `id_pk` (`id_pk`),
  ADD UNIQUE KEY `kINDcODE` (`pk_issue`,`pk_code`,`pk_patent`) USING BTREE;

--
-- Indexes for table `patent_pais_sigla`
--
ALTER TABLE `patent_pais_sigla`
  ADD UNIQUE KEY `id_ps` (`id_ps`);

--
-- Indexes for table `patent_pct`
--
ALTER TABLE `patent_pct`
  ADD UNIQUE KEY `id_pct` (`id_pct`),
  ADD UNIQUE KEY `PCT1` (`pct_nr`,`pct_data`,`pct_patent`) USING BTREE,
  ADD KEY `pct_nr` (`pct_nr`);

--
-- Indexes for table `patent_prioridade`
--
ALTER TABLE `patent_prioridade`
  ADD UNIQUE KEY `id_prior` (`id_prior`),
  ADD UNIQUE KEY `Prior1` (`prior_patent`,`prior_numero_prioridade`,`prior_sigla_pais`) USING BTREE,
  ADD KEY `prio_numero` (`prior_numero_prioridade`(20));

--
-- Indexes for table `patent_pub`
--
ALTER TABLE `patent_pub`
  ADD UNIQUE KEY `id_pub` (`id_pub`),
  ADD UNIQUE KEY `publicacao` (`pub_nr`,`pub_data`,`pub_patent`) USING BTREE;

--
-- Indexes for table `patent_section`
--
ALTER TABLE `patent_section`
  ADD UNIQUE KEY `id_ps` (`id_ps`),
  ADD UNIQUE KEY `Sessions` (`ps_acronic`);

--
-- Indexes for table `patent_source`
--
ALTER TABLE `patent_source`
  ADD UNIQUE KEY `id_ps` (`id_ps`);

--
-- Indexes for table `_search`
--
ALTER TABLE `_search`
  ADD UNIQUE KEY `id_s` (`id_s`);

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
-- AUTO_INCREMENT for table `patent_kindcode`
--
ALTER TABLE `patent_kindcode`
  MODIFY `id_pk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_pais_sigla`
--
ALTER TABLE `patent_pais_sigla`
  MODIFY `id_ps` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;
--
-- AUTO_INCREMENT for table `patent_pct`
--
ALTER TABLE `patent_pct`
  MODIFY `id_pct` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_prioridade`
--
ALTER TABLE `patent_prioridade`
  MODIFY `id_prior` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patent_pub`
--
ALTER TABLE `patent_pub`
  MODIFY `id_pub` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
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
--
-- AUTO_INCREMENT for table `_search`
--
ALTER TABLE `_search`
  MODIFY `id_s` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
