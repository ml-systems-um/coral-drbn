ALTER TABLE `Resource` ADD COLUMN `coverageText` VARCHAR(1000) NULL DEFAULT NULL ;
ALTER TABLE `Resource` ADD COLUMN `resourceAltURL` VARCHAR(2000) NULL DEFAULT NULL ;

DROP TABLE IF EXISTS `GeneralSubject`;
CREATE TABLE `GeneralSubject` (
  `generalSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`generalSubjectID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `DetailedSubject`;
CREATE TABLE `DetailedSubject` (
  `detailedSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`detailedSubjectID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `GeneralDetailSubjectLink`;
CREATE TABLE `GeneralDetailSubjectLink` (
  `generalDetailSubjectLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `generalSubjectID` int(11) DEFAULT NULL,
  `detailedSubjectID` int(11) DEFAULT NULL,
  PRIMARY KEY (`generalDetailSubjectLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `ResourceSubject`;
CREATE TABLE `ResourceSubject` (
  `resourceSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `generalDetailSubjectLinkID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceSubjectID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

ALTER TABLE `GeneralSubject` ADD INDEX `generalSubjectID` ( `generalSubjectID` );
ALTER TABLE `DetailedSubject` ADD INDEX `detailedSubjectID` ( `detailedSubjectID` );

ALTER TABLE `GeneralDetailSubjectLink` ADD INDEX `generalDetailSubjectLinkID` ( `generalDetailSubjectLinkID` ),
 ADD INDEX `Index_All` (`generalSubjectID` ASC, `detailedSubjectID` ASC),
 ADD INDEX `Index_generalSubject` (`generalSubjectID` ASC),
 ADD INDEX `Index_detailedSubject` (`detailedSubjectID` ASC) ;

ALTER TABLE `ResourceSubject` ADD INDEX `resourceSubjectID` ( `resourceSubjectID` ),
 ADD INDEX `Index_All` (`resourceID` ASC, `generalDetailSubjectLinkID` ASC),
 ADD INDEX `Index_ResourceID` (`resourceID` ASC),
 ADD INDEX `Index_GeneralDetailLink` (`generalDetailSubjectLinkID` ASC) ;



