<?php
	//ini_set('display_errors', true);
	include_once "../../includes/accesscontrol.php";
	include_once "../../includes/db2.php";
	include_once "../../includes/phpfunc.php";

	echo "<LINK REL=\"Stylesheet\" HREF=\"../../style.css\" TYPE=\"text/css\">";

	$lead_source = $_POST['lsource'];
	$department = $_POST['dept'];
	$product = $_POST['prod'];
	$idnum = $_POST['idnum'];

	$cid = $_POST['rcid'];
	$con = $_POST['rcon'];

	// misc other non existing company info thats not being used.
	if(isset($_POST['territory'])) { $territory = $_POST['territory']; }

?>
<script>
function lview(page,ref)
{
	var winname = "SA"+ref;
	if(page != ""){
		window.open(page, winname, 'width=950, height=700, scrollbars=yes');
	}
}

function dotheclose(ref)
{
	alert(ref);
	var accd = document.getElementById('accyu').value;
	var loc = "../../"+accd+"?id="+ref; 
	if(accd != "0") {
		window.open(loc,'lformp','width=900,height=700');
	}
	parent.close();
}

function startclose(ref)
{
	setTimeout('dotheclose("'+ref+'")',1000);
}
</script>

<?php

	/*==============================================================
	   | Code to create the reference number and update the relevant places for future activities			        |
	   ==============================================================*/
	$sql_code1="SELECT `code`,`item` FROM `zsys_dropdown` WHERE `id` = '$department';";
	$rst_code1=mysql_query($sql_code1) or die("code died");
	$rec_code1=mysql_fetch_row($rst_code1);
		$dept_code = $rec_code1[0];
		$dept_name = $rec_code1[1];
	mysql_free_result($rst_code1);

	$sql_code2="SELECT `code`,`item`,`printacc`,`dephead` FROM `zsys_dropdown` WHERE `id` = '$product';";
	$rst_code2=mysql_query($sql_code2) or die("printacc died");
	$rec_code2=mysql_fetch_row($rst_code2);
		$prod_code = $rec_code2[0];
		$prod_name = $rec_code2[1];
		$print_ac = $rec_code2[2];
		if($print_ac == "") { $print_ac = '0'; }
		$prodheadid = $rec_code2[3];
	mysql_free_result($rst_code2);


	if($idnum == "0" || $idnum == ""){
		/* Get the last used number */
		$sql="SELECT `lastqnum` FROM `globals` WHERE `sys` = 'tom';";
		$rst=mysql_query($sql) or die("lastqnum");
			$rec=mysql_fetch_row($rst);
			$lastnum = $rec[0];
		mysql_free_result($rst);

		$newnum = $lastnum+1; // Add one to the current quote number

		$sql2="UPDATE `globals` SET `lastqnum` = '$newnum' WHERE `sys` = 'tom';";
		$rst2=mysql_query($sql2) or die("globals");


		$refnum = $newnum + 10000;
		$ref = $dept_code . $prod_code . $refnum . "A";
	}else{
		/* Use the existing container number */
		$suff = substr($idnum,strlen($idnum)-1,1);

		$refnum = $idnum;
		$ref = $dept_code . $prod_code . $refnum;
	}

	/* Get Todays Date for record */
	$creation_dt = date('U'); $today_stamp = date('U');


	/* ================================*/
	/* Save all the answers to the dynamically built lead form */
	/* ================================*/
	$i = 0;
	$a = 0;
	$sql3456="SELECT `position` FROM `lead_structure` WHERE `product` = '$product' ORDER BY `position` DESC;";
	$rst3456=mysql_query($sql3456) or die("this died");
	$rec3456=mysql_fetch_row($rst3456);
		$num = $rec3456[0];
		$num2 = mysql_num_rows($rst3456);
	mysql_free_result($rst3456);

	while($a <= $num2) {
		$sql3456="SELECT `position` FROM `lead_structure` WHERE `product` = '$product' AND `position` > $i ORDER BY `position` LIMIT 1;";
		$rst3456=mysql_query($sql3456) or die("died at this point: 66678456");
		$rec3456=mysql_fetch_row($rst3456);
			$nextpos = $rec3456[0];
		mysql_free_result($rst3456);

		$questions[$i] = addslashes($_POST[$i]);
		$ie = "{$i}id";
		$idofs[$i] = $_POST[$ie];
		$provid = $idofs[$i];

		if($questions[$i] != "" && $idofs[$i] != "") {
			//echo ".";
			$sqlee="SELECT `ProvWhat` FROM `lead_structure` WHERE `position` = '$i' AND `product` = '$product';";
			$rstee=mysql_query($sqlee) or die("died 823452346");
			$recee=mysql_fetch_row($rstee);
				$prov = $recee[0];
			mysql_free_result($rstee);

			$sqli="INSERT INTO `lead_data` (`ref`,`question`,`val`,`prod`,`ProvWhat`) VALUES ('$ref','".$idofs[$i]."','".$questions[$i]."','$product','$prov');";
			$rsti=mysql_query($sqli) or die("12355782345568ds");
		}
		$i = $nextpos;
		$a++;
	}


	# Set a couple of hidden field items that are easily referenced by javascript
	echo "<input id=\"rref\" value=\"$ref\" type=\"hidden\">";
	echo "<input id=\"accyu\" value=\"$print_ac\" type=\"text\" style=\"visiblity:hidden;\">";
	
	/* ================================*/
	/* Does this process have a sales section */
	/* ================================*/
	$sql2="SELECT `createssale` FROM `zsys_dropdown` WHERE `id` = '$product';";
	$rst2=mysql_query($sql2) or die("create sale");
	$rec2=mysql_fetch_row($rst2);
		$hassale = $rec2[0];
	mysql_free_result($rst2);
	if($hassale == 0) {
		$hassale = "JobWon";
		$srid = $prodheadid;
		$assdate = $today_stamp;
		$jobbed = $today_stamp;
	}else{
		$hassale = "Yes";
		$srid = "";
		$assdate = "0";
		$jobbed = "0";
	}

	/* =======================================
		Get the selected labour rate. Do we still use this?
	=======================================*/
	$sql_rate="SELECT `position` FROM `lead_structure` WHERE `product` = '$product' AND `ProvWhat` = 'rate';";
	$rst_rate=mysql_query($sql_rate) or die("lab rate thing");
	$rec_rate=mysql_fetch_row($rst_rate);
		$ratepos = $rec_rate[0];
	mysql_free_result($rst_rate);

	$rate = $_POST[''.$ratepos.''];

	/* Did I self assign this lead? */
	$is_mine = $_POST['mylead'];

	/* Check to see if a similar process already exists */

$sqlpro="SELECT `id` FROM `activity_summary` WHERE `id` = '$ref';";
$rstpro=mysql_query($sqlpro) or die($_SERVER['PHP_SELF']+" on line 186");
$recpro=mysql_num_rows($rstpro);

if($recpro == "0"){
	/* Do the insert into activity summary */

	if($is_mine == "on") {
		$sql_insert="INSERT INTO `activity_summary`"
				."(`id`,`dept_id`,`prod_id`,`cid`,`con`,"
				."`creator`,`lead_created`,`assigned`,`salesrep_id`,"
				."`lead_source_id`,`self_gen`,`active`,`ratetype`,`fowner`,`jobbed`)"
				."VALUES ('$ref','$department','$product','$cid','$con',"
				."'$userid','$creation_dt','$today_stamp','$userid',"
				."'$lead_source','1','$hassale','$rate','$userid','$jobbed');";
        		$rst_insert=mysql_query($sql_insert) or die("died3456");
	} else {
		$sql_insert="INSERT INTO `activity_summary`"
				."(`id`,`dept_id`,`prod_id`,`cid`,`con`,"
				."`creator`,`lead_created`,`lead_source_id`,"
				."`active`,`ratetype`,`last_step_td`,`assigned`,`salesrep_id`,`jobbed`)"
				."VALUES ('$ref','$department','$product','$cid','$con',"
				."'$userid','$creation_dt','$lead_source','$hassale',"
				."'$rate','$today_stamp','$today_stamp','$srid','0');";
		$rst_insert=mysql_query($sql_insert) or die("died214235346");
	}

	# Get the summary description for something.... for what? Do we need this?
		$sql="SELECT `val` FROM `lead_data` WHERE `ref` = '$ref' AND `ProvWhat` = 'summary';";
        $rst=mysql_query($sql) or die("summ desc");
        $rec=mysql_fetch_row($rst);
			$desc = addslashes($rec[0]);
       	mysql_free_result($rst);
       	if($desc == "") { $desc = $prod_name; }

       		# Copy the summary description we just got into activity_summary table
        	$sql_insertsumm="UPDATE `activity_summary` SET `summary` = '$desc' WHERE `id` = '$ref';";
			$rst_insertsumm=mysql_query($sql_insertsumm) or die("died updating summary desc thing");

			# select the target date from the lead form - if one was provided
			$sql="SELECT `val` FROM `lead_data` WHERE `ref` = '$ref' AND `ProvWhat` = 'date';";
			$rst=mysql_query($sql) or die("target date");
			$rec=mysql_fetch_row($rst);
				$target_date = $rec[0];
			mysql_free_result($rst);
			if($target_date == "") { $target_date = "Not provided"; }
		else {
			$target_date = revformatdate($target_date);
			$target_date = strtotime($target_date);
		}

	# Set the job_booked field in activity summary to this newly got date --- Do we need this?
	$sql345="UPDATE `activity_summary` SET `job_booked` = '$target_date' WHERE `id` = '$ref';";
	$rst345=mysql_query($sql345) or die("job booked");

	$sqllabtab = "SHOW TABLES LIKE 'lab_rates';";
	$rstlabtab=mysql_query($sqllabtab);
	$reclabtab = mysql_num_rows($rstlabtab);

	if($reclabtab > '0'){

		# Get the global warranty margin and gst rates based on the departments
		$sqlwmg = "SELECT `warranty`,`margin`,`gst` FROM `lab_rates` WHERE `dept_id` = '$department';";
		$rstwmg=mysql_query($sqlwmg) or die("gst etc");
		$recwmg=mysql_fetch_row($rstwmg);
			$warranty = $recwmg[0];
			$margin = $recwmg[1];
			$gst = $recwmg[2];
		mysql_free_result($rstwmg);
	}
		$tval = 0; # default $tval to 0

	# Set default tasks if there are any from the lead forms (the issue fields)
	$sql="SELECT `val` FROM `lead_data` WHERE `ref` = '$ref' AND `ProvWhat` = 'task';";
	$rst=mysql_query($sql) or die("died here too im afraid");
	$recno=mysql_num_rows($rst);
	if($recno > '0'){
		while($rec=mysql_fetch_row($rst)){
			$tval = $tval+1;
			$tid = "T".$tval;

			$sql2="INSERT INTO `labour` (`desc`,`taskid`,`jobid`,`qid`,`ehours`,`qty`) VALUES ('".addslashes($rec[0])."','$tid','$ref','$ref','1','1');";
			$rst2=mysql_query($sql2) or die("sql2");
		}
	}
	mysql_free_result($rst);

	$sql22="INSERT INTO `labour` (`part`,`desc`,`taskid`,`jobid`,`qid`,`subcategory`,`status`,`ehours`) VALUES ('.','.','T0','$ref','$ref','hold','Complete','0');";
	$rst22=mysql_query($sql22) or die("sql22");
	mysql_free_result($rst22);

	$sqlpsctab = "SHOW TABLES LIKE 'psconf_global';";
	$rstpsctab=mysql_query($sqlpsctab);
	$recpsctab = mysql_num_rows($rstpsctab);

	if($recpsctab > '0'){
		# Insert the global values for margin warranty etc into the local table in case they need adjustment
		$sql_insertwmg="INSERT INTO `psconf_global` (`id`,`brand`,`margin`,`warranty`,`gst`,`type4`) VALUES ('$ref','','$margin','$warranty','$gst','');";
		$rst_insertwmg=mysql_query($sql_insertwmg) or die("i global things");
	}
	# This sets the jobbed field in activity summary to 0 - not sure why this is happening
	//$sql34="UPDATE `activity_summary` SET `jobbed` = '0' WHERE `id` = '$ref';";
 	//$rst34=mysql_query($sql34) or die("died setting jobbed to nothing");
	
	$sqlsas2="SELECT `cid`,`salesrep_id`,`dept_id` FROM `activity_summary` WHERE `id` = '$ref';";
	$rstsas2=mysql_query($sqlsas2) or die(mysql_error());
	$recsas2=mysql_fetch_row($rstsas2);
		$tname = gettnamebycid($recsas2[0]);
		$sridi = $recsas2[1];
	mysql_free_result($rstsas2);
	
	$sqlszsd="SELECT `dephead` FROM `zsys_dropdown` WHERE `id` = '".$recsas2[2]."';";
	$rstszsd=mysql_query($sqlszsd) or die(mysql_error());
	$recszsd=mysql_fetch_row($rstszsd);	
		$smid = str_replace("#","",$recszsd[0]);
	mysql_free_result($rstszsd);

    if($is_mine == "on") {
		echo "<script>lview('../../process/interface.php?id=$ref&pleaseclose=1','$ref'); startclose('$ref');</script>";
	} else {
 		echo "<script>startclose('$ref');</script>";
	}

?>

		<table width="100%" height="100%">
			<tr>
			<td class="boxheader" valign="middle"><big><big>Thank you. An activity has been created.</big></big></td>
			</tr>
		</table>
<?php
	}else{
?>
		<table width="100%" height="100%">
			<tr>
				<td class="boxheader" valign="middle"><big><big>A similar process already exists. If you are trying to create a revision of this quote please use the revisions process!</big></big></td>
			</tr>
		</table>
<?php
	}
?>