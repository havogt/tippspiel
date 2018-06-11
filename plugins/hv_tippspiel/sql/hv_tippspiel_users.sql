CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL_USERS` (
  `ID` BIGINT(20) unsigned NOT NULL,
  `WeltmeisterID` int(10) unsigned NOT NULL DEFAULT '0',
  `TorschuetzeID` int(10) unsigned NOT NULL DEFAULT '0',
  `ClubID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
