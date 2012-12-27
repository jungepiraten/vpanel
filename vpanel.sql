-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 18, 2012 at 07:21 PM
-- Server version: 5.0.51
-- PHP Version: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `vpanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `beitraege`
--

CREATE TABLE IF NOT EXISTS `beitraege` (
  `beitragid` int(11) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  `hoehe` double default NULL,
  `mailtemplateid` int(10) unsigned default NULL,
  PRIMARY KEY  (`beitragid`),
  UNIQUE KEY `label` (`label`),
  KEY `mailtemplateid` (`mailtemplateid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `countryid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(20) NOT NULL,
  PRIMARY KEY  (`countryid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dashboardwidgets`
--

CREATE TABLE IF NOT EXISTS `dashboardwidgets` (
  `widgetid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `column` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `typedata` blob NOT NULL,
  PRIMARY KEY  (`widgetid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokument`
--

CREATE TABLE IF NOT EXISTS `dokument` (
  `dokumentid` int(10) unsigned NOT NULL auto_increment,
  `gliederungid` int(10) unsigned NOT NULL,
  `dokumentkategorieid` int(10) unsigned NOT NULL,
  `dokumentstatusid` int(10) unsigned NOT NULL,
  `identifier` varchar(30) NOT NULL,
  `label` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `data` blob,
  `fileid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`dokumentid`),
  UNIQUE KEY `fileid` (`fileid`),
  UNIQUE KEY `idKey` (`identifier`),
  KEY `dokumentkategorieid` (`dokumentkategorieid`),
  KEY `dokumentstatusid` (`dokumentstatusid`),
  KEY `gliederungid` (`gliederungid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentdokumentflags`
--

CREATE TABLE IF NOT EXISTS `dokumentdokumentflags` (
  `dokumentid` int(10) unsigned NOT NULL,
  `flagid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`dokumentid`,`flagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentflags`
--

CREATE TABLE IF NOT EXISTS `dokumentflags` (
  `flagid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY  (`flagid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentkategorien`
--

CREATE TABLE IF NOT EXISTS `dokumentkategorien` (
  `dokumentkategorieid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY  (`dokumentkategorieid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentnotifies`
--

CREATE TABLE IF NOT EXISTS `dokumentnotifies` (
  `dokumentnotifyid` int(10) unsigned NOT NULL auto_increment,
  `gliederungid` int(10) unsigned default NULL,
  `dokumentkategorieid` int(10) unsigned default NULL,
  `dokumentstatusid` int(10) unsigned default NULL,
  `emailid` int(10) unsigned default NULL,
  PRIMARY KEY  (`dokumentnotifyid`),
  KEY `dokumentkategoriestatus` (`dokumentkategorieid`,`dokumentstatusid`),
  KEY `dokumentkategorieid` (`dokumentkategorieid`),
  KEY `dokumentstatusid` (`dokumentstatusid`),
  KEY `emailid` (`emailid`),
  KEY `gliederungid` (`gliederungid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentnotizen`
--

CREATE TABLE IF NOT EXISTS `dokumentnotizen` (
  `dokumentnotizid` int(10) unsigned NOT NULL auto_increment,
  `dokumentid` int(10) unsigned NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `nextState` int(10) unsigned default NULL,
  `nextKategorie` int(10) unsigned default NULL,
  `nextLabel` varchar(50) default NULL,
  `nextIdentifier` varchar(30) default NULL,
  `kommentar` text NOT NULL,
  PRIMARY KEY  (`dokumentnotizid`),
  KEY `dokumentid` (`dokumentid`),
  KEY `author` (`author`),
  KEY `nextState` (`nextState`),
  KEY `nextKategorie` (`nextKategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentnotizflags`
--

CREATE TABLE IF NOT EXISTS `dokumentnotizflags` (
  `notizid` int(10) unsigned NOT NULL,
  `flagid` int(10) unsigned NOT NULL,
  `change` enum('add','del') NOT NULL,
  PRIMARY KEY  (`notizid`,`flagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dokumentstatus`
--

CREATE TABLE IF NOT EXISTS `dokumentstatus` (
  `dokumentstatusid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY  (`dokumentstatusid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `emailbounces`
--

CREATE TABLE IF NOT EXISTS `emailbounces` (
  `bounceid` int(10) unsigned NOT NULL auto_increment,
  `emailid` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  PRIMARY KEY  (`bounceid`),
  KEY `emailid` (`emailid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `emailid` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`emailid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `fileid` int(10) unsigned NOT NULL auto_increment,
  `filename` varchar(50) NOT NULL,
  `exportfilename` varchar(50) NOT NULL,
  `mimetype` varchar(25) NOT NULL default 'application/octet-stream',
  PRIMARY KEY  (`fileid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gliederungen`
--

CREATE TABLE IF NOT EXISTS `gliederungen` (
  `gliederungsid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  `parentid` int(10) unsigned default NULL,
  PRIMARY KEY  (`gliederungsid`),
  KEY `parentid` (`parentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jurperson`
--

CREATE TABLE IF NOT EXISTS `jurperson` (
  `jurpersonid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY  (`jurpersonid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kontakte`
--

CREATE TABLE IF NOT EXISTS `kontakte` (
  `kontaktid` int(10) unsigned NOT NULL auto_increment,
  `adresszusatz` varchar(70) NOT NULL,
  `strasse` varchar(40) NOT NULL,
  `hausnummer` varchar(10) NOT NULL,
  `ortid` int(10) unsigned NOT NULL,
  `telefonnummer` varchar(15) NOT NULL,
  `handynummer` varchar(15) NOT NULL,
  `emailid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`kontaktid`),
  KEY `ortid` (`ortid`),
  KEY `emailid` (`emailid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mailtemplateattachments`
--

CREATE TABLE IF NOT EXISTS `mailtemplateattachments` (
  `templateid` int(10) unsigned NOT NULL,
  `fileid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`templateid`,`fileid`),
  KEY `templateid` (`templateid`),
  KEY `fileid` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mailtemplateheaders`
--

CREATE TABLE IF NOT EXISTS `mailtemplateheaders` (
  `templateid` int(10) unsigned NOT NULL,
  `field` varchar(30) NOT NULL,
  `value` varchar(70) NOT NULL,
  PRIMARY KEY  (`templateid`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mailtemplates`
--

CREATE TABLE IF NOT EXISTS `mailtemplates` (
  `templateid` int(10) unsigned NOT NULL auto_increment,
  `gliederungid` int(10) unsigned NOT NULL,
  `label` varchar(50) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`templateid`),
  UNIQUE KEY `label` (`label`),
  KEY `gliederungid` (`gliederungid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitglieddokument`
--

CREATE TABLE IF NOT EXISTS `mitglieddokument` (
  `mitgliedid` int(10) unsigned NOT NULL,
  `dokumentid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`mitgliedid`,`dokumentid`),
  KEY `mitgliedid` (`mitgliedid`),
  KEY `dokumentid` (`dokumentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitglieder`
--

CREATE TABLE IF NOT EXISTS `mitglieder` (
  `mitgliedid` int(10) unsigned NOT NULL auto_increment,
  `globalid` varchar(50) NOT NULL,
  `eintritt` date NOT NULL,
  `austritt` date default NULL,
  PRIMARY KEY  (`mitgliedid`),
  UNIQUE KEY `globalid` (`globalid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliederbeitrag`
--

CREATE TABLE IF NOT EXISTS `mitgliederbeitrag` (
  `mitgliederbeitragid` int(10) unsigned NOT NULL auto_increment,
  `mitgliedid` int(10) unsigned NOT NULL,
  `beitragid` int(10) unsigned NOT NULL,
  `hoehe` double NOT NULL,
  PRIMARY KEY  (`mitgliederbeitragid`),
  KEY `mitgliedid` (`mitgliedid`),
  KEY `beitragid` (`beitragid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliederbeitragbuchung`
--

CREATE TABLE IF NOT EXISTS `mitgliederbeitragbuchung` (
  `buchungid` int(10) unsigned NOT NULL auto_increment,
  `beitragid` int(10) unsigned NOT NULL,
  `gliederungid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned default NULL,
  `timestamp` date default NULL,
  `vermerk` text NOT NULL,
  `hoehe` double NOT NULL,
  PRIMARY KEY  (`buchungid`),
  KEY `gliederungid` (`gliederungid`),
  KEY `beitragid` (`beitragid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliederflags`
--

CREATE TABLE IF NOT EXISTS `mitgliederflags` (
  `flagid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY  (`flagid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliedernotizen`
--

CREATE TABLE IF NOT EXISTS `mitgliedernotizen` (
  `mitgliednotizid` int(10) unsigned NOT NULL auto_increment,
  `mitgliedid` int(10) unsigned NOT NULL,
  `author` int(10) unsigned default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `kommentar` text NOT NULL,
  PRIMARY KEY  (`mitgliednotizid`),
  KEY `author` (`author`),
  KEY `mitgliedid` (`mitgliedid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliederrevisionflags`
--

CREATE TABLE IF NOT EXISTS `mitgliederrevisionflags` (
  `revisionid` int(10) unsigned NOT NULL,
  `flagid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`revisionid`,`flagid`),
  KEY `flagid` (`flagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliederrevisions`
--

CREATE TABLE IF NOT EXISTS `mitgliederrevisions` (
  `revisionid` int(10) unsigned NOT NULL auto_increment,
  `globaleid` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `userid` int(10) unsigned default NULL,
  `mitgliedid` int(10) unsigned NOT NULL,
  `mitgliedschaftid` int(10) unsigned NOT NULL,
  `gliederungsid` int(10) unsigned default NULL,
  `geloescht` tinyint(1) NOT NULL default '0',
  `beitrag` double unsigned NOT NULL,
  `natpersonid` int(10) unsigned default NULL,
  `jurpersonid` int(10) unsigned default NULL,
  `kontaktid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`revisionid`),
  UNIQUE KEY `globaleid` (`globaleid`),
  KEY `mitgliedid` (`mitgliedid`),
  KEY `userid` (`userid`),
  KEY `mitgliedschaftid` (`mitgliedschaftid`),
  KEY `gliederungsid` (`gliederungsid`),
  KEY `natpersonid` (`natpersonid`),
  KEY `jurpersonid` (`jurpersonid`),
  KEY `kontaktid` (`kontaktid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliederrevisiontextfields`
--

CREATE TABLE IF NOT EXISTS `mitgliederrevisiontextfields` (
  `revisionid` int(10) unsigned NOT NULL,
  `textfieldid` int(10) unsigned NOT NULL,
  `value` text,
  PRIMARY KEY  (`revisionid`,`textfieldid`),
  KEY `revisionid` (`revisionid`),
  KEY `textfieldid` (`textfieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliedertextfields`
--

CREATE TABLE IF NOT EXISTS `mitgliedertextfields` (
  `textfieldid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY  (`textfieldid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mitgliedschaften`
--

CREATE TABLE IF NOT EXISTS `mitgliedschaften` (
  `mitgliedschaftid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY  (`mitgliedschaftid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `natperson`
--

CREATE TABLE IF NOT EXISTS `natperson` (
  `natpersonid` int(10) unsigned NOT NULL auto_increment,
  `anrede` varchar(15) NOT NULL,
  `name` varchar(30) NOT NULL,
  `vorname` varchar(35) NOT NULL,
  `geburtsdatum` date NOT NULL,
  `nationalitaet` varchar(20) NOT NULL,
  PRIMARY KEY  (`natpersonid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orte`
--

CREATE TABLE IF NOT EXISTS `orte` (
  `ortid` int(10) unsigned NOT NULL auto_increment,
  `plz` varchar(5) NOT NULL,
  `label` varchar(30) NOT NULL,
  `latitude` double default NULL,
  `longitude` double default NULL,
  `stateid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`ortid`),
  KEY `stateid` (`stateid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `permissionid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(20) NOT NULL,
  `description` text,
  `global` tinyint(1) NOT NULL,
  PRIMARY KEY  (`permissionid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `processes`
--

CREATE TABLE IF NOT EXISTS `processes` (
  `processid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned default NULL,
  `type` varchar(50) NOT NULL,
  `typedata` blob NOT NULL,
  `progress` double NOT NULL,
  `queued` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `started` timestamp NULL default NULL,
  `finished` timestamp NULL default NULL,
  `finishedpage` text NOT NULL,
  PRIMARY KEY  (`processid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rolepermissions`
--

CREATE TABLE IF NOT EXISTS `rolepermissions` (
  `roleid` int(10) unsigned NOT NULL,
  `permissionid` int(10) unsigned NOT NULL,
  `gliederungid` int(10) unsigned default NULL,
  `transitive` tinyint(1) NOT NULL,
  KEY `permissionid` (`permissionid`),
  KEY `gliederungid` (`gliederungid`),
  KEY `roleid` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `roleid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY  (`roleid`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `sessionid` int(10) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `data` blob NOT NULL,
  PRIMARY KEY  (`sessionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `stateid` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(25) NOT NULL,
  `population` int(10) unsigned default NULL,
  `countryid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`stateid`),
  KEY `countryid` (`countryid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tempfiles`
--

CREATE TABLE IF NOT EXISTS `tempfiles` (
  `tempfileid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `fileid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`tempfileid`),
  UNIQUE KEY `fileid` (`fileid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userroles`
--

CREATE TABLE IF NOT EXISTS `userroles` (
  `userid` int(10) unsigned NOT NULL,
  `roleid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`userid`,`roleid`),
  KEY `roleid` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `password` char(64) NOT NULL,
  `passwordsalt` varchar(15) NOT NULL,
  `apikey` varchar(50) default NULL,
  `aktiv` tinyint(1) NOT NULL,
  `defaultgliederungid` int(10) unsigned default NULL,
  `defaultdokumentkategorieid` int(10) unsigned default NULL,
  `defaultdokumentstatusid` int(10) unsigned default NULL,
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `apikey` (`apikey`),
  KEY `defaultdokumentkategorieid` (`defaultdokumentkategorieid`),
  KEY `defaultdokumentstatusid` (`defaultdokumentstatusid`),
  KEY `defaultgliederungid` (`defaultgliederungid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beitraege`
--
ALTER TABLE `beitraege`
  ADD CONSTRAINT `beitraege_ibfk_1` FOREIGN KEY (`mailtemplateid`) REFERENCES `mailtemplates` (`templateid`);

--
-- Constraints for table `dashboardwidgets`
--
ALTER TABLE `dashboardwidgets`
  ADD CONSTRAINT `dashboardwidgets_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dokument`
--
ALTER TABLE `dokument`
  ADD CONSTRAINT `dokument_ibfk_1` FOREIGN KEY (`dokumentkategorieid`) REFERENCES `dokumentkategorien` (`dokumentkategorieid`),
  ADD CONSTRAINT `dokument_ibfk_2` FOREIGN KEY (`dokumentstatusid`) REFERENCES `dokumentstatus` (`dokumentstatusid`),
  ADD CONSTRAINT `dokument_ibfk_4` FOREIGN KEY (`fileid`) REFERENCES `files` (`fileid`),
  ADD CONSTRAINT `dokument_ibfk_5` FOREIGN KEY (`gliederungid`) REFERENCES `gliederungen` (`gliederungsid`);

--
-- Constraints for table `dokumentnotifies`
--
ALTER TABLE `dokumentnotifies`
  ADD CONSTRAINT `dokumentnotifies_ibfk_3` FOREIGN KEY (`dokumentkategorieid`) REFERENCES `dokumentkategorien` (`dokumentkategorieid`),
  ADD CONSTRAINT `dokumentnotifies_ibfk_4` FOREIGN KEY (`dokumentstatusid`) REFERENCES `dokumentstatus` (`dokumentstatusid`),
  ADD CONSTRAINT `dokumentnotifies_ibfk_5` FOREIGN KEY (`emailid`) REFERENCES `emails` (`emailid`),
  ADD CONSTRAINT `dokumentnotifies_ibfk_6` FOREIGN KEY (`gliederungid`) REFERENCES `gliederungen` (`gliederungsid`);

--
-- Constraints for table `dokumentnotizen`
--
ALTER TABLE `dokumentnotizen`
  ADD CONSTRAINT `dokumentnotizen_ibfk_1` FOREIGN KEY (`dokumentid`) REFERENCES `dokument` (`dokumentid`),
  ADD CONSTRAINT `dokumentnotizen_ibfk_2` FOREIGN KEY (`author`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `dokumentnotizen_ibfk_3` FOREIGN KEY (`nextState`) REFERENCES `dokumentstatus` (`dokumentstatusid`),
  ADD CONSTRAINT `dokumentnotizen_ibfk_4` FOREIGN KEY (`nextKategorie`) REFERENCES `dokumentkategorien` (`dokumentkategorieid`);

--
-- Constraints for table `emailbounces`
--
ALTER TABLE `emailbounces`
  ADD CONSTRAINT `emailbounces_ibfk_1` FOREIGN KEY (`emailid`) REFERENCES `emails` (`emailid`);

--
-- Constraints for table `gliederungen`
--
ALTER TABLE `gliederungen`
  ADD CONSTRAINT `gliederungen_ibfk_1` FOREIGN KEY (`parentid`) REFERENCES `gliederungen` (`gliederungsid`);

--
-- Constraints for table `mailtemplateattachments`
--
ALTER TABLE `mailtemplateattachments`
  ADD CONSTRAINT `mailtemplateattachments_ibfk_1` FOREIGN KEY (`templateid`) REFERENCES `mailtemplates` (`templateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mailtemplateattachments_ibfk_2` FOREIGN KEY (`fileid`) REFERENCES `files` (`fileid`);

--
-- Constraints for table `mailtemplateheaders`
--
ALTER TABLE `mailtemplateheaders`
  ADD CONSTRAINT `mailtemplateheaders_ibfk_1` FOREIGN KEY (`templateid`) REFERENCES `mailtemplates` (`templateid`);

--
-- Constraints for table `mailtemplates`
--
ALTER TABLE `mailtemplates`
  ADD CONSTRAINT `mailtemplates_ibfk_1` FOREIGN KEY (`gliederungid`) REFERENCES `gliederungen` (`gliederungsid`);

--
-- Constraints for table `mitglieddokument`
--
ALTER TABLE `mitglieddokument`
  ADD CONSTRAINT `mitglieddokument_ibfk_1` FOREIGN KEY (`mitgliedid`) REFERENCES `mitglieder` (`mitgliedid`),
  ADD CONSTRAINT `mitglieddokument_ibfk_2` FOREIGN KEY (`dokumentid`) REFERENCES `dokument` (`dokumentid`);

--
-- Constraints for table `mitgliederbeitrag`
--
ALTER TABLE `mitgliederbeitrag`
  ADD CONSTRAINT `mitgliederbeitrag_ibfk_1` FOREIGN KEY (`mitgliedid`) REFERENCES `mitglieder` (`mitgliedid`),
  ADD CONSTRAINT `mitgliederbeitrag_ibfk_2` FOREIGN KEY (`beitragid`) REFERENCES `beitraege` (`beitragid`);

--
-- Constraints for table `mitgliederbeitragbuchung`
--
ALTER TABLE `mitgliederbeitragbuchung`
  ADD CONSTRAINT `mitgliederbeitragbuchung_ibfk_3` FOREIGN KEY (`gliederungid`) REFERENCES `gliederungen` (`gliederungsid`),
  ADD CONSTRAINT `mitgliederbeitragbuchung_ibfk_4` FOREIGN KEY (`beitragid`) REFERENCES `mitgliederbeitrag` (`mitgliederbeitragid`),
  ADD CONSTRAINT `mitgliederbeitragbuchung_ibfk_5` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `mitgliedernotizen`
--
ALTER TABLE `mitgliedernotizen`
  ADD CONSTRAINT `mitgliedernotizen_ibfk_1` FOREIGN KEY (`mitgliedid`) REFERENCES `mitglieder` (`mitgliedid`),
  ADD CONSTRAINT `mitgliedernotizen_ibfk_2` FOREIGN KEY (`author`) REFERENCES `users` (`userid`);

--
-- Constraints for table `mitgliederrevisionflags`
--
ALTER TABLE `mitgliederrevisionflags`
  ADD CONSTRAINT `mitgliederrevisionflags_ibfk_1` FOREIGN KEY (`revisionid`) REFERENCES `mitgliederrevisions` (`revisionid`),
  ADD CONSTRAINT `mitgliederrevisionflags_ibfk_2` FOREIGN KEY (`flagid`) REFERENCES `mitgliederflags` (`flagid`);

--
-- Constraints for table `mitgliederrevisions`
--
ALTER TABLE `mitgliederrevisions`
  ADD CONSTRAINT `mitgliederrevisions_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `mitgliederrevisions_ibfk_2` FOREIGN KEY (`mitgliedid`) REFERENCES `mitglieder` (`mitgliedid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mitgliederrevisions_ibfk_3` FOREIGN KEY (`gliederungsid`) REFERENCES `gliederungen` (`gliederungsid`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `mitgliederrevisions_ibfk_5` FOREIGN KEY (`mitgliedschaftid`) REFERENCES `mitgliedschaften` (`mitgliedschaftid`),
  ADD CONSTRAINT `mitgliederrevisions_ibfk_8` FOREIGN KEY (`kontaktid`) REFERENCES `kontakte` (`kontaktid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mitgliederrevisiontextfields`
--
ALTER TABLE `mitgliederrevisiontextfields`
  ADD CONSTRAINT `mitgliederrevisiontextfields_ibfk_1` FOREIGN KEY (`revisionid`) REFERENCES `mitgliederrevisions` (`revisionid`),
  ADD CONSTRAINT `mitgliederrevisiontextfields_ibfk_2` FOREIGN KEY (`textfieldid`) REFERENCES `mitgliedertextfields` (`textfieldid`);

--
-- Constraints for table `orte`
--
ALTER TABLE `orte`
  ADD CONSTRAINT `orte_ibfk_1` FOREIGN KEY (`stateid`) REFERENCES `states` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `processes`
--
ALTER TABLE `processes`
  ADD CONSTRAINT `processes_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `rolepermissions`
--
ALTER TABLE `rolepermissions`
  ADD CONSTRAINT `rolepermissions_ibfk_1` FOREIGN KEY (`roleid`) REFERENCES `roles` (`roleid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rolepermissions_ibfk_2` FOREIGN KEY (`permissionid`) REFERENCES `permissions` (`permissionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rolepermissions_ibfk_3` FOREIGN KEY (`gliederungid`) REFERENCES `gliederungen` (`gliederungsid`);

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_ibfk_1` FOREIGN KEY (`countryid`) REFERENCES `countries` (`countryid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tempfiles`
--
ALTER TABLE `tempfiles`
  ADD CONSTRAINT `tempfiles_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `tempfiles_ibfk_2` FOREIGN KEY (`fileid`) REFERENCES `files` (`fileid`);

--
-- Constraints for table `userroles`
--
ALTER TABLE `userroles`
  ADD CONSTRAINT `userroles_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userroles_ibfk_2` FOREIGN KEY (`roleid`) REFERENCES `roles` (`roleid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`defaultdokumentkategorieid`) REFERENCES `dokumentkategorien` (`dokumentkategorieid`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_4` FOREIGN KEY (`defaultdokumentstatusid`) REFERENCES `dokumentstatus` (`dokumentstatusid`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`countryid`, `label`) VALUES
(1, 'Deutschland');

--
-- Dumping data for table `gliederungen`
--

INSERT INTO `gliederungen` (`gliederungsid`, `label`, `parentid`) VALUES
(1, 'Bundesverband', NULL),
(2, 'Trollgliederung', NULL);

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permissionid`, `label`, `description`, `global`) VALUES
(1, 'users_show', NULL, 1),
(2, 'users_modify', NULL, 1),
(3, 'users_create', NULL, 1),
(4, 'roles_show', NULL, 1),
(5, 'roles_modify', NULL, 1),
(6, 'roles_create', NULL, 1),
(7, 'users_delete', NULL, 1),
(8, 'roles_delete', NULL, 1),
(9, 'mitglieder_show', NULL, 0),
(10, 'mitglieder_modify', NULL, 0),
(11, 'mitglieder_create', NULL, 0),
(12, 'mitglieder_delete', NULL, 0),
(13, 'stats_show', NULL, 0),
(14, 'mailtemplates_show', NULL, 0),
(15, 'mailtemplates_create', NULL, 0),
(16, 'mailtemplates_modify', NULL, 0),
(17, 'mailtemplates_delete', NULL, 0),
(18, 'dokumente_show', NULL, 0),
(19, 'dokumente_modify', NULL, 0),
(20, 'dokumente_create', NULL, 0),
(21, 'beitraege_show', NULL, 1),
(22, 'beitraege_create', NULL, 1),
(23, 'beitraege_modify', NULL, 1),
(24, 'beitraege_delete', NULL, 1),
(25, 'mitglieder_moveto', NULL, 0),
(26, 'mitglieder_beitrag', NULL, 0),
(27, 'dokumente_delete', NULL, 0);

--
-- Dumping data for table `rolepermissions`
--

INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `gliederungid`, `transitive`) VALUES
(7, 1, NULL, 0),
(7, 2, NULL, 0),
(7, 3, NULL, 0),
(7, 4, NULL, 0),
(7, 5, NULL, 0),
(7, 6, NULL, 0),
(7, 7, NULL, 0),
(7, 8, NULL, 0),
(8, 9, 1, 1),
(8, 10, 1, 1),
(8, 11, 1, 1),
(8, 18, 1, 1),
(8, 19, 1, 1),
(8, 20, 1, 1),
(8, 16, 1, 0),
(8, 17, 1, 0),
(8, 25, 1, 1),
(8, 26, 1, 1),
(8, 9, 2, 0),
(8, 10, 2, 0),
(8, 11, 2, 0),
(8, 12, 2, 0),
(8, 13, 2, 0),
(8, 14, 2, 0),
(8, 15, 2, 0),
(8, 16, 2, 0),
(8, 17, 2, 0),
(8, 18, 2, 0),
(8, 19, 2, 0),
(8, 20, 2, 0),
(8, 25, 2, 0),
(8, 26, 2, 0),
(8, 12, 1, 1),
(8, 13, 1, 1),
(8, 14, 1, 0),
(8, 15, 1, 0),
(8, 21, NULL, 0),
(8, 22, NULL, 0),
(8, 23, NULL, 0),
(8, 24, NULL, 0),
(8, 27, 1, 1);

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleid`, `label`, `description`) VALUES
(6, 'User', 'Grundlegende Rechte für alle Benutzer'),
(7, 'Administrator', 'Systemadministratoren zur Verwaltung von Zugriffsrechten'),
(8, 'Mensch', '');

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`stateid`, `label`, `population`, `countryid`) VALUES
(1, 'Baden-Württemberg', 10745000, 1),
(2, 'Bayern', 12510000, 1),
(3, 'Berlin', 3443000, 1),
(4, 'Brandenburg', 2512000, 1),
(5, 'Bremen', 662000, 1),
(6, 'Hamburg', 1774000, 1),
(7, 'Hessen', 6062000, 1),
(8, 'Mecklenburg-Vorpommern', 1651000, 1),
(9, 'Niedersachsen', 7929000, 1),
(10, 'Nordrhein-Westfalen', 17873000, 1),
(11, 'Rheinland-Pfalz', 4013000, 1),
(12, 'Saarland', 1023000, 1),
(13, 'Sachsen', 4169000, 1),
(14, 'Sachsen-Anhalt', 2356000, 1),
(15, 'Schleswig-Holstein', 2832000, 1),
(16, 'Thüringen', 2250000, 1);

--
-- Dumping data for table `userroles`
--

INSERT INTO `userroles` (`userid`, `roleid`) VALUES
(1, 6),
(1, 7),
(1, 8);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `password`, `passwordsalt`, `apikey`, `aktiv`, `defaultgliederungid`, `defaultdokumentkategorieid`, `defaultdokumentstatusid`) VALUES
(1, 'admin', 'dfd51bf440807bf20c3b3d0eee11929cc0f701b3280c7fe20fbe1bd135054e8d', '*I;V,', NULL, 1, 1, NULL, NULL);

-- Username: admin / admin

-- Update 2012-12-16

ALTER TABLE  `kontakte` ADD  `iban` VARCHAR( 34 ) NULL;

-- Update 2012-12-23

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dokumentrevisions` (
  `revisionid` int(10) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `userid` int(10) unsigned default NULL,
  `dokumentid` int(10) unsigned NOT NULL,
  `gliederungid` int(10) unsigned NOT NULL,
  `kategorieid` int(10) unsigned NOT NULL,
  `statusid` int(10) unsigned NOT NULL,
  `identifier` varchar(30) NOT NULL,
  `label` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `data` blob,
  `fileid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`revisionid`),
  KEY `userid` (`userid`),
  KEY `dokumentid` (`dokumentid`),
  KEY `gliederungid` (`gliederungid`),
  KEY `kategorieid` (`kategorieid`),
  KEY `statusid` (`statusid`),
  KEY `identifier` (`identifier`),
  KEY `fileid` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `dokumentrevisions`
  ADD CONSTRAINT `dokumentrevisions_ibfk_6` FOREIGN KEY (`fileid`) REFERENCES `files` (`fileid`),
  ADD CONSTRAINT `dokumentrevisions_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `dokumentrevisions_ibfk_2` FOREIGN KEY (`dokumentid`) REFERENCES `dokument` (`dokumentid`),
  ADD CONSTRAINT `dokumentrevisions_ibfk_3` FOREIGN KEY (`gliederungid`) REFERENCES `gliederungen` (`gliederungsid`),
  ADD CONSTRAINT `dokumentrevisions_ibfk_4` FOREIGN KEY (`kategorieid`) REFERENCES `dokumentkategorien` (`dokumentkategorieid`),
  ADD CONSTRAINT `dokumentrevisions_ibfk_5` FOREIGN KEY (`statusid`) REFERENCES `dokumentstatus` (`dokumentstatusid`);

ALTER TABLE  `dokumentrevisions` ADD  `kommentar` TEXT NOT NULL;

CREATE TABLE  `vpanel`.`dokumentrevisionflags` (
`revisionid` INT UNSIGNED NOT NULL ,
`flagid` INT UNSIGNED NOT NULL ,
PRIMARY KEY (  `revisionid` ,  `flagid` )
) ENGINE = INNODB;

ALTER TABLE  `dokumentrevisionflags` ADD FOREIGN KEY (  `revisionid` ) REFERENCES  `vpanel`.`dokumentrevisions` (
`revisionid`
);

ALTER TABLE  `dokumentrevisionflags` ADD FOREIGN KEY (  `flagid` ) REFERENCES  `vpanel`.`dokumentflags` (
`flagid`
);

UPDATE `dashboardwidgets` SET `type` = 'DokumentRevisionTimelineDashboardWidget' WHERE `type` = 'DokumentNotizenTimelineDashboardWidget';

-- (After migration)

DROP TABLE `dokumentdokumentflags`, `dokumentnotizen`, `dokumentnotizflags`;

ALTER TABLE  `dokument` DROP FOREIGN KEY  `dokument_ibfk_5` ;

ALTER TABLE  `dokument` DROP FOREIGN KEY  `dokument_ibfk_1` ;

ALTER TABLE  `dokument` DROP FOREIGN KEY  `dokument_ibfk_2` ;

ALTER TABLE  `dokument` DROP FOREIGN KEY  `dokument_ibfk_4` ;

ALTER TABLE `dokument`
  DROP `gliederungid`,
  DROP `dokumentkategorieid`,
  DROP `dokumentstatusid`,
  DROP `identifier`,
  DROP `label`,
  DROP `content`,
  DROP `data`,
  DROP `fileid`;

-- Update 2012-12-24

CREATE TABLE  `vpanel`.`beitragtimeformat` (
`beitragtimeformatid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`label` VARCHAR( 50 ) NOT NULL ,
`format` VARCHAR( 30 ) NOT NULL ,
UNIQUE (
`label`
)
) ENGINE = INNODB;

INSERT INTO  `vpanel`.`beitragtimeformat` (
`beitragtimeformatid` ,
`label` ,
`format`
)
VALUES (
NULL ,  'jährlich',  'Jahresbeitrag %Y'
);

ALTER TABLE  `mitgliederrevisions` ADD  `beitragtimeformatid` INT UNSIGNED NOT NULL AFTER  `beitrag` ,
ADD INDEX (  `beitragtimeformatid` );

UPDATE `mitgliederrevisions` SET `beitragtimeformatid` = 1;

ALTER TABLE  `mitgliederrevisions` ADD FOREIGN KEY (  `beitragtimeformatid` ) REFERENCES  `vpanel`.`beitragtimeformat` (
`beitragtimeformatid`
);

UPDATE `beitraege` SET `label` = 'Jahresbeitrag 2010' WHERE `label` = '2010';
UPDATE `beitraege` SET `label` = 'Jahresbeitrag 2011' WHERE `label` = '2011';
UPDATE `beitraege` SET `label` = 'Jahresbeitrag 2012' WHERE `label` = '2012';

-- update 2012-12-26

ALTER TABLE  `mitgliederrevisions` ADD  `kommentar` TEXT NOT NULL;

-- after update

DROP TABLE `dokumentnotizen`;

-- update 2012-12-27

ALTER TABLE  `mitgliederbeitragbuchung` DROP FOREIGN KEY  `mitgliederbeitragbuchung_ibfk_5` ;
