CREATE TABLE IF NOT EXISTS `HV_TIPPSPIEL_TORSCHUETZEN` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=11 ;

INSERT INTO `HV_TIPPSPIEL_TORSCHUETZEN` (`ID`, `Name`) VALUES
(1, 'Thomas Müller'),
(2, 'Christiano Ronaldo'),
(3, 'Antoine Griezmann'),
(4, 'Harry Kane'),
(5, 'Olivier Giroud'),
(6, 'Robert Lewandowski'),
(7, 'Romelu Lukaku'),
(8, 'Alvaro Morata'),
(9, 'Mario Gomez'),
(10, 'Anthony Martial');
