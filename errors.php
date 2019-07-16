<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Errors</title>
</head>

<body>


<br />
<font size='1'><table class='xdebug-error xe-uncaught-exception' dir='ltr' border='1' cellspacing='0'
 cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f
; font-size: x-large;'>( ! )</span> Fatal error: Uncaught exception 'PDOException' with message 'SQLSTATE
[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual
 that corresponds to your MySQL server version for the right syntax to use near '426.87 where itemcode
 = 'TOWER'' at line 1' in C:\wamp\www\biztracc\includes\DBClass.php on line <i>59</i></th></tr>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f
; font-size: x-large;'>( ! )</span> PDOException: SQLSTATE[42000]: Syntax error or access violation:
 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version
 for the right syntax to use near '426.87 where itemcode = 'TOWER'' at line 1 in C:\wamp\www\biztracc
\includes\DBClass.php on line <i>59</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align
='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left'
 bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0036</td><td bgcolor
='#eeeeec' align='right'>877240</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp\www\biztracc
\fin\includes\ajaxPostTrade.php' bgcolor='#eeeeec'>..\ajaxPostTrade.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0917</td><td bgcolor
='#eeeeec' align='right'>952144</td><td bgcolor='#eeeeec'>DBClass->execute(  )</td><td title='C:\wamp
\www\biztracc\fin\includes\ajaxPostTrade.php' bgcolor='#eeeeec'>..\ajaxPostTrade.php<b>:</b>1450</td
></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.0917</td><td bgcolor
='#eeeeec' align='right'>952192</td><td bgcolor='#eeeeec'><a href='http://www.php.net/PDOStatement.execute'
 target='_new'>execute</a>
(  )</td><td title='C:\wamp\www\biztracc\includes\DBClass.php' bgcolor='#eeeeec'>..\DBClass.php<b>:<
/b>59</td></tr>
</table></font>
</body>
</html>