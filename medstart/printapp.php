<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

include_once("../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$mid = $_SESSION['s_memberid'];
$sessionid = session_id();

$db->query("select firstname,lastname,DATE_FORMAT(dob, '%d/%m/%Y') as dob from ".$cltdb.".members where member_id = ".$mid);
$row = $db->single();
extract($row);

$db->query("select ad1,suburb,town,postcode,country from ".$cltdb.".addresses where location = 'Street' and member_id = ".$mid);
$row = $db->single();
extract($row);

$db->query("select comm as email from ".$cltdb.".comms where comms_type_id = 4 and member_id = ".$mid);
$row = $db->single();
extract($row);

$db->query("select concat(country_code,' ',area_code,' ',comm) as phone from ".$cltdb.".comms where comms_type_id = 1 and member_id = ".$mid);
$row = $db->single();
extract($row);

$db->query("select comm as mobile from ".$cltdb.".comms where comms_type_id = 3 and member_id = ".$mid);
$row = $db->single();
extract($row);


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Print Application</title>

<style>
.break { page-break-before: always; }
</style>

</head>

<body>

 	<table width="900" align="center">
    	<tr>
        	<td colspan="4" align="center"><h2>Online application for Membership of Cmeds4U Services</h2></td>
       	</tr>
        <tr>
            <td colspan="4" ><hr size="4" width="880" align="center" />  </td>
        </tr>
        <tr>
          <td colspan="4" style="font-size:16px" align="center">If your browser does not show any Print function, just press Ctrl P for a printout.</td>
        </tr>
        <tr>
          <td colspan="4" style="font-size:16px"><hr size="4" width="880" align="center" /></td>
        </tr>
        <tr>
       	  <td style="font-size:16px">Firstname  </td>
        	<td><?php echo $firstname; ?></td>
        	<td><span style="font-size:16px">Lastname</span></td>
        	<td><?php echo $lastname; ?></td>
       	</tr>
        <tr>
          <td style="font-size:16px">Date of Birth</td>
          <td colspan="3"  style="font-size:16px"><?php echo $dob; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Address</td>
          <td colspan="3" style="font-size:16px">&nbsp;</td>
        </tr>
        <tr>
          <td style="font-size:16px">Line 1</td>
          <td colspan="3"><?php echo $ad1; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Line 2</td>
          <td colspan="3"><?php echo $suburb; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Town</td>
          <td colspan="3"><?php echo $town; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Postcode</td>
          <td colspan="3"><?php echo $postcode; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Country</td>
          <td colspan="3"><?php echo $country; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Email</td>
          <td colspan="3"><?php echo $email; ?></td>
        </tr>
        <tr>
          <td style="font-size:16px">Phone</td>
          <td style="font-size:16px"><?php echo $phone; ?></td>
          <td style="font-size:16px">Mobile</td>
          <td style="font-size:16px"><?php echo $mobile; ?></td>
        </tr>
        <tr>
          <td colspan="4" style="font-size:16px">applies to Cmeds4U for the supply of the medicines listed below for which the applicant agrees to pay in advance on the terms and conditions described in the following sections.</td>
        </tr>
    </table>
    
 	<table width="900" align="center">
        <tr>
            <td colspan="4" ><hr size="4" width="880" align="center" />  </td>
      </tr>
    	<tr>
        	<td>Medicine</td>
            <td>Quantity</td>
            <td>Per</td>
            <td>Monthly Cost incl. GST</td>
        </tr>
        <tr>
            <td colspan="4" ><hr size="4" width="880" align="center" />  </td>
        </tr>
    	<?php

			$fdb = $_SESSION['s_findb'];
			$cdb = $_SESSION['s_cltdb'];
			$mdb = $_SESSION['s_prcdb'];
			
			$db->query("select ".$mdb.".requirements.dosage,".$mdb.".requirements.qty,".$fdb.".stkmast.item,".$fdb.".stkmast.noinunit,case ".$fdb.".stkmast.setsell when 0 then ".$fdb.".stkmast.avgcost else stkmast.setsell end as cost, ".$fdb.".stkmast.deftax from ".$mdb.".requirements,".$fdb.".stkmast where ".$mdb.".requirements.medicineid = ".$fdb.".stkmast.itemid and ".$mdb.".requirements.patientid = ".$mid);
   			$rows = $db->resultset();
			$totcost = 0;
			foreach ($rows as $row) {
				extract($row);
				echo "<tr>";
				echo "<td>".$item."</td>";
				echo "<td>".$qty."</td>";
				echo "<td>".$dosage."</td>";
				
				// calculate quantity required
				
				switch ($dosage) {
					case 'Month';
						$qtyreq = $qty;
					break;
					case 'Week';
						$qtyreq = ($qty * 4);
					break;
					case 'Day';
						$qtyreq = ($qty * 28);
					break;
				}
				
				// get relevant tax	
		
				$db->query("select taxpcent from ".$fdb.".taxtypes where uid = ".$deftax);
				$rowt = $db->single();
				extract($rowt);
				
				// calculate unit/packs required
				
				$unitsreq = ceil($qtyreq/$noinunit);
				$mcost = round(($unitsreq * $cost) * (1 + ($taxpcent / 100)),2);
				$ccost = number_format($mcost,2);
				
				echo "<td>".$ccost."</td>";
				echo "</tr>";
				$totcost = $totcost + $mcost;
			}	
			$totcost = round($totcost,2);
			$totcost = number_format($totcost,2);
			echo "<tr>";
			echo "<td>&nbsp;</td>";
			echo "<td>Total Monthly Cost incl GST</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>".$totcost."</td>";
			
			$db->closeDB();

			?>
</table>
    
    
<h2 class="break" align="center">Terms of Agreement</h2>

 	<table width="900" align="center">
        <tr>
            <td colspan="9" ><hr size="4" width="880" align="center" />  </td>
        </tr>
        <tr>
        <td>
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum. Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.
        </td>
        </tr>
</table>

</body>
</html>