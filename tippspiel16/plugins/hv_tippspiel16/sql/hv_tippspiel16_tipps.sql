CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL16_TIPPS` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` bigint(20) unsigned NOT NULL,
  `MatchID` int(10) unsigned NOT NULL,
  `Goals1` int(3) NOT NULL,
  `Goals2` int(3) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;