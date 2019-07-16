<?php

			
$sql ="
CREATE TABLE IF NOT EXISTS `c_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `morder` smallint(4) NOT NULL,
  `level` tinyint(2) NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT '',
  `onclick` text,
  `tooltip` varchar(250) NOT NULL DEFAULT '',
  `image_file` blob NOT NULL,
  `facility` char(2) NOT NULL DEFAULT '',
  `a1` char(1) NOT NULL DEFAULT 'N',
  `a2` char(1) NOT NULL DEFAULT 'N',
  `a3` char(1) NOT NULL DEFAULT 'N',
  `a4` char(1) NOT NULL DEFAULT 'N',
  `a5` char(1) NOT NULL DEFAULT 'N',
  `a6` char(1) NOT NULL DEFAULT 'N',
  `a7` char(1) NOT NULL DEFAULT 'N',
  `a8` char(1) NOT NULL DEFAULT 'N',
  `a9` char(1) NOT NULL DEFAULT 'N',
  `a10` char(1) NOT NULL DEFAULT 'N',
  `a11` char(1) NOT NULL DEFAULT 'N',
  `a12` char(1) NOT NULL DEFAULT 'N',
  `a13` char(1) NOT NULL DEFAULT 'N',
  `a14` char(1) NOT NULL DEFAULT 'N',
  `a15` char(1) NOT NULL DEFAULT 'N',
  `a16` char(1) NOT NULL DEFAULT 'N',
  `a17` char(1) NOT NULL DEFAULT 'N',
  `a18` char(1) NOT NULL DEFAULT 'N',
  `a19` char(1) NOT NULL DEFAULT 'N',
  `a20` char(1) NOT NULL DEFAULT 'Y',
  `std_sub` char(1) NOT NULL DEFAULT 'N',
  `extra_1` char(1) NOT NULL DEFAULT 'N',
  `extra_2` char(1) NOT NULL DEFAULT 'N',
  `extra_3` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 " ;

$r = mysql_query($sql) or die(mysql_error().' '.$sql);

/*
$sql ="
CREATE TABLE IF NOT EXISTS `a_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `morder` smallint(4) NOT NULL,
  `level` tinyint(2) NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT '',
  `onclick` text,
  `tooltip` varchar(250) NOT NULL DEFAULT '',
  `image_file` blob NOT NULL,
  `facility` char(2) NOT NULL DEFAULT '',
  `a1` char(1) NOT NULL DEFAULT 'N',
  `a2` char(1) NOT NULL DEFAULT 'N',
  `a3` char(1) NOT NULL DEFAULT 'N',
  `a4` char(1) NOT NULL DEFAULT 'N',
  `a5` char(1) NOT NULL DEFAULT 'N',
  `a6` char(1) NOT NULL DEFAULT 'N',
  `a7` char(1) NOT NULL DEFAULT 'N',
  `a8` char(1) NOT NULL DEFAULT 'N',
  `a9` char(1) NOT NULL DEFAULT 'N',
  `a10` char(1) NOT NULL DEFAULT 'N',
  `a11` char(1) NOT NULL DEFAULT 'N',
  `a12` char(1) NOT NULL DEFAULT 'N',
  `a13` char(1) NOT NULL DEFAULT 'N',
  `a14` char(1) NOT NULL DEFAULT 'N',
  `a15` char(1) NOT NULL DEFAULT 'N',
  `a16` char(1) NOT NULL DEFAULT 'N',
  `a17` char(1) NOT NULL DEFAULT 'N',
  `a18` char(1) NOT NULL DEFAULT 'N',
  `a19` char(1) NOT NULL DEFAULT 'N',
  `a20` char(1) NOT NULL DEFAULT 'Y',
  `std_sub` char(1) NOT NULL DEFAULT 'N',
  `extra_1` char(1) NOT NULL DEFAULT 'N',
  `extra_2` char(1) NOT NULL DEFAULT 'N',
  `extra_3` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 " ;

$r = mysql_query($sql) or die(mysql_error().' '.$sql);

*/

$sql = "CREATE TABLE IF NOT EXISTS `access` (
  `access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL DEFAULT '0',
  `module` char(3) NOT NULL,
  `usergroup` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`access_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql = "
CREATE TABLE IF NOT EXISTS `activities` (
  `activities_id` int(11) NOT NULL AUTO_INCREMENT,
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `ttime` char(10) NOT NULL,
  `activity` text NOT NULL,
  `staff_id` int(10) unsigned NOT NULL DEFAULT '0',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(25) NOT NULL,
  `contact` varchar(30) NOT NULL DEFAULT 'Other',
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`activities_id`),
  KEY `staff_id` (`staff_id`),
  KEY `member_id` (`member_id`),
  KEY `sub_id` (`sub_id`),
  FULLTEXT KEY `activity` (`activity`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql="
CREATE TABLE IF NOT EXISTS `activity_status` (
  `activity_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_status` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`activity_status_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `addresses` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `location` varchar(15) CHARACTER SET latin1 NOT NULL,
  `address_type_id` int(10) unsigned NOT NULL,
  `street_no` varchar(45) CHARACTER SET latin1 NOT NULL,
  `ad1` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `ad2` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `suburb` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `town` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `state` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `country` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `postcode` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `preferredp` char(1) CHARACTER SET latin1 NOT NULL DEFAULT 'N',
  `preferredv` char(1) CHARACTER SET latin1 NOT NULL DEFAULT 'N',
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`address_id`),
  KEY `member_id` (`member_id`),
  KEY `suburb` (`suburb`),
  KEY `town` (`town`),
  KEY `sub_id` (`sub_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `address_type` (
  `address_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address_type` varchar(45) NOT NULL,
  PRIMARY KEY (`address_type_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `assoc_xref` (
  `assoc_xref_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `association` varchar(30) NOT NULL,
  `of_id` int(11) NOT NULL DEFAULT '0',
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`assoc_xref_id`),
  KEY `member_id` (`member_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `attachments` (
  `doc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ddate` date NOT NULL,
  `doc` varchar(45) NOT NULL,
  `staff` varchar(45) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`doc_id`)
) ENGINE=myisam DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `audit` (
  `audit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `ttime` char(5) NOT NULL DEFAULT '00:00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `uname` varchar(45) NOT NULL,
  `userip` varchar(30) NOT NULL,
  `sub_id` tinyint(4) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `action` varchar(70) NOT NULL,
  `address_id` int(11) NOT NULL DEFAULT '0',
  `comms_id` int(11) NOT NULL DEFAULT '0',
  `activities_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`audit_id`),
  KEY `ddate` (`ddate`),
  KEY `user_id` (`user_id`),
  KEY `sub_id` (`sub_id`),
  KEY `member_id` (`member_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `boxes` (
  `box_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_office` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `from` mediumint(9) NOT NULL,
  `to` mediumint(9) NOT NULL,
  `postcode` varchar(5) NOT NULL,
  `boxbag` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`box_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `campaigns` (
  `campaign_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `startdate` date NOT NULL DEFAULT '0000-00-00',
  `advisor` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `goals` text NOT NULL,
  `outprov_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaign_id`),
  KEY `name` (`name`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `campaign_costs` (
  `costs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL DEFAULT '0',
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `item` varchar(70) NOT NULL,
  `cost` decimal(16,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`costs_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=myisam DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `campaign_docs` (
  `campdoc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL DEFAULT '0',
  `ddate` date NOT NULL,
  `doc` varchar(45) NOT NULL,
  `staff` varchar(45) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`campdoc_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=myisam DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `candidates` (
  `candidate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `campaign_id` int(11) NOT NULL DEFAULT '0',
  `cand_status` char(1) NOT NULL DEFAULT 'A',
  `staff_id` int(11) NOT NULL DEFAULT '0',
  `lastname` varchar(45) NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `preferred` varchar(50) NOT NULL,
  `suburb` varchar(45) NOT NULL,
  `advisor` varchar(45) NOT NULL,
  `workflow` varchar(45) NOT NULL,
  `candstatus` varchar(10) NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`candidate_id`),
  KEY `sub_id` (`sub_id`),
  KEY `member_id` (`member_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `clienttype_xref` (
  `clienttype_xref_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `client_type` varchar(45) NOT NULL,
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`clienttype_xref_id`),
  KEY `member_id` (`member_id`,`sub_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `client_company_xref` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL DEFAULT '0',
  `company_id` int(10) unsigned NOT NULL DEFAULT '0',
  `drno` int(10) unsigned NOT NULL DEFAULT '0',
  `crno` int(10) unsigned NOT NULL DEFAULT '0',
  `drsub` int(2) NOT NULL DEFAULT '0',
  `crsub` int(2) NOT NULL DEFAULT '0',
  `subname` varchar(45) NOT NULL DEFAULT '',
  `blocked` char(1) NOT NULL DEFAULT 'N',
  `sortcode` varchar(45) NOT NULL DEFAULT '',
  `current` double(16,2) NOT NULL DEFAULT '0.00',
  `d30` double(16,2) NOT NULL DEFAULT '0.00',
  `d60` double(16,2) NOT NULL DEFAULT '0.00',
  `d90` double(16,2) NOT NULL DEFAULT '0.00',
  `d120` double(16,2) NOT NULL DEFAULT '0.00',
  `tcur` double(16,2) NOT NULL DEFAULT '0.00',
  `t30` double(16,2) NOT NULL DEFAULT '0.00',
  `t60` double(16,2) NOT NULL DEFAULT '0.00',
  `t90` double(16,2) NOT NULL DEFAULT '0.00',
  `t120` double(16,2) NOT NULL DEFAULT '0.00',
  `limit` int(4) NOT NULL,
  `monlimit` double(16,2) NOT NULL DEFAULT '0.00',
  `sellprice` int(4) NOT NULL,
 `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `client_types` (
  `client_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_type` varchar(15) NOT NULL,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`client_type_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `comms` (
  `comms_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `staff_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comms_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `country_code` varchar(4) NOT NULL,
  `area_code` varchar(4) NOT NULL DEFAULT ' ',
  `comm` varchar(75) NOT NULL DEFAULT ' ',
  `comm2` varchar(75) NOT NULL,
  `preferred` char(1) NOT NULL,
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`comms_id`),
  KEY `member_id` (`member_id`),
  KEY `comm2` (`comm2`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `comms_type` (
  `comms_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comm_type` varchar(45) NOT NULL,
  PRIMARY KEY (`comms_type_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(45) NOT NULL,
  `db_name` varchar(45) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  `business_number` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`company_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `complaints` (
  `complaint_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `complainant` varchar(50) NOT NULL,
  `against` varchar(50) NOT NULL,
  `received` date DEFAULT NULL,
  `via` varchar(20) NOT NULL,
  `acknowledged` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `compensation` decimal(16,2) NOT NULL DEFAULT '0.00',
  `medium` varchar(50) NOT NULL,
  `source` varchar(50) NOT NULL,
  `nature` varchar(50) NOT NULL,
  `product` varchar(100) NOT NULL,
  `taken_by` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `outcome` text NOT NULL,
  `notes` text NOT NULL,
  `responded` date DEFAULT NULL,
  `cause` varchar(100) NOT NULL,
  `further_action` text NOT NULL,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`complaint_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `documents` (
  `doc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `ddate` date NOT NULL,
  `doc` varchar(100) NOT NULL,
  `staff` varchar(45) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`doc_id`),
  KEY `member_id` (`member_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `emails` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `email_date` date NOT NULL DEFAULT '0000-00-00',
  `email_from` varchar(45) NOT NULL,
  `email_subject` varchar(45) NOT NULL,
  `email_message` text NOT NULL,
  `email_time` char(8) NOT NULL,
  `staff_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email_id`),
  KEY `member_id` (`member_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `emails2send` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_date` date NOT NULL DEFAULT '0000-00-00',
  `email_from` varchar(70) NOT NULL,
  `email_to` varchar(70) NOT NULL,
  `cc` varchar(300) CHARACTER SET latin1 NOT NULL,
  `email_subject` varchar(70) NOT NULL,
  `email_message` text NOT NULL,
  `sent` char(1) NOT NULL DEFAULT 'N',
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email_id`),
  KEY `email_date` (`email_date`),
  KEY `sub_id` (`sub_id`)
) ENGINE=myisam DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `industries` (
  `industry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `industry` varchar(45) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`industry_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql="
CREATE TABLE IF NOT EXISTS `links` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alt_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(45) NOT NULL DEFAULT '',
  `middlename` varchar(45) NOT NULL,
  `preferredname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL DEFAULT '',
  `dob` date NOT NULL,
  `gender` char(6) NOT NULL,
  `title` char(9) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  `client_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `age` smallint(3) unsigned NOT NULL,
  `checked` char(3) NOT NULL DEFAULT 'No',
  `occupation` varchar(45) NOT NULL,
  `industry_id` int(11) NOT NULL,
  `position` varchar(45) NOT NULL,
  `dependant` char(1) NOT NULL DEFAULT 'N',
  `staff` varchar(45) NOT NULL,
  `status` varchar(45) NOT NULL,
  `next_meeting` date NOT NULL DEFAULT '0000-00-00',
  `priceband` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`),
  KEY `lastname` (`lastname`),
  KEY `dob` (`dob`),
  KEY `sub_id` (`sub_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `outprovs` (
  `outprov_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(45) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  `phone` varchar(30) NOT NULL,
  `address` varchar(70) NOT NULL,
  `email` varchar(70) NOT NULL,
  `web` varchar(70) NOT NULL,
  PRIMARY KEY (`outprov_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql="
CREATE TABLE IF NOT EXISTS `referrals` (
  `referral_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(10) DEFAULT NULL,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `middlename` varchar(45) DEFAULT NULL,
  `preferred` varchar(45) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `commtype` int(11) NOT NULL DEFAULT '0',
  `country` varchar(45) DEFAULT NULL,
  `area` varchar(10) DEFAULT NULL,
  `comm` varchar(80) DEFAULT NULL,
  `commtype2` int(11) NOT NULL,
  `country2` varchar(45) DEFAULT NULL,
  `area2` varchar(10) DEFAULT NULL,
  `comm2` varchar(80) DEFAULT NULL,
  `location` varchar(20) DEFAULT NULL,
  `addresstype` int(11) NOT NULL DEFAULT '0',
  `streetno` varchar(50) DEFAULT NULL,
  `street` varchar(50) DEFAULT NULL,
  `suburb` varchar(50) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `postcode` varchar(6) DEFAULT NULL,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `staff_id` int(11) NOT NULL DEFAULT '0',
  `ddate` date DEFAULT NULL,
  `referred_id` int(11) NOT NULL DEFAULT '0',
  `phoned` char(3) NOT NULL DEFAULT 'No',
  `phcounted` char(1) NOT NULL DEFAULT 'N',
  `note` text NOT NULL,
  PRIMARY KEY (`referral_id`),
  KEY `lastname` (`lastname`),
  KEY `comm` (`comm`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 "; 
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `referrals_phone` (
  `ref_phone_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ddate` date NOT NULL,
  `ttime` char(11) NOT NULL,
  `referral_id` int(11) NOT NULL DEFAULT '0',
  `staff_id` int(11) NOT NULL DEFAULT '0',
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ref_phone_id`)
) ENGINE=myisam DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `referred` (
  `referred_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referred` varchar(25) NOT NULL,
  `sub_id` int(11) NOT NULL,
  PRIMARY KEY (`referred_id`)
) ENGINE=myisam DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `rural` (
  `rural_id` int(11) NOT NULL AUTO_INCREMENT,
  `rd` mediumint(9) NOT NULL,
  `town` varchar(100) NOT NULL,
  `postcode` varchar(5) NOT NULL,
  PRIMARY KEY (`rural_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `sayings` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heading` varchar(45) NOT NULL,
  `saying` varchar(400) NOT NULL,
  `credit` varchar(45) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(20) NOT NULL,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`status_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `streets` (
  `street_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `street` varchar(45) NOT NULL,
  `suburb` varchar(45) NOT NULL,
  `area` varchar(45) NOT NULL,
  `postcode` varchar(4) NOT NULL,
  PRIMARY KEY (`street_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `subemails` (
  `subemail_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(70) NOT NULL,
  `recipient` varchar(70) NOT NULL,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`subemail_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `todo` (
  `todo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `enter_date` date NOT NULL,
  `enter_staff` varchar(45) NOT NULL,
  `todo_by` varchar(45) NOT NULL,
  `complete_by` date NOT NULL,
  `task` varchar(250) NOT NULL,
  `done` char(3) NOT NULL DEFAULT 'No',
  `category` varchar(45) NOT NULL,
  `sub_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`todo_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `workflow` (
  `process_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `process` varchar(45) NOT NULL,
  `sub_id` int(11) NOT NULL,
  `porder` int(10) NOT NULL DEFAULT '0',
  `aide_memoir` text NOT NULL,
  PRIMARY KEY (`process_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql="
CREATE TABLE IF NOT EXISTS `workflow_xref` (
  `workflow_xref_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `process` varchar(45) NOT NULL,
  `sub_id` int(11) NOT NULL,
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`workflow_xref_id`),
  KEY `member_id` (`member_id`),
  KEY `sub_id` (`sub_id`),
  KEY `process` (`process`)
) ENGINE=myisam  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql = "INSERT INTO `c_menu` (`menu_id`, `morder`, `level`, `label`, `onclick`, `tooltip`, `image_file`, `facility`, `a1`, `a2`, `a3`, `a4`, `a5`, `a6`, `a7`, `a8`, `a9`, `a10`, `a11`, `a12`, `a13`, `a14`, `a15`, `a16`, `a17`, `a18`, `a19`, `a20`, `std_sub`, `extra_1`, `extra_2`, `extra_3`) VALUES
(1, 1, 0, 'Members', '', 'Clients Menu', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(4, 4, 1, 'Administer Members', 'javascript:createTab(''updtmembers.php'',''Add/Edit/Delete Member Records'',0)', 'Add/Edit/Delete Member Records', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(6, 10, 1, 'View Members','javascript:createTab(''viewmembers.php'',''View Member Records'',0)', 'View Member Records', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(116, 100, 0, 'Administrative Tools', '', 'Administrative Tools Menu', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(118, 27, 1, 'Campaign Administration', NULL, 'Campaign Functions', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'N'),
(119, 28, 2, 'Update Campaigns', 'javascript:createTab(''updtcampaigns.php'',''Update Campaign Data'',0)', 'Update Campaign Data', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'N'),
(120, 29, 2, 'Run Campaign', 'javascript:createTab(''runcampaign.php'',''Run a Campaign'',0)', 'Run a Campaign', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'N'),
(121, 31, 2, 'Update Outsource Providers', 'javascript:createTab(''updtoutprov.php'',''Add/Edit/Delete Outsoruce Providers'',0)', 'Add/Edit/Delete Outsoruce Providers', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'N'),
(124, 300, 0, 'Complaints Register', NULL, '', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(154, 2, 1, 'Referrals', 'javascript:createTab(''updtreferrals.php'',''Add and Administer Referrals'',0)', 'Add and Administer Referrals', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(129, 305, 1, 'Administer Complaints', 'javascript:createTab(''complaints.php'',''List and update complaints'',0)', 'List and update complaints', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(135, 32, 2, 'Campaign Statistics', '', 'Report on campaign statistics', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'N'),
(136, 33, 3, 'By Campaign/Outsource', 'javascript:createTab(''campstats.php'',''Report on campaign statistics by campaign/outsource'',0)', 'Report on campaign statistics by campaign/outsource', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'N'),
(138, 180, 1, 'Duplicate Member Records', 'javascript:createTab(''listduplicates.php'',''List Duplicate member records'',0)', 'List Duplicate member records', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(141, 185, 1, 'Update Client Types', 'javascript:createTab(''updtclienttypes.php'',''Update Client Types'',0)', 'Update Client Types', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(142, 186, 1, 'Update Industries', 'javascript:createTab(''updtindustries.php'',''Update Industry Types'',0)', 'Update Industry Types', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(144, 145, 1, 'Update Workflow Stages', 'javascript:createTab(''updtworkflow.php'',''Update Workflow Stages'',0)', 'Update list of workflow stages', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(145, 26, 0, 'Marketing', NULL, 'Marketing Functions', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'Y', 'N', 'Y'),
(149, 45, 2, 'Update Marketers', 'javascript:createTab(''updtmarketers.php'',''Add/Edit/Delete Marketers and their Staff'',0)', 'Add/Edit/Delete Marketers and their Staff', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y'),
(155, 205, 1, 'Update Source List', 'javascript:createTab(''updtreferredby.php'',''Update Source entries'',0)', 'Update Source entries', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N')";

$r = mysql_query($sql) or die(mysql_error().' '.$sql);


/*
$sql = "INSERT INTO `a_menu` (`menu_id`, `morder`, `level`, `label`, `onclick`, `tooltip`, `image_file`, `facility`, `a1`, `a2`, `a3`, `a4`, `a5`, `a6`, `a7`, `a8`, `a9`, `a10`, `a11`, `a12`, `a13`, `a14`, `a15`, `a16`, `a17`, `a18`, `a19`, `a20`, `std_sub`, `extra_1`, `extra_2`, `extra_3`) VALUES
(11, 110, 1, 'Update Users', 'javascript:createTab(''updtusers.php'',''Update user details and access levels'',0)', 'Add, edit user details', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(12, 115, 1, 'Update Menu Groups', 'javascript:createTab(''updtmenug.php'',''Update menu groups'',0)', 'Assign menu items to groups', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(97, 105, 1, 'Back Up', 'javascript:createTab(''bkup/backup.php'',''Backup database'',0)', 'Backup Database', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(116, 100, 0, 'Administrative Tools', '', 'Administrative Tools Menu', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(115, 165, 1, 'Audit Trail', 'javascript:createTab(''audittrail.php'',''View/Print Audit Trail'',0)', '', '', '', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(123, 155, 1, 'Update Email Recipients', 'javascript:createTab(''updtrecipients.php'',''Update non-member email recipients'',0)', 'Update non-member email recipients', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(114, 120, 1, 'Update Links', 'javascript:createTab(''links.php'',''Links to useful sites'',0)', 'Navigate to useful sites', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(117, 160, 1, 'Add Email Attachment File', 'javascript:createTab(''updtattach.php'',''Add files to use as email attachments'',0)', 'Add files to use as email attachments', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(134, 135, 1, 'Change Login Details', 'javascript:createTab(''updtlogin.php'',''Change Login Details'',0)', 'Change Login Details', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(139, 140, 1, 'Update Company Details', 'javascript:createTab(''editgroup.php'',''Update your company Details'',0)', 'Update Your Company Details', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(156, 210, 1, 'Upload Template Letters', 'javascript:createTab(''uploadtemplates.php'',''Upload template letters'',0)', 'Upload template word documents', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(158, 215, 1, 'Upload Email Signature File', 'javascript:createTab(''uploadsig.php'',''Upload Email Signature file'',0)', 'Uplaod Email Signature file', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(160, 260, 1, 'Import Client List from spreadsheet', 'javascript:createTab(''import/importclients.php'',''Import Client List from spreadsheet'',0)', 'Import Client List from spreadsheet', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(161, 265, 1, 'Download client input template', 'javascript:createTab(''services/clienttemplate.php'',''Downlpad client input template spreadsheet'',0)', 'Download client input template spreadsheet', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N'),
(162, 106, 1, 'Restore from Backup', 'javascript:createTab(''bkup/rbackup.php'',''Restore from Backup File'',0)', 'Restore from backup file', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'N', 'N', 'N')";

$r = mysql_query($sql) or die(mysql_error().' '.$sql);

*/

$sql = "INSERT INTO `comms_type` (`comms_type_id`, `comm_type`) VALUES
(1, 'Phone Home'),
(2, 'Phone Work'),
(3, 'Mobile'),
(4, 'Email'),
(5, 'Skype'),
(6, 'Fax Work'),
(7, 'Fax Home'),
(8, 'After Hours'),
(9, 'Facebook'),
(10, 'Twitter'),
(11, 'Web')";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql = "INSERT INTO `address_type` (`address_type_id`, `address_type`) VALUES
(1, 'Home'),
(2, 'Work'),
(3, 'Delivery'),
(4, 'Billing')";




?>