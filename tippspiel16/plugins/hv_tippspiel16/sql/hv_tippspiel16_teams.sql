CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL16_TEAMS` (
  `TeamID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) COLLATE latin1_german2_ci NOT NULL,
  `NameShort` varchar(3) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`TeamID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=25 ;

INSERT INTO `HV_TIPPSPIEL16_TEAMS` (`TeamID`, `Name`, `NameShort`) VALUES
(1, 'Albanien', 'ALB'),
(2, 'Belgien', 'BEL'),
(3, 'Deutschland', 'GER'),
(4, 'England', 'ENG'),
(5, 'Frankreich', 'FRA'),
(6, 'Island', 'ISL'),
(7, 'Italien', 'ITA'),
(8, 'Kroatien', 'CRO'),
(9, 'Nordirland', 'NIR'),
(10, 'Österreich', 'AUT'),
(11, 'Polen', 'POL'),
(12, 'Portugal', 'POR'),
(13, 'Irland', 'IRL'),
(14, 'Rumänien', 'ROU'),
(15, 'Russland', 'RUS'),
(16, 'Schweden', 'SWE'),
(17, 'Schweiz', 'SUI'),
(18, 'Slowakei', 'SVK'),
(19, 'Spanien', 'ESP'),
(20, 'Tschechien', 'CZE'),
(21, 'Türkei', 'TUR'),
(22, 'Ukraine', 'UKR'),
(23, 'Ungarn', 'HUN'),
(24, 'Wales', 'WAL');
