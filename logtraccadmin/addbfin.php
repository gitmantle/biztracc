<?php


$sql ="
CREATE TABLE IF NOT EXISTS `assetheadings` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `hcode` char(2) NOT NULL DEFAULT '',
  `heading` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `assign` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `asscode` char(5) NOT NULL DEFAULT '',
  `assignment` char(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `audit` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `entrydate` date NOT NULL DEFAULT '0000-00-00',
  `entrytime` char(8) NOT NULL DEFAULT '',
  `acc2dr` int(11) NOT NULL DEFAULT '0',
  `brdr` char(4) NOT NULL DEFAULT '',
  `subdr` int(11) NOT NULL DEFAULT '0',
  `acc2cr` int(11) NOT NULL DEFAULT '0',
  `brcr` char(4) NOT NULL DEFAULT '',
  `subcr` int(11) NOT NULL DEFAULT '0',
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `descript1` varchar(45) NOT NULL DEFAULT '',
  `reference` char(9) NOT NULL DEFAULT '',
  `amount` double(16,2) NOT NULL DEFAULT '0.00',
  `tax` double(16,2) NOT NULL DEFAULT '0.00',
  `total` double(16,2) NOT NULL DEFAULT '0.00',
  `taxtype` char(3) NOT NULL DEFAULT '',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  `userip` varchar(20) NOT NULL DEFAULT '',
  `username` varchar(45) NOT NULL,
  `currency` char(2) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `branch` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `branch` char(4) NOT NULL DEFAULT '',
  `branchname` char(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `crntemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `c_menu` (
  `c_menu_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`c_menu_id`)
) ENGINE=myisam  DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `c_stemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8"  ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `fixassets` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `hcode` char(2) NOT NULL DEFAULT '',
  `accountno` int(11) NOT NULL DEFAULT '0',
  `branch` char(4) NOT NULL DEFAULT '',
  `sub` int(11) NOT NULL DEFAULT '0',
  `asset` varchar(45) NOT NULL DEFAULT '',
  `cost` double(16,2) NOT NULL DEFAULT '0.00',
  `lastyrcost` double(16,2) NOT NULL DEFAULT '0.00',
  `lastyrbv` double(16,2) NOT NULL DEFAULT '0.00',
  `totdep` double(16,2) NOT NULL DEFAULT '0.00',
  `blocked` char(1) NOT NULL DEFAULT '',
  `way` char(1) NOT NULL DEFAULT '',
  `ncount` int(11) NOT NULL DEFAULT '0',
  `rate` double(4,1) NOT NULL DEFAULT '0.0',
  `anndep` double(13,2) NOT NULL DEFAULT '0.00',
  `dep5000` double(13,2) NOT NULL DEFAULT '0.00',
  `bought` date NOT NULL DEFAULT '0000-00-00',
  `notes` text NOT NULL,
  `depndate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `forex` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  `descript` char(25) NOT NULL DEFAULT '',
  `symbol` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `glmast` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `grp` char(3) NOT NULL DEFAULT '',
  `account` char(40) NOT NULL DEFAULT '',
  `accountno` int(11) NOT NULL DEFAULT '0',
  `branch` char(4) NOT NULL DEFAULT '',
  `sub` int(11) NOT NULL DEFAULT '0',
  `obal` double(16,2) NOT NULL DEFAULT '0.00',
  `obalm` double(16,2) NOT NULL DEFAULT '0.00',
  `prevbal` double(16,2) NOT NULL DEFAULT '0.00',
  `lastyear` double(16,2) NOT NULL DEFAULT '0.00',
  `recon` char(1) NOT NULL DEFAULT 'N',
  `blocked` char(1) NOT NULL DEFAULT 'N',
  `active` char(1) NOT NULL DEFAULT 'Y',
  `paygst` char(1) NOT NULL DEFAULT 'N',
  `sc` tinyint(3) NOT NULL DEFAULT 20,
  `ctrlacc` char(1) NOT NULL DEFAULT 'N',
  `system` char(1) NOT NULL DEFAULT 'N',
  `ird` tinyint(3) NOT NULL DEFAULT '0',
  `ird2` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `globals` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `setupf` char(1) NOT NULL DEFAULT 'N',
  `setupg` char(1) NOT NULL DEFAULT 'N',
  `coyname` varchar(45) NOT NULL DEFAULT '',
  `bustype1` varchar(45) NOT NULL DEFAULT '',
  `bustype2` varchar(45) NOT NULL DEFAULT '',
  `ad1` varchar(45) NOT NULL DEFAULT '',
  `ad2` varchar(45) NOT NULL DEFAULT '',
  `ad3` varchar(45) NOT NULL DEFAULT '',
  `boxno` varchar(15) NOT NULL DEFAULT '',
  `telno` varchar(15) NOT NULL DEFAULT '',
  `faxno` varchar(15) NOT NULL DEFAULT '',
  `po` varchar(45) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `branch` char(3) NOT NULL DEFAULT 'No',
  `branchname` varchar(30) NOT NULL DEFAULT 'Main',
  `subac` char(3) NOT NULL DEFAULT 'No',
  `stock` char(3) NOT NULL DEFAULT 'No',
  `bedate` date NOT NULL DEFAULT '2007-01-01',
  `yrdate` date NOT NULL DEFAULT '2007-01-01',
  `gstno` char(20) NOT NULL DEFAULT '',
  `gstpcent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `gsttype` char(1) NOT NULL DEFAULT 'I',
  `gstperiod` char(8) NOT NULL DEFAULT '1 Month',
  `z_rt1` varchar(45) NOT NULL DEFAULT '',
  `z_rt2` varchar(45) NOT NULL DEFAULT '',
  `z_rt3` varchar(45) NOT NULL DEFAULT '',
  `lstatdt` date NOT NULL DEFAULT '2007-01-01',
  `pstatdt` date NOT NULL DEFAULT '2007-01-01',
  `allowtrans` char(1) NOT NULL DEFAULT 'Y',
  `cashacc` decimal(3,0) NOT NULL DEFAULT '755',
  `cashbr` char(1) NOT NULL DEFAULT '',
  `cashsb` decimal(2,0) NOT NULL DEFAULT '0',
  `gst1` char(5) NOT NULL DEFAULT '',
  `gst2` char(5) NOT NULL DEFAULT '',
  `gst3` char(5) NOT NULL DEFAULT '',
  `gst4` char(5) NOT NULL DEFAULT '',
  `gst5` char(5) NOT NULL DEFAULT '',
  `gst6` char(5) NOT NULL DEFAULT '',
  `gst7` char(5) NOT NULL DEFAULT '',
  `gst8` char(5) NOT NULL DEFAULT '',
  `gst9` char(5) NOT NULL DEFAULT '',
  `gst10` char(5) NOT NULL DEFAULT '',
  `gst11` char(5) NOT NULL DEFAULT '',
  `gst12` char(5) NOT NULL DEFAULT '',
  `gstfiled` date NOT NULL DEFAULT '2007-01-01',
  `salesac` int(11) NOT NULL DEFAULT '755',
  `salesbr` char(4) NOT NULL,
  `salessb` int(11) NOT NULL DEFAULT '0',
  `acc` char(1) NOT NULL,
  `stk` char(1) NOT NULL,
  `fas` char(1) NOT NULL,
  `prd` char(1) NOT NULL,
  `country` char(3) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `grntemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8"  ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `invhead` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `accountno` int(11) NOT NULL DEFAULT '0',
  `branch` char(4) NOT NULL DEFAULT '',
  `sub` int(11) NOT NULL DEFAULT '0',
  `gldesc` char(40) NOT NULL DEFAULT '',
  `transtype` char(3) NOT NULL,
  `ref_no` char(15) NOT NULL,
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `totvalue` double(16,2) NOT NULL DEFAULT '0.00',
  `tax` double(16,2) NOT NULL DEFAULT '0.00',
  `cash` double(16,2) NOT NULL DEFAULT '0.00',
  `cheque` double(16,2) NOT NULL DEFAULT '0.00',
  `eftpos` double(16,2) NOT NULL DEFAULT '0.00',
  `ccard` double(16,2) NOT NULL DEFAULT '0.00',
  `staff` varchar(45) NOT NULL,
  `xref` char(9) NOT NULL DEFAULT '',
  `postaladdress` varchar(100) NOT NULL,
  `deliveryaddress` varchar(100) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `invtemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `invtrans` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `itemcode` varchar(30) NOT NULL,
  `quantity` decimal(9,3) NOT NULL DEFAULT '0.000',
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `taxtype` char(3) NOT NULL DEFAULT '',
  `taxpcent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(16,2) NOT NULL DEFAULT '0.00',
  `disc_type` char(1) NOT NULL DEFAULT '',
  `discount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `ref_no` char(15) NOT NULL,
  `item` varchar(45) NOT NULL DEFAULT '',
  `unit` char(4) NOT NULL DEFAULT '',
  `value` decimal(16,2) NOT NULL DEFAULT '0.00',
  `supplier` int(11) NOT NULL DEFAULT '0',
  `currency` char(3) NOT NULL DEFAULT '',
  `sub` int(11) NOT NULL DEFAULT '0',
  `consign` char(1) NOT NULL DEFAULT '',
  `rate` decimal(7,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql ="
CREATE TABLE IF NOT EXISTS `numbers` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `inv` int(11) NOT NULL DEFAULT '0',
  `c_s` int(11) NOT NULL DEFAULT '0',
  `req` int(11) NOT NULL DEFAULT '0',
  `grn` int(11) NOT NULL DEFAULT '0',
  `c_p` int(11) NOT NULL DEFAULT '0',
  `crn` int(11) NOT NULL DEFAULT '0',
  `ret` int(11) NOT NULL DEFAULT '0',
  `chq` int(11) NOT NULL DEFAULT '0',
  `adj` int(11) NOT NULL DEFAULT '0',
  `tsf` int(11) NOT NULL DEFAULT '0',
  `dep` int(11) NOT NULL DEFAULT '0',
  `jnl` int(11) NOT NULL DEFAULT '0',
  `oth` int(11) NOT NULL DEFAULT '0',
  `rec` int(11) NOT NULL DEFAULT '0',
  `pay` int(11) NOT NULL DEFAULT '0',
  `crd` int(11) NOT NULL DEFAULT '0',
  `ebk` int(11) NOT NULL DEFAULT '0',
  `p_c` int(11) NOT NULL DEFAULT '0',
  `sal` int(11) NOT NULL DEFAULT '0',
  `pur` int(11) NOT NULL DEFAULT '0',
  `c_n` int(11) NOT NULL DEFAULT '0',
  `r_t` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `rectemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `rectr` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `entry_no` int(11) NOT NULL DEFAULT '0',
  `acc2dr` int(11) NOT NULL DEFAULT '0',
  `subdr` int(11) NOT NULL DEFAULT '0',
  `brdr` char(4) NOT NULL DEFAULT '',
  `acc2cr` int(11) NOT NULL DEFAULT '0',
  `subcr` int(11) NOT NULL DEFAULT '0',
  `brcr` char(4) NOT NULL DEFAULT '',
  `cdate` date NOT NULL DEFAULT '0000-00-00',
  `descript1` varchar(60) NOT NULL DEFAULT '',
  `reference` char(9) NOT NULL DEFAULT '',
  `amount` double(16,2) NOT NULL DEFAULT '0.00',
  `depdr` int(11) NOT NULL DEFAULT '0',
  `depbrdr` char(4) NOT NULL DEFAULT '',
  `depcr` int(11) NOT NULL DEFAULT '0',
  `depbrcr` char(4) NOT NULL DEFAULT '',
  `del` char(1) NOT NULL DEFAULT '',
  `nallocate` int(11) NOT NULL DEFAULT '0',
  `entrydate` date NOT NULL DEFAULT '0000-00-00',
  `entrytime` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `rettemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `stkcategory` (
  `catid` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `category` varchar(30) NOT NULL DEFAULT '',
 `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`catid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `stkform` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `sellcode` char(3) NOT NULL DEFAULT '',
  `s1` double(7,2) NOT NULL DEFAULT '0.00',
  `s2` double(7,2) NOT NULL DEFAULT '0.00',
  `s3` double(7,2) NOT NULL DEFAULT '0.00',
  `s4` double(7,2) NOT NULL DEFAULT '0.00',
  `s5` double(7,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `stkgroup` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(30) NOT NULL DEFAULT '',
  `stock` char(3) NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`groupid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql = "
CREATE TABLE IF NOT EXISTS `stklocs` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(40) NOT NULL,
  `branch` char(4) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);


$sql ="
CREATE TABLE IF NOT EXISTS `stkmast` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `itemcode` varchar(30) NOT NULL DEFAULT '',
  `item` varchar(100) NOT NULL,
  `barno` varchar(30) NOT NULL DEFAULT '0',
  `avgcost` decimal(16,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(20) NOT NULL DEFAULT '',
  `sellacc` int(11) NOT NULL DEFAULT '0',
  `sellbr` char(4) NOT NULL DEFAULT '',
  `sellsub` int(11) NOT NULL DEFAULT '0',
  `purchacc` int(11) NOT NULL DEFAULT '0',
  `purchbr` char(4) NOT NULL DEFAULT '',
  `purchsub` int(11) NOT NULL DEFAULT '0',
  `setsell` decimal(16,2) NOT NULL DEFAULT '0.00',
  `deftax` char(3) NOT NULL DEFAULT '',
  `active` char(3) NOT NULL DEFAULT 'Yes',
  `bom` char(1) NOT NULL DEFAULT 'N',
  `stock` char(3) NOT NULL DEFAULT 'Yes',
  `trackserial` char(3) NOT NULL DEFAULT 'No',
  `onhand` double(17,3) NOT NULL DEFAULT '0.000',
  `uncosted` double(17,3) NOT NULL DEFAULT '0.000',
  `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`itemid`),
  UNIQUE KEY `itemcode` (`itemcode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8    " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql = "
CREATE TABLE IF NOT EXISTS `stkpricepcent` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `priceband` varchar(40) NOT NULL,
  `pcent` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql = "
CREATE TABLE IF NOT EXISTS `stkserials` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `itemcode` varchar(30) NOT NULL,
  `item` varchar(100) NOT NULL,
  `serialno` varchar(50) NOT NULL,
  `locationid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1"; 
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `stktrans` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `itemcode` varchar(30) NOT NULL,
  `item` varchar(45) NOT NULL DEFAULT '',
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `increase` decimal(9,3) NOT NULL DEFAULT '0.000',
  `decrease` decimal(9,3) NOT NULL DEFAULT '0.000',
  `locid` int(11) NOT NULL DEFAULT '0',
  `ref_no` char(15) NOT NULL,
  `transtype` char(3) NOT NULL DEFAULT '',
  `amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `supplier` int(11) NOT NULL DEFAULT '0',
  `currency` char(2) NOT NULL DEFAULT '',
  `sub` int(11) NOT NULL DEFAULT '0',
  `rate` decimal(7,3) NOT NULL DEFAULT '0.000',
  `itemcost` decimal(16,2) NOT NULL DEFAULT '0.00',
  `stocksell` decimal(16,2) NOT NULL DEFAULT '0.00',
  `keep` char(1) NOT NULL DEFAULT '',
  `sold` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql = "
CREATE TABLE IF NOT EXISTS `taxtypes` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `tax` char(3) NOT NULL DEFAULT '',
  `description` varchar(45) NOT NULL DEFAULT '',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `tbtemplate` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL DEFAULT '',
  `include` char(1) NOT NULL DEFAULT 'N',
  `xcoord` int(11) NOT NULL DEFAULT '0',
  `ycoord` int(11) NOT NULL DEFAULT '0',
  `font` varchar(20) NOT NULL DEFAULT 'Arial,,10',
  `drawcolor` varchar(20) NOT NULL DEFAULT '0',
  `fillcolor` varchar(20) NOT NULL DEFAULT '0',
  `textcolor` varchar(20) NOT NULL DEFAULT '0',
  `linewidth` decimal(2,1) NOT NULL DEFAULT '0.2',
  `cellwidth` int(11) NOT NULL DEFAULT '0',
  `gridwidths` varchar(45) NOT NULL DEFAULT '',
  `cellheight` int(11) NOT NULL DEFAULT '0',
  `content` varchar(200) NOT NULL DEFAULT '',
  `border` varchar(5) NOT NULL DEFAULT '0',
  `nextpos` int(11) NOT NULL DEFAULT '0',
  `align` varchar(25) NOT NULL DEFAULT 'L',
  `fill` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `trans` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `entry_no` int(11) NOT NULL DEFAULT '0',
  `acc2dr` int(11) NOT NULL DEFAULT '0',
  `subdr` int(11) NOT NULL DEFAULT '0',
  `brdr` char(4) NOT NULL DEFAULT '',
  `acc2cr` int(11) NOT NULL DEFAULT '0',
  `subcr` int(11) NOT NULL DEFAULT '0',
  `brcr` char(4) NOT NULL DEFAULT '',
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `descript1` varchar(60) NOT NULL DEFAULT '',
  `reference` char(9) NOT NULL DEFAULT '',
  `amount` double(16,2) NOT NULL DEFAULT '0.00',
  `depdr` int(11) NOT NULL DEFAULT '0',
  `depbrdr` char(4) NOT NULL DEFAULT '',
  `depcr` int(11) NOT NULL DEFAULT '0',
  `depbrcr` char(4) NOT NULL DEFAULT '',
  `del` char(1) NOT NULL DEFAULT '',
  `nallocate` int(11) NOT NULL DEFAULT '0',
  `entrydate` date NOT NULL DEFAULT '0000-00-00',
  `entrytime` char(5) NOT NULL DEFAULT '',
  `tax` double(16,2) NOT NULL DEFAULT '0.00',
  `taxtype` int(11) NOT NULL DEFAULT '0',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  `applytax` char(1) NOT NULL DEFAULT '',
  `total` double(16,2) NOT NULL DEFAULT '0.00',
  `done` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '',
  `grn` char(10) NOT NULL DEFAULT '',
  `inv` char(10) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `trmain` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `accountno` int(11) NOT NULL DEFAULT '0',
  `branch` char(4) NOT NULL DEFAULT '',
  `sub` int(11) NOT NULL DEFAULT '0',
  `accno` int(11) NOT NULL DEFAULT '0',
  `br` char(4) NOT NULL DEFAULT '',
  `subbr` int(11) NOT NULL DEFAULT '0',
  `debit` double(16,2) NOT NULL DEFAULT '0.00',
  `credit` double(16,2) NOT NULL DEFAULT '0.00',
  `reference` char(15) NOT NULL DEFAULT '',
  `descript1` varchar(60) NOT NULL DEFAULT '',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  `gsttype` char(3) NOT NULL DEFAULT '',
  `gstrecon` char(1) NOT NULL DEFAULT 'N',
  `gstadjust` char(1) NOT NULL DEFAULT '',
  `reconciled` char(1) NOT NULL DEFAULT 'N',
  `temprecon` char(1) NOT NULL DEFAULT 'N',
  `consign` int(11) NOT NULL DEFAULT '0',
  `supref` char(6) NOT NULL DEFAULT '',
  `grn` char(15) NOT NULL DEFAULT '',
  `paid` double(16,2) NOT NULL DEFAULT '0.00',
  `inv` char(15) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  `invtransuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `z_1rec` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `entry_no` int(11) NOT NULL DEFAULT '0',
  `acc2dr` int(11) NOT NULL DEFAULT '0',
  `subdr` int(11) NOT NULL DEFAULT '0',
  `brdr` char(4) NOT NULL DEFAULT '',
  `acc2cr` int(11) NOT NULL DEFAULT '0',
  `subcr` int(11) NOT NULL DEFAULT '0',
  `brcr` char(4) NOT NULL DEFAULT '',
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `descript1` varchar(60) NOT NULL DEFAULT '',
  `reference` char(9) NOT NULL DEFAULT '',
  `amount` double(16,2) NOT NULL DEFAULT '0.00',
  `depdr` int(11) NOT NULL DEFAULT '0',
  `depbrdr` char(4) NOT NULL DEFAULT '',
  `depcr` int(11) NOT NULL DEFAULT '0',
  `depbrcr` char(4) NOT NULL DEFAULT '',
  `del` char(1) NOT NULL DEFAULT '',
  `nallocate` int(11) NOT NULL DEFAULT '0',
  `entrydate` date NOT NULL DEFAULT '0000-00-00',
  `entrytime` char(5) NOT NULL DEFAULT '',
  `tax` double(16,2) NOT NULL DEFAULT '0.00',
  `taxtype` int(11) NOT NULL DEFAULT '0',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  `applytax` char(1) NOT NULL DEFAULT '',
  `price` double(16,2) NOT NULL DEFAULT '0.00',
  `done` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '',
  `grn` char(10) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `z_2rec` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `entry_no` int(11) NOT NULL DEFAULT '0',
  `acc2dr` int(11) NOT NULL DEFAULT '0',
  `subdr` int(11) NOT NULL DEFAULT '0',
  `brdr` char(4) NOT NULL DEFAULT '',
  `acc2cr` int(11) NOT NULL DEFAULT '0',
  `subcr` int(11) NOT NULL DEFAULT '0',
  `brcr` char(4) NOT NULL DEFAULT '',
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `descript1` varchar(60) NOT NULL DEFAULT '',
  `reference` char(9) NOT NULL DEFAULT '',
  `amount` double(16,2) NOT NULL DEFAULT '0.00',
  `depdr` int(11) NOT NULL DEFAULT '0',
  `depbrdr` char(4) NOT NULL DEFAULT '',
  `depcr` int(11) NOT NULL DEFAULT '0',
  `depbrcr` char(4) NOT NULL DEFAULT '',
  `del` char(1) NOT NULL DEFAULT '',
  `nallocate` int(11) NOT NULL DEFAULT '0',
  `entrydate` date NOT NULL DEFAULT '0000-00-00',
  `entrytime` char(5) NOT NULL DEFAULT '',
  `tax` double(16,2) NOT NULL DEFAULT '0.00',
  `taxtype` int(11) NOT NULL DEFAULT '0',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  `applytax` char(1) NOT NULL DEFAULT '',
  `price` double(16,2) NOT NULL DEFAULT '0.00',
  `done` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '',

  `grn` char(10) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8" ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `z_3rec` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `entry_no` int(11) NOT NULL DEFAULT '0',
  `acc2dr` int(11) NOT NULL DEFAULT '0',
  `subdr` int(11) NOT NULL DEFAULT '0',
  `brdr` char(4) NOT NULL DEFAULT '',
  `acc2cr` int(11) NOT NULL DEFAULT '0',
  `subcr` int(11) NOT NULL DEFAULT '0',
  `brcr` char(4) NOT NULL DEFAULT '',
  `ddate` date NOT NULL DEFAULT '0000-00-00',
  `descript1` varchar(60) NOT NULL DEFAULT '',
  `reference` char(9) NOT NULL DEFAULT '',
  `amount` double(16,2) NOT NULL DEFAULT '0.00',
  `depdr` int(11) NOT NULL DEFAULT '0',
  `depbrdr` char(4) NOT NULL DEFAULT '',
  `depcr` int(11) NOT NULL DEFAULT '0',
  `depbrcr` char(4) NOT NULL DEFAULT '',
  `del` char(1) NOT NULL DEFAULT '',
  `nallocate` int(11) NOT NULL DEFAULT '0',
  `entrydate` date NOT NULL DEFAULT '0000-00-00',
  `entrytime` char(5) NOT NULL DEFAULT '',
  `tax` double(16,2) NOT NULL DEFAULT '0.00',
  `taxtype` int(11) NOT NULL DEFAULT '0',
  `taxpcent` double(5,2) NOT NULL DEFAULT '0.00',
  `applytax` char(1) NOT NULL DEFAULT '',
  `price` double(16,2) NOT NULL DEFAULT '0.00',
  `done` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '',
  `grn` char(10) NOT NULL DEFAULT '',
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` double(7,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8"  ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `z_acno` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `cset` char(4) NOT NULL DEFAULT '',
  `depn` int(11) NOT NULL DEFAULT '250',
  `salary` int(11) NOT NULL DEFAULT '500',
  `provpay` int(11) NOT NULL DEFAULT '880',
  `retearn` int(11) NOT NULL DEFAULT '998',
  `journal` int(11) NOT NULL DEFAULT '999',
  `closestk` int(11) NOT NULL DEFAULT '187',
  `openstk` int(11) NOT NULL DEFAULT '181',
  `wip` int(11) NOT NULL DEFAULT '190',
  `salestax` int(11) NOT NULL DEFAULT '75',
  `purchtax` int(11) NOT NULL DEFAULT '185',
  `salesdis` int(11) NOT NULL DEFAULT '76',
  `purchdis` int(11) NOT NULL DEFAULT '186',
  `stockadj` int(11) NOT NULL DEFAULT '699',
  `stock` int(11) NOT NULL DEFAULT '825',
  `taxpay` int(11) NOT NULL DEFAULT '870',
  `bank` int(11) NOT NULL DEFAULT '751',
  `cashacc` int(11) NOT NULL DEFAULT '755',
  `cr` int(11) NOT NULL DEFAULT '851',
  `dr` int(11) NOT NULL DEFAULT '801',
  `ass` int(11) NOT NULL DEFAULT '701',
  `dep` int(11) NOT NULL DEFAULT '702',
  `brn` int(11) NOT NULL DEFAULT '997',
  `income1b` int(11) NOT NULL DEFAULT '1',
  `income1e` int(11) NOT NULL DEFAULT '100',
  `direct1b` int(11) NOT NULL DEFAULT '101',
  `direct1e` int(11) NOT NULL DEFAULT '200',
  `expenseb` int(11) NOT NULL DEFAULT '201',
  `expensee` int(11) NOT NULL DEFAULT '700',
  `bankb` int(11) NOT NULL DEFAULT '751',
  `banke` int(11) NOT NULL DEFAULT '800',
  `assetb` int(11) NOT NULL DEFAULT '801',
  `assete` int(11) NOT NULL DEFAULT '850',
  `liabilb` int(11) NOT NULL DEFAULT '851',
  `liabile` int(11) NOT NULL DEFAULT '900',
  `investb` int(11) NOT NULL DEFAULT '701',
  `investe` int(11) NOT NULL DEFAULT '750',
  `sharesb` int(11) NOT NULL DEFAULT '901',
  `sharese` int(11) NOT NULL DEFAULT '999',
  `plb` int(11) NOT NULL DEFAULT '1',
  `ple` int(11) NOT NULL DEFAULT '700',
  PRIMARY KEY (`uid`)
) ENGINE=myisam  DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql ="
CREATE TABLE IF NOT EXISTS `z_dates` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `dcurdate` date NOT NULL DEFAULT '0000-00-00',
  `ddate30` date NOT NULL DEFAULT '0000-00-00',
  `dldate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`uid`)
) ENGINE=myisam DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql = "
CREATE TABLE IF NOT EXISTS `glcodes` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `grp` char(3) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `glgroup` varchar(50) NOT NULL,
  `range_start` int(7) NOT NULL,
  `range_end` int(7) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 " ;
$r = mysql_query($sql) or die(mysql_error().' '.$sql);

$sql = "
INSERT INTO `c_menu` (`c_menu_id`, `morder`, `level`, `label`, `onclick`, `tooltip`, `image_file`, `facility`, `a1`, `a2`, `a3`, `a4`, `a5`, `a6`, `a7`, `a8`, `a9`, `a10`, `a11`, `a12`, `a13`, `a14`, `a15`, `a16`, `a17`, `a18`, `a19`, `a20`) VALUES
(1, 1, 0, 'Transactions', '', 'Transactions main menu', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(2, 2, 1, 'Invoice', 'javascript:createTab(''tr_inv.php'',''Invoice'',0)', 'Invoice', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(3, 3, 1, 'Cash Sale', 'javascript:createTab(''tr_c_s.php'',''Cash Sale'',0)', 'Cash Sale', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(4, 4, 1, 'Credit Note', 'javascript:createTab(''tr_crn.php'',''Credit Note'',0)', 'Enter credit note', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(5, 5, 1, 'Goods Received', 'javascript:createTab(''tr_grn.php'',''Goods Received'',0)', 'Enter goods/services recieved', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(6, 6, 1, 'Goods Returned', 'javascript:createTab(''tr_ret.php'',''Goods Returned'',0)', 'Goods Returned', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(7, 7, 1, 'Receipt', 'javascript:createTab(''tr_rec.php'',''Receipts'',0)', 'Enter payments recieved', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(8, 8, 1, 'Payment', 'javascript:createTab(''tr_pay.php'',''Payments'',0)', 'Enter payments made', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(9, 9, 1, 'Non Stock Invoice', 'javascript:createTab(''tr_nsinv.php'',''Invoice'',0)', 'Non stock Invoice', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(10, 10, 1, 'Non Stock Cash Sale', 'javascript:createTab(''tr_nsc_s.php'',''Cash Sale'',0)', 'Cash Sale', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(11, 11, 1, 'Non Stock Credit Note', 'javascript:createTab(''tr_nscrn.php'',''Credit Note'',0)', 'Credit Note', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(12, 12, 1, 'Requisition to Own Use', 'javascript:createTab(''tr_req.php'',''Requisition'',0)', 'Requisition to own use', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(13, 13, 1, 'Standard Transactions', 'javascript:createTab(''tr_stdtrans.php'',''Standard Transactions'',0)', 'Standard Transactions', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(14, 14, 1, 'Journal Transactions', 'javascript:createTab(''tr_journal.php'',''Journal Transactions'',0)', 'Journal Transactions', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(15, 15, 1, 'Recurring Transactions', 'javascript:createTab(''rep_bs.php'',''Recurring Transactions'',0)', 'Recurring Transactions', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(16, 16, 0, ' Reports', '', 'Reports Menu', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(17, 17, 1, 'GL Reports', '', 'GL Reports', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(18, 18, 2, 'Trial Balance', 'javascript:createTab(''rep_tb.php'',''Trial Balance'',0)', 'Trial Balance', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(19, 19, 2, 'Profit & Loss', 'javascript:createTab(''rep_pl.php'',''Profit and Loss'',0)', 'Profit and Loss', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(20, 20, 2, 'Balance Sheet', 'javascript:createTab(''rep_bs.php'',''Balance Sheet'',0)', 'Balance Sheet', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(21, 21, 2, 'Detail One Account', ' 	javascript:createTab(''rep_det1acc.php'',''Detail one account'',0)', 'Detail One Account', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(22, 22, 2, 'Days Takings', 'javascript:createTab(''rep_take.php'',''Days Takings'',0)', 'Days Takngs', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(23, 23, 2, 'Trading Tax  Report', 'javascript:createTab(''rep_gst.php'',''Trading Tax Report'',0)', 'Trading Tax Report', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(24, 24, 2, 'Monthly Balance Report', 'javascript:createTab(''rep_mthbals.php'',''Monthly Balance Report'',0)', 'Monthly Balance Report', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(25, 25, 2, 'Print Multiple GL Accounts', 'javascript:createTab(''rep_allgl.php'',''Print multiple GL Accounts'',0)', 'Print all transactions between dates for a range of GL accounts.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(26, 26, 1, 'Debtor  Reports', '', 'Debtor Reports menu', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(27, 27, 2, 'Debtors List', 'javascript:createTab(''rep_drlist.php'',''Debtors List'',0)', 'Debtors List', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(28, 28, 2, 'Debtors Aged Balances', 'javascript:createTab(''rep_dragebal.php'',''Debtors aged balances,0)', 'Debtors aged balances', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(29, 29, 2, 'Print Statements', 'javascript:createTab(''rep_printstat.php'',''Print statements'',0)', 'Print DR statements', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(30, 30, 2, 'Balances at a date', 'javascript:createTab(''rep_drbaldt.php'',''Balances at a date'',0)', 'DR Balances at a date', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(31, 31, 2, 'Print Multiple DR Accounts', 'javascript:createTab(''rep_alldr.php'',''Print multiple DR Accounts'',0)', 'Print all transactions between dates for a range of Debtors accounts.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(32, 32, 1, 'Creditor Reports', '', 'Creditor Reports Menu.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(33, 33, 2, 'Creditors List', 'javascript:createTab(''rep_crlist.php'',''Creditors List'',0)', 'Creditors List', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(34, 34, 2, 'Creditors Aged Balances', 'javascript:createTab(''rep_cragebal.php'',''Creditors aged balances,0)', 'Creditors aged balances', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(35, 35, 2, 'Print Statements', 'javascript:createTab(''rep_printrems.php'',''Print remittance advice'',0)', 'Remittance Advice', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(36, 36, 2, 'Balances at a date', 'javascript:createTab(''rep_crbaldt.php'',''Balances at a date'',0)', 'CR Balances at a date', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(37, 37, 2, 'Print Multiple CR Accounts', 'javascript:createTab(''rep_allcr.php'',''Print multiple CR  Accounts'',0)', 'Print all transactions between dates for a range of Creditors accounts.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(38, 38, 0, 'Accounts Administration', '', 'Accounts Administration menu.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(39, 39, 1, 'Update General Ledger', 'javascript:createTab(''ad_updtgl.php'',''Update General Ledger'',0)', 'Add, edit, delete general ledger accounts.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(40, 40, 1, 'Update Debtors', 'javascript:createTab(''ad_updtdr.php'',''Update Debtors'',0)', 'Add, edit, delete debtors accounts.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(41, 41, 1, 'Update Creditors', 'javascript:createTab(''ad_updtcr.php'',''Update Creditors'',0)', 'Add, edit, delete creditors accounts.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(42, 42, 1, 'Update Branches', 'javascript:createTab(''ad_branches.php'',''Update Branches'',0)', 'Add, edit, delete branches.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(43, 43, 1, 'Multiple Branches', 'javascript:createTab(''ad_multbranch.php'',''Multiple Branches'',0)', 'Copy a branch chart of accounts to other branches.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(44, 44, 1, 'Bank Reconciliation', 'javascript:createTab(''ad_bankrec.php'',''Bank Reconciliation'',0)', 'Reconcile system bank transactions against bank', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(45, 45, 0, 'Stock Control', '', 'Stock Control menu', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(46, 46, 1, 'Stock Administration', '', 'Stock Administration menu.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(47, 47, 2, 'Stock Master File', 'javascript:createTab(''stad_stkmast.php'',''Stock Master File'',0)', 'Add/Amend/Delete/List stock items.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(48, 48, 2, 'Stock Groups and Categories', 'javascript:createTab(''stad_stkgroups.php'',''Stock Groups and Categories'',0)', 'Add/Amend/Delete/List stock groups and categories.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(50, 50, 2, 'Percentage Mark-ups', 'javascript:createTab(''stad_pcentmarkup.php'',''Percentage Mark-ups'',0)', 'Add/Amend/Delete/List mark-up % per price group.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(51, 51, 2, 'Enter Opening Stock', 'javascript:createTab(''stad_openstock.php'',''Enter Opening Stock'',0)', 'Enter opening stock balances if applicable.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(52, 52, 2, 'Transfer Stock', 'javascript:createTab(''stad_stktransfer.php'',''Transfer Stock'',0)', 'Transfer stock from one code to another, or between locations.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(53, 53, 2, 'Stock Locations', 'javascript:createTab(''stad_stklocation.php'',''Stock Locations'',0)', 'Add/Amend/Delete/List Stock Locations.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(54, 54, 2, 'Stock Adjustment', 'javascript:createTab(''stad_adj.php'',''Stock Adjustment'',0)', 'Adjust Stock Balances', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(55, 55, 2, 'Bills of Material', 'javascript:createTab(''stad_bom.php'',''Bills of Material'',0)', 'Build/Maintain Bills of Material', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(56, 56, 1, 'Stock Reports', '', 'Stock Reports menu', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(57, 57, 2, 'Stock List', 'javascript:createTab(''strep_stklist.php'',''Stock List'',0)', 'Stock listing shown stock on hand.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(58, 58, 2, 'Stock Movement', 'javascript:createTab(''strep_stkmove.php'',''Stock Movement'',0)', 'Stock transactions per item or groups of items between dates.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(59, 59, 2, 'Price List', 'javascript:createTab(''strep_stkprice.php'',''Price List'',0)', 'Price lists for each of five price categories.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(60, 60, 2, 'Display INV & CRN', 'javascript:createTab(''strep_dispinv.php'',''Display INV & CRN'',0)', 'Display invoices per debtor and line items per invoice.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(61, 61, 2, 'Display Cash Sales', 'javascript:createTab(''strep_dispcs.php'',''Display Cash Sales'',0)', 'Display cash sales and line items per cash sale.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(62, 62, 2, 'Display GRNs and Returns', 'javascript:createTab(''strep_dispgrn.php'',''Display GRNs and Returns'',0)', 'Display goods received & returned per creditor.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(63, 63, 2, 'Display Adjustment & Transfer', 'javascript:createTab(''strep_dispadj.php'',''Display Adjustment & Transfer'',0)', 'Display adjustments and transfers.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(64, 64, 2, 'Display Work in Progress', 'javascript:createTab(''strep_dispwip.php'',''Display Work in Progress'',0)', 'Display work in progress transactions.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(65, 65, 2, 'Sales Analysis', 'javascript:createTab(''strep_salesanalysis.php'',''Sales Analysis'',0)', 'Display best sales.', '', 'ST', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(66, 66, 0, 'Fixed Assets', '', 'Assets and depreciation menu.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(67, 67, 1, 'Asset Register', 'javascript:createTab(''fa_register.php'',''Asset Register'',0)', 'Display list of Fixed Assets', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(68, 68, 1, 'Detail One Asset', 'javascript:createTab(''farep_det1a.php'',''Detail One Asset'',0)', 'List all transactions for an Asset account between dates', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(69, 69, 1, 'Purchase Asset', 'javascript:createTab(''fa_purchfa.php'',''Purchase Asset'',0)', 'Purchase Fixed Asset', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(70, 70, 1, 'Sell Asset', 'javascript:createTab(''fa_sellfa.php'',''Sell Asset'',0)', 'Sell a Fixed Asset', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(71, 71, 1, 'Depreciate Assets', 'javascript:createTab(''fa_fadep.php'',''Depreciate Assets'',0)', 'Run depreciation routine.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(72, 72, 1, 'Reverse Depreciation', 'javascript:createTab(''fa_revdep.php'',''Reverse Depreciation'',0)', 'Reverse incorrect depreciation on an asset.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(73, 73, 1, 'Reallocate Asset', 'javascript:createTab(''fa_faalloc.php'',''Reallocate Asset'',0)', 'Transfer all or part of an asset account to another asset account.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(74, 74, 1, 'Update Fixed Assets', 'javascript:createTab(''fa_faupdt.php'',''Update Fixed Assets'',0)', 'Add, Edit, Delete Fixed Assets.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(75, 75, 1, 'Fixed Asset Headings', 'javascript:createTab(''fa_fahead.php'',''Fixed Asset Headings'',0)', 'Amend Fixed Asset Headings.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(76, 76, 1, 'Print Fixed Asset Accounts', 'javascript:createTab(''farep_allas.php'',''Print Fixed Asset Accounts'',0)', 'Print all transactions between dates for a range of Asset accounts.', '', 'FA', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(77, 77, 0, 'Production', '', 'Production menu', '', 'PR', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(78, 78, 1, 'Enter Debtors Order', 'javascript:createTab(''pr_drorder.php'',''Enter Debtors Order'',0)', 'Enter orders from customers for production', '', 'PR', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(79, 79, 1, 'Schedule Orders', 'javascript:createTab(''pr_prodsched.php'',''Schedule Orders'',0)', 'Schedule orders for production', '', 'PR', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(80, 80, 1, 'Purchase Orders', 'javascript:createTab(''pr_purchord.php'',''Purchase Orders'',0)', 'Generate purchase orders', '', 'PR', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(81, 81, 1, 'Track Production', 'javascript:createTab(''pr_trackprod.php'',''Track Production'',0)', 'Track production status', '', 'PR', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(82, 82, 0, 'Housekeeping', '', 'Housekeeping menu', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(83, 83, 1, 'Audit Trail', 'javascript:createTab(''hs_audit.php'',''Audit Trail'',0)', 'List audit trail between dates', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(84, 84, 1, 'Year End Routine', 'javascript:createTab(''hs_yrend.php'',''Year End Routine'',0)', 'List audit trail between dates', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(85, 85, 1, 'Transaction Summary', 'javascript:createTab(''hs_transsum.php'',''Transaction Summary'',0)', 'Display consolidation of types of transaction.', '', 'AC', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(86, 86, 1, 'Maintenance', '', 'System maintenance menu', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(87, 87, 2, 'Update users', 'javascript:createTab(''hs_users.php'',''Update users'',0)', 'Assign passwords to users and users to menu groups.', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(88, 88, 2, 'Update Menu Groups', 'javascript:createTab(''hs_updtmeng.php'',''Update Menu Groups'',0)', 'Assign menu options to groups of users.', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(89, 89, 2, 'Setup', 'javascript:createTab(''hs_setup.php'',''Setup Company Details'',0)', 'Setup company Details', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(90, 90, 1, 'Backup', 'javascript:createTab(''hs_bkup.php'',''Backup'',0)', 'Backup database for this company', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(91, 91, 1, 'Print layout template', 'javascript:createTab(''hs_pdfgrid.php'',''Print layout template'',0)', 'Print layout template for pdf trading forms', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y'),
(92, 92, 1, 'Forex Factors', 'javascript:createTab(''hs_forex.php'',''Update forex factors'',0)', 'Update forex factors', '', '*', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y')";
$r = mysql_query($sql) or die(mysql_error().' '.$sql);



	//populate templates
	// c_stemplate
	$cString = "insert into c_stemplate (item) values ('page')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('watermark')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('image')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('header1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('header2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('gridtitle')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('griddetail')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('docdate')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('ref1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('ref2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('gst')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('toad7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromphone')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromfax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('fromemail')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into c_stemplate (item) values ('box1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('box9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rbox9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label10')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label11')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label12')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label13')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label14')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label15')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label16')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label17')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label18')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label19')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('label20')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footmessage')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('footlabel9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('totval')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('tottax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('totdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('remmitance')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('remitbox')";
	$result = mysql_query($cString) or die(mysql_error());																																								
	$cString = "insert into c_stemplate (item) values ('remitlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('remitlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('remitlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('remitlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('remitlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rtotdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('ramtpaid')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('rref1')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into c_stemplate (item) values ('worknotesbox')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('worknoteshead')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('worknotes')";
	$result = mysql_query($cString) or die(mysql_error());		
	$cString = "insert into c_stemplate (item) values ('cfootlabel1')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);

	$cString = "insert into c_stemplate (item) values ('cfootlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('cfootlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('cfootlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into c_stemplate (item) values ('cfootlabel5')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into c_stemplate (item) values ('cwatermark')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into c_stemplate (item) values ('notes')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);																					
																				
	// invtemplate																																		
	$cString = "insert into invtemplate (item) values ('page')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('watermark')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('image')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('header1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('header2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('gridtitle')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('griddetail')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('docdate')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('ref1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('ref2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('gst')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('toad7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromphone')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromfax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('fromemail')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into invtemplate (item) values ('box1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('box9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rbox9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label10')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label11')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label12')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label13')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label14')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label15')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label16')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label17')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label18')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label19')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('label20')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footmessage')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('footlabel9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('totval')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('tottax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('totdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('remmitance')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('remitbox')";
	$result = mysql_query($cString) or die(mysql_error());																																								
	$cString = "insert into invtemplate (item) values ('remitlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('remitlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('remitlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('remitlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('remitlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rtotdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('ramtpaid')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('rref1')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into invtemplate (item) values ('worknotesbox')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('worknoteshead')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('worknotes')";
	$result = mysql_query($cString) or die(mysql_error());		
	$cString = "insert into invtemplate (item) values ('cfootlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('cfootlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('cfootlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('cfootlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into invtemplate (item) values ('cfootlabel5')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into invtemplate (item) values ('cwatermark')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into invtemplate (item) values ('notes')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);																					
																				
														
																				
	// rectemplate																																		
	$cString = "insert into rectemplate (item) values ('page')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('watermark')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('image')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('header1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('header2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('gridtitle')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('griddetail')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('docdate')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('ref1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('ref2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('gst')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('toad7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromphone')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromfax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('fromemail')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into rectemplate (item) values ('box1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('box9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rbox9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label10')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label11')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label12')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label13')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label14')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label15')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label16')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label17')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label18')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label19')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('label20')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footmessage')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('footlabel9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('totval')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('tottax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('totdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('remmitance')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('remitbox')";
	$result = mysql_query($cString) or die(mysql_error());																																								
	$cString = "insert into rectemplate (item) values ('remitlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('remitlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('remitlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('remitlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('remitlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rtotdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('ramtpaid')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('rref1')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into rectemplate (item) values ('worknotesbox')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('worknoteshead')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('worknotes')";
	$result = mysql_query($cString) or die(mysql_error());		
	$cString = "insert into rectemplate (item) values ('cfootlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('cfootlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('cfootlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('cfootlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rectemplate (item) values ('cfootlabel5')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into rectemplate (item) values ('cwatermark')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into rectemplate (item) values ('notes')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);																					
																				
										
										
										
	// crntemplate																																		
	$cString = "insert into crntemplate (item) values ('page')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('watermark')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('image')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('header1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('header2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('gridtitle')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('griddetail')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('docdate')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('ref1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('ref2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('gst')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('toad7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromphone')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromfax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('fromemail')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into crntemplate (item) values ('box1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('box9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rbox9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label10')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label11')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label12')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label13')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label14')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label15')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label16')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label17')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label18')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label19')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('label20')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footmessage')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('footlabel9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('totval')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('tottax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('totdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('remmitance')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('remitbox')";
	$result = mysql_query($cString) or die(mysql_error());																																								
	$cString = "insert into crntemplate (item) values ('remitlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('remitlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('remitlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('remitlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('remitlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rtotdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('ramtpaid')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('rref1')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into crntemplate (item) values ('worknotesbox')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('worknoteshead')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('worknotes')";
	$result = mysql_query($cString) or die(mysql_error());		
	$cString = "insert into crntemplate (item) values ('cfootlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('cfootlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('cfootlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('cfootlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into crntemplate (item) values ('cfootlabel5')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into crntemplate (item) values ('cwatermark')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into crntemplate (item) values ('notes')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);																					
																				
										
																				
	// grntemplate																																		
	$cString = "insert into grntemplate (item) values ('page')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('watermark')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('image')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('header1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('header2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('gridtitle')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('griddetail')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('docdate')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('ref1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('ref2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('gst')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('toad7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromphone')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromfax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('fromemail')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into grntemplate (item) values ('box1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('box9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rbox9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label10')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label11')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label12')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label13')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label14')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label15')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label16')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label17')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label18')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label19')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('label20')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footmessage')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('footlabel9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('totval')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('tottax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('totdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('remmitance')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('remitbox')";
	$result = mysql_query($cString) or die(mysql_error());																																								
	$cString = "insert into grntemplate (item) values ('remitlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('remitlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('remitlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('remitlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('remitlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rtotdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('ramtpaid')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('rref1')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into grntemplate (item) values ('worknotesbox')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('worknoteshead')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('worknotes')";
	$result = mysql_query($cString) or die(mysql_error());		
	$cString = "insert into grntemplate (item) values ('cfootlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('cfootlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('cfootlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('cfootlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into grntemplate (item) values ('cfootlabel5')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into grntemplate (item) values ('cwatermark')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into grntemplate (item) values ('notes')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);																					
																				
										
																				
	// rettemplate																																		
	$cString = "insert into rettemplate (item) values ('page')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('watermark')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('image')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('header1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('header2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('gridtitle')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('griddetail')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromname')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('docdate')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('ref1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('ref2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('gst')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('toad7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromaddress')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromad1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromad2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromad3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromad4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromphone')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromfax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('fromemail')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into rettemplate (item) values ('box1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('box9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rbox9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label10')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label11')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label12')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label13')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label14')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label15')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label16')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label17')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label18')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label19')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('label20')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footmessage')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footbox1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel6')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel7')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel8')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('footlabel9')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('totval')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('tottax')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('totdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('remmitance')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('remitbox')";
	$result = mysql_query($cString) or die(mysql_error());																																								
	$cString = "insert into rettemplate (item) values ('remitlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('remitlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('remitlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('remitlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('remitlabel5')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rtotdue')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('ramtpaid')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('rref1')";
	$result = mysql_query($cString) or die(mysql_error());	
	$cString = "insert into rettemplate (item) values ('worknotesbox')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('worknoteshead')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('worknotes')";
	$result = mysql_query($cString) or die(mysql_error());		
	$cString = "insert into rettemplate (item) values ('cfootlabel1')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('cfootlabel2')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('cfootlabel3')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('cfootlabel4')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into rettemplate (item) values ('cfootlabel5')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into rettemplate (item) values ('cwatermark')";
	$result = mysql_query($cString) or die(mysql_error());																					
	$cString = "insert into rettemplate (item) values ('notes')";
	$result = mysql_query($cString) or die(mysql_error());																					
																				
											
														
	// populate globals
	$dt = date("Y-m-d");
	$cString = "insert into globals (coyname,bedate,yrdate,lstatdt) values ('".$sname."','".$dt."','".$dt."','".$dt."')";
	$result = mysql_query($cString) or die(mysql_error().' '.$cString);
	
	// populate numbers
	$cString = "insert into numbers (inv) values (0)";
	$result = mysql_query($cString) or die($cString);
		
	// insert system accounts into glmast 
	$cString = "INSERT INTO `glmast` (`uid`, `grp`, `account`, `accountno`, `branch`, `sub`, `obal`, `obalm`, `prevbal`, `lastyear`, `recon`, `blocked`, `active`, `paygst`, `sc`, `ctrlacc`, `system`, `ird`, `ird2`) VALUES
(1, 'LIB', 'CREDITORS CONTROL', 851, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(2, 'OAS', 'DEBTORS CONTROL', 801, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(3, 'INV', 'FIXED ASSETS CONTROL', 701, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(4, 'INV', 'ACCUMULATED DEPRECIATION', 702, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(5, 'EQT', 'INTER BRANCH TRANSFER', 997, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(6, 'EXP', 'DEPRECIATION', 250, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(7, 'EXP', 'SALARIES & EXPENSES', 500, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(8, 'LIB', 'PROVISION FOR PAY', 880, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(9, 'EQT', 'RETAINED EARNINGS', 998, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(10, 'EQT', 'JOURNAL', 999, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(11, 'BAN', 'BANK', 751, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(12, 'BAN', 'CASH ON HAND', 755, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(13, 'LIB', 'CREDIT CARDS', 860, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(14, 'COS', 'OPENING STOCK', 181, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(15, 'COS', 'CLOSING STOCK', 187, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(16, 'EXP', 'STOCK ADJUSTMENT', 699, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(17, 'OAS', 'STOCK ON HAND', 825, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(18, 'COS', 'WORK IN PROGRESS', 190, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(19, 'LIB', 'TRADING TAX PAYABLE', 870, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(20, 'INC', 'DISCOUNT ON SALES', 76, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(21, 'COS', 'DISCOUNT ON PURCHASES', 186, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(22, 'INC', 'OVERS & UNDERS ON EXCHANGE', 79, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
(23, 'XXX', 'JOURNAL DUMMY ACCOUNT', 1000, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
(24, 'XXX', 'ACCUMULATED DEPRECIATION', 5000, 'AA', 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0)";
$result = mysql_query($cString) or die(mysql_error().$cString);

	
	// insert asset headings
	$cString = "insert into assetheadings (hcode,heading) values ('LD','LAND')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('BL','BUILDINGS & IMPROVEMENTS')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('MV','MOTOR VEHICLES')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('PE','PLANT & EQUIPMENT')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('FF','FURNITURE & FITTINGS')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('MS','MISCELLANEOUS')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('S1','SPARE')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('S2','SPARE')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('S3','SPARE')";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into assetheadings (hcode,heading) values ('S4','SPARE')";
	$result = mysql_query($cString) or die(mysql_error());

	//insert controling account numbers into z_acno
	$cString = "insert into z_acno (depn) values (250)";
	$result = mysql_query($cString) or die(mysql_error().$cstring);

/*
	// insert autaxtypes
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('GST','Goods & Services Tax',10.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('FRE','GST Free',0.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('GNR','GST Non Registered',0.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('N-T','Not Reportable',0.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('INP','Input Taxed',10.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('IMP','Import Duty',5.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('WST','Wholesale Sales Tax',22.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('ABN','No ABN Withholding',-46.50)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('CAP','Capital Aquisitions',10.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('GW','Consolidated WEG & WET',41.90)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('LCG','Consolidatd LCT & GST',35.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('LCT','Luxury Car Tax',25.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('VWH','Voluntary Withholdings',-20.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('WEG','GST on Wine Equalisation',12.90)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into autaxtypes (tax,description,taxpcent) values ('WET','Wine Equalisation Tax',29.00)";
	$result = mysql_query($cString) or die(mysql_error());
				
	// insert nztaxtypes
	$cString = "insert into nztaxtypes (tax,description,taxpcent) values ('GST','Goods & Services Tax',12.50)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into nztaxtypes (tax,description,taxpcent) values ('Z-R','Zero Rated',0.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into nztaxtypes (tax,description,taxpcent) values ('EXM','Exempt',0.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into nztaxtypes (tax,description,taxpcent) values ('N-T','No Tax',0.00)";
	$result = mysql_query($cString) or die(mysql_error());
	$cString = "insert into nztaxtypes (tax,description,taxpcent) values ('IMP','Import',100.00)";
	$result = mysql_query($cString) or die(mysql_error());
*/

	// insert main branch
	$cString = "insert into branch (branch,branchname) values ('AA','Main')";
	$result = mysql_query($cString) or die(mysql_error().$cstring);

$cString = "
INSERT INTO `glcodes` (`uid`, `grp`, `lft`, `rgt`, `level`, `glgroup`, `range_start`, `range_end`) VALUES
(1, 'P_L', 1, 44, 0, 'PROFIT AND LOSS', 1, 700),
(2, 'INC', 1, 19, 1, '   Income', 1, 80),
(3, 'SIN', 1, 2, 1, '   Sundry Income', 81, 100),
(4, 'COS', 1, 2, 1, '   Cost of Sales', 101, 200),
(5, 'EXP', 1, 2, 1, '   Expenses', 201, 700),
(6, 'B_S', 0, 0, 0, 'BALANCE SHEET', 701, 999),
(7, 'ASS', 6, 7, 1, '   Assets', 701, 850),
(8, 'INV', 7, 45, 2, '      Investment', 701, 750),
(9, 'BAN', 7, 46, 2, '      Bank Accounts', 751, 800),
(10, 'OAS', 7, 47, 2, '      Other Assets', 801, 850),
(11, 'LIB', 6, 48, 1, '   Liabilities', 851, 900),
(12, 'EQT', 6, 49, 1, '   Equity', 901, 999)";
$result = mysql_query($cString) or die(mysql_error().$cstring);

$cSting = "
INSERT INTO `tbtemplate` (`uid`, `item`, `include`, `xcoord`, `ycoord`, `font`, `drawcolor`, `fillcolor`, `textcolor`, `linewidth`, `cellwidth`, `gridwidths`, `cellheight`, `content`, `border`, `nextpos`, `align`, `fill`) VALUES
(1, 'page', 'Y', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, 'P,mm,A4', '0', 0, 'L', 0),
(2, 'watermark', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(3, 'image', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(4, 'header1', 'Y', 80, 15, 'Arial,B,16', '0', '0', '0', '0.2', 0, '', 0, 'Trial Balance', '0', 0, 'L', 0),
(5, 'header2', 'Y', 10, 22, 'Arial,B,12', '0', '0', '0', '0.2', 0, '', 0, ' ', '0', 0, 'L', 0),
(6, 'gridtitle', 'Y', 10, 30, 'Arial,,10', '0', '0', '0', '0.2', 0, '20,12,10,80,25,25,25', 0, 'Account No.,Branch,Sub,Account Name,Debit,Credit,Last Year', '0', 0, 'L,L,L,L,R,R,R', 0),
(7, 'griddetail', 'Y', 10, 35, 'Arial,,9', '0', '0', '0', '0.2', 0, '20,12,10,80,25,25,25 	', 3, 'C,C,C,C,N,N,N', '0', 0, 'L,C,C,L,R,R,R', 0),
(8, 'toname', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(9, 'fromname', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(10, 'docdate', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(11, 'ref1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(12, 'ref2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(13, 'gst', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(14, 'toaddress', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(15, 'toad1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(16, 'toad2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(17, 'toad3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(18, 'toad4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(19, 'toad5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(20, 'toad6', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(21, 'toad7', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(22, 'fromaddress', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(23, 'fromad1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(24, 'fromad2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(25, 'fromad3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(26, 'fromad4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(27, 'fromphone', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(28, 'fromfax', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(29, 'fromemail', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(30, 'box1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(31, 'box2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(32, 'box3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(33, 'box4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(34, 'box5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(35, 'box6', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(36, 'box7', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(37, 'box8', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(38, 'box9', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(39, 'rbox1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(40, 'rbox2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(41, 'rbox3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(42, 'rbox4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(43, 'rbox5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(44, 'rbox6', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(45, 'rbox7', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(46, 'rbox8', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(47, 'rbox9', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(48, 'label1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(49, 'label2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(50, 'label3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(51, 'label4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(52, 'label5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(53, 'label6', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(54, 'label7', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(55, 'label8', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(56, 'label9', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(57, 'label10', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(58, 'label11', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(59, 'label12', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(60, 'label13', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(61, 'label14', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(62, 'label15', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(63, 'label16', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(64, 'label17', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(65, 'label18', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(66, 'label19', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(67, 'label20', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(68, 'footmessage', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(69, 'footbox1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(70, 'footlabel1', 'Y', 42, 270, 'Arial,B,10', '0', '0', '0', '0.2', 70, '', 4, 'Balances:-', '0', 0, 'L', 0),
(71, 'footlabel2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(72, 'footlabel3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(73, 'footlabel4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(74, 'footlabel5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(75, 'footlabel6', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(76, 'footlabel7', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(77, 'footlabel8', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(78, 'footlabel9', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(79, 'totdebit', 'Y', 122, 270, 'Arial,,10', '0', '0', '0', '0.2', 25, '', 4, '', 'TB', 0, 'R', 0),
(80, 'totcredit', 'Y', 147, 270, 'Arial,,10', '0', '0', '0', '0.2', 25, '', 4, '', 'TB', 0, 'R', 0),
(81, 'totlastyear', 'Y', 172, 270, 'Arial,,10', '0', '0', '0', '0.2', 25, '', 4, '', 'TB', 0, 'R', 0),
(82, 'remmitance', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(83, 'remitbox', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(84, 'remitlabel1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(85, 'remitlabel2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(86, 'remitlabel3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(87, 'remitlabel4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(88, 'remitlabel5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(89, 'rtotdue', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(90, 'ramtpaid', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(91, 'rref1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(92, 'worknotesbox', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(93, 'worknoteshead', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(94, 'worknotes', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(95, 'cfootlabel1', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(96, 'cfootlabel2', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(97, 'cfootlabel3', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(98, 'cfootlabel4', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(99, 'cfootlabel5', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(100, 'cwatermark', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0),
(101, 'notes', 'N', 0, 0, 'Arial,,10', '0', '0', '0', '0.2', 0, '', 0, '', '0', 0, 'L', 0)";
$result = mysql_query($cString) or die(mysql_error().$cstring);

$sql = "
INSERT INTO `stkpricepcent` (`priceband`, `pcent`) VALUES
('Retail', '100.00')";
$result = mysql_query($sql) or die(mysql_error().$cstring);



?>