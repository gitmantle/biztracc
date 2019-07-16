
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update NZ Box Post Codes</title>

<script type="text/javascript">

window.name = 'updtboxes';


</script>
</head>
<body>
<form name="form1" id="form1" method="post" action="">
<br>
  <table>
    <tr>
      <th colspan="2" align="left">Search</th>
    </tr>
    <tr>
      <td align="left">Post Office        </td>
      <td align="left"><input type="text" id="searchpo" size="10" onkeypress="doSearch1bx()" /></td>
	</tr>
    <tr>
      <td align="left">Town        </td>
      <td align="left"><input type="text" id="searchtown" size="10" onkeydown="doSearch2bx()" /></td>
    </tr>
    <tr>
      <td align="left">Post Code        </td>
      <td align="left"><input type="text" id="searchpc" size="10" onkeydown="doSearch3bx()" /></td>
    </tr>
	<tr>
    	<td colspan="2"><?php include "getboxes.php" ?></td>
    </tr>
  </table>
</form>


</body>
</html>