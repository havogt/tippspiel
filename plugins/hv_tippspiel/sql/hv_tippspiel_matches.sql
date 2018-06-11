CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL_MATCHES` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Gruppe` char(1) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `Team1` int(10) unsigned NOT NULL DEFAULT '0',
  `Team2` int(10) unsigned NOT NULL DEFAULT '0',
  `Goals1` int(2) NOT NULL DEFAULT '-1',
  `Goals2` int(2) NOT NULL DEFAULT '-1',
  `Datum` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=100;
