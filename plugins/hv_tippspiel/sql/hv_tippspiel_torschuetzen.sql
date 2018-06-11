CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL_TORSCHUETZEN` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=16 ;

INSERT INTO `HV_TIPPSPIEL_TORSCHUETZEN` (`ID`, `Name`) VALUES
(1, 'Neymar'),
(2, 'Lionel Messi'),
(3, 'Antoine Griezmann'),
(4, 'Gabriel Jesus'),
(5, 'Christiano Ronaldo'),
(6, 'Timo Werner'),
(7, 'Harry Kane'),
(8, 'Luis Suarez'),
(9, 'Romelu Lukaku'),
(10, 'Kylian Mbappe'),
(11, 'Thomas Müller'),
(12, 'Edinson Cavani'),
(13, 'Sergio Aguero'),
(14, 'Diego Costa'),
(15, 'Robert Lewandowski');
