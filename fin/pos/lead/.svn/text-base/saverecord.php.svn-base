<?php
	include_once "../../includes/db2.php";
	
	$ctype = $_REQUEST['ctype'];
	$legal = $_REQUEST['legal'];
	$trade = $_REQUEST['trade'];
	$abn = $_REQUEST['abn'];
	$compph = $_REQUEST['compph'];
	$compfax = $_REQUEST['compfax'];
	$confname = $_REQUEST['confname'];
	$conlname = $_REQUEST['conlname'];
	$conph = $_REQUEST['conph'];
	$confax = $_REQUEST['confax'];
	$conmobile = $_REQUEST['conmobile'];
	$conpos = $_REQUEST['conpos'];
	$conemail = $_REQUEST['conemail'];
	$addline1 = $_REQUEST['addline1'];
	$addline2 = $_REQUEST['addline2'];
	$addcity = $_REQUEST['addcity'];
	$addstate = $_REQUEST['addstate'];
	$addpcode = $_REQUEST['addpcode'];
	$industry = $_REQUEST['industry'];
	$status = $_REQUEST['status'];
	$cid = $_REQUEST['cid'];

	$ts = date('U');
	if($cid != 0) {
		$sql="INSERT INTO `companies` (`id`,`companyname`,`tradingname`,`abn`,`mainphone`,`indialrange`,
										`fax`,`email`,`website`,`addline1`,`addline2`,`city`,`state`,
										`pcode`,`lastmodified`,`modby`,`status`,`category`,`subcategory`,
										`notes`,`creator`,`salesperson`,`work_status`,`client_type`,
										`ca_username`,`ca_password`,`deleted`,`factored`,`nospam`,
										`created`,`group_id`,`group_parent`,`ca_access`,`client_type_id`,
										`cori`) 
			VALUES (NULL,'$legal','$trade','$abn','$compph','','$compfax','','','$addline1','$addline2','$addcity',
					 '$addstate','$addpcode',NULL,'0','$status','$industry','','','0','0','','Company','','','0',
					'0','0','$ts','','0','0','0','$ctype');";
		$rst=mysql_query($sql);
		$lastid=mysql_insert_id();
	} else {
		$lastid = $cid;
	}
	
	$sql2="INSERT INTO `contacts` (`pid`,`title`,`fname`,`mnames`,`lname`,`phone`,`fax`,`mobile`,
									`email`,`position`,`lastmod`,`modby`,`notes`,`photo`,`sal`,`deleted`,
									`created`,`creator`)
						VALUES ('$lastid','','$confname','','$conlname','$conph','$confax','$conmobile',
								'$conemail','$conpos','','0','','','','0','$ts','0');";
	$rst2=mysql_query($sql2);
	$lastid2=mysql_insert_id();
	
	echo $lastid."#@#".$lastid2;
?>
