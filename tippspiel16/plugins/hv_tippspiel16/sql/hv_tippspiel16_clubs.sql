CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL16_CLUBS` (
  `ClubID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ClubID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;