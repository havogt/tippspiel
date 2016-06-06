CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL16_MATCHES` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Gruppe` char(1) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `Team1` int(10) unsigned NOT NULL DEFAULT '0',
  `Team2` int(10) unsigned NOT NULL DEFAULT '0',
  `Goals1` int(2) NOT NULL DEFAULT '-1',
  `Goals2` int(2) NOT NULL DEFAULT '-1',
  `Datum` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=37;

INSERT INTO `HV_TIPPSPIEL16_MATCHES` (`ID`, `Gruppe`, `Team1`, `Team2`, `Goals1`, `Goals2`, `Datum`) VALUES
(1, 'A', 5, 14, -1, -1, 1465585200),
(2, 'A', 1, 17, -1, -1, 1465650000),
(3, 'B', 24, 18, -1, -1, 1465660800),
(4, 'B', 4, 15, -1, -1, 1465671600),
(5, 'D', 21, 8, -1, -1, 1465736400),
(6, 'C', 11, 9, -1, -1, 1465747200),
(7, 'C', 3, 22, -1, -1, 1465758000),
(8, 'D', 19, 20, -1, -1, 1465822800),
(9, 'E', 13, 16, -1, -1, 1465833600),
(10, 'E', 2, 7, -1, -1, 1465844400),
(11, 'F', 10, 23, -1, -1, 1465920000),
(12, 'F', 12, 6, -1, -1, 1465930800),
(13, 'B', 15, 18, -1, -1, 1465995600),
(14, 'A', 14, 17, -1, -1, 1466006400),
(15, 'A', 5, 1, -1, -1, 1466017200),
(16, 'B', 4, 24, -1, -1, 1466082000),
(17, 'C', 3, 11, -1, -1, 1466103600),
(18, 'D', 7, 16, -1, -1, 1466168400),
(19, 'D', 20, 8, -1, -1, 1466179200),
(20, 'D', 19, 21, -1, -1, 1466190000),
(21, 'E', 2, 13, -1, -1, 1466254800),
(22, 'F', 6, 23, -1, -1, 1466265600),
(23, 'F', 12, 10, -1, -1, 1466276400),
(24, 'C', 22, 9, -1, -1, 1466092800),
(25, 'A', 17, 5, -1, -1, 1466362800),
(26, 'A', 14, 1, -1, -1, 1466362800),
(27, 'B', 18, 4, -1, -1, 1466449200),
(28, 'B', 15, 24, -1, -1, 1466449200),
(29, 'C', 9, 3, -1, -1, 1466524800),
(30, 'C', 22, 11, -1, -1, 1466524800),
(31, 'D', 8, 19, -1, -1, 1466535600),
(32, 'D', 20, 21, -1, -1, 1466535600),
(33, 'F', 6, 10, -1, -1, 1466611200),
(34, 'F', 23, 12, -1, -1, 1466611200),
(35, 'E', 16, 2, -1, -1, 1466622000),
(36, 'E', 7, 13, -1, -1, 1466622000);