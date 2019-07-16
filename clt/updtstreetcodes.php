
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update NZ Street Post Codes</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<!-- In head section we should include the style sheet for the grid -->
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jqgrid/themes/coffee/grid.css" />

<!-- and at end the jqGrid Java Script file -->
<script src="../includes/jqgrid/jquery.jqGrid.js" type="text/javascript"></script>

<script type="text/javascript">

window.name = 'updtstreets';

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
      <td align="left">Street   </td>
      <td align="left"><input type="text" id="searchst" size="10" onkeypress="doSearch1st()" /></td>
	</tr>
    <tr>
      <td align="left">Suburb/Town       </td>
      <td align="left"><input type="text" id="searchsub" size="10" onkeydown="doSearch2st()" /></td>
    </tr>
    <tr>
      <td align="left">Area       </td>
      <td align="left"><input type="text" id="searcharea" size="10" onkeydown="doSearch3st()" /></td>
    </tr>
    <tr>
      <td align="left">Post Code        </td>
      <td align="left"><input type="text" id="searchpc" size="10" onkeydown="doSearch4st()" /></td>
    </tr>
	<tr>
    	<td colspan="2"><?php include "getstreets.php" ?></td>
    </tr>
  </table>
</form>



</body>
</html>