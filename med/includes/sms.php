<?
/*
 Simple and easy for modification, PHP script for SMS sending through HTTP with you own Sender ID and delivery reports. 
 You just have to type your account information on www.2-waysms.com and upload file on server.
*/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SMSER</title>
<style type="text/css">
body{
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif; 
	font-size:12px;
}
p, h1, form, button{border:0; margin:0; padding:0;}
.spacer{clear:both; height:1px;}
/* ----------- My Form ----------- */
.myform{
	margin:0 auto;
	width:400px;
	padding:14px;
}
/* ----------- stylized ----------- */
	#stylized{
		border:solid 2px #b7ddf2;
		background:#ebf4fb;
	}
	#stylized h1 {
		font-size:14px;
		font-weight:bold;
		margin-bottom:8px;
	}
	#stylized p{
		font-size:11px;
		color:#666666;
		margin-bottom:20px;
		border-bottom:solid 1px #b7ddf2;
		padding-bottom:10px;
	
}
	</style> 
</head>

<body>
<?

$option = $_REQUEST["option"];
$text = $_REQUEST["text"];
$to = $_REQUEST["to"];
$senderid = $_REQUEST["senderid"];

	switch ($option) {

	case sendsms:
		
		if ($text == "") { echo "Error!<br>Text not entered<br><a href=\"javascript:history.back(-1)\">Go Back</a>"; die; } else { }
		if ($to == "") { echo "Error!<br>Number not entered<br><a href=\"javascript:history.back(-1)\">Go Back</a>"; die; } else { }
		if ($senderid == "") { echo "Error!<br>From not entered<br><a href=\"javascript:history.back(-1)\">Go Back</a>"; die; } else { }
		$to_arr = explode(";", $to);

		foreach ($to_arr as $to_x){		
			$url = "http://www.2-waysms.com/my/api/sms.php";
		
			$postfields = array ( "from" => "********", // Change ********, and put you'r SMS Number in www.2-waysms.com account
			"token" => "********", // Change ********, and put you'r token code in www.2-waysms.com account
			"text" => "$text", // do not need to change
			"to" => "$to_x", // do not need to change
			"senderid" => "$senderid"); // do not need to change
			if (!$curld = curl_init()) {
			echo "Could not initialize cURL session.";
			exit;
			}
			curl_setopt($curld, CURLOPT_POST, true);
			curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($curld, CURLOPT_URL, $url);
			curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($curld);
			curl_close ($curld);
		}
		echo "Message Status: $out[1]<br><a href=\"smser_multi.php\">Send New</a>"; 

		//Header("Location: $PHP_SELF");
	break;

	default:

	echo "<div id=\"stylized\" class=\"myform\">"
	   ."<h1>Bulk SMS Sending Script</h1>"
	   ."<form method=post action=\"$PHP_SELF?option=sendsms\">"
	   ."<table border=\"0\">"

	   ."<tr>"
		 ."<td>From</td>"
		 ."<td><input type=\"text\" name=\"senderid\"></td>"
	   ."</tr>"

	   ."<tr>"
		 ."<td>Number (separate numbers with ;)</td>"
		 ."<td><textarea rows=\"4\" cols=\"25\" name=\"to\"></textarea></td>"
	   ."</tr>"
	   ."<tr>"
		 ."<td>Message</td>"
		 ."<td><textarea rows=\"4\" cols=\"25\" name=\"text\"></textarea></td>"
	   ."</tr>"
	   ."<tr>"
		 ."<td>&nbsp;</td>"
		 ."<td><input type=submit name=submit value=Send>"
		 ."<div class=\"spacer\"></div></td>"
	   ."</tr>"
	   ."</table>"
	   ."</form>"
	."</div>";


	}

?>
</body>
</html>