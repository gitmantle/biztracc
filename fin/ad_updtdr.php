<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Debtors</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "ad_updtdr";

</script>
</head>
<body>
<div style="width: 970px; height: 420px;">
  <form name="form1" id="form1" method="post" action="">
    <table align="left" width="960">
      <tr>
        <td>Company/Last name &nbsp;
          <input type="text" id="searchlegalname" size="12" onkeydown="doSearch(arguments[0]||event)" />
          &nbsp;<img src="../images/Search.gif" alt="Search" title="Search" onclick="gridReload1()" /> &nbsp;&nbsp;</td>
          <td>Add to Debtors from this Client List</td>
      </tr>
      <tr>
        <td><?php include "getdr.php" ?></td>
        <td><?php include "getcltdr.php" ?></td>
    </table>
  </form>
</div>
</body>
</html>