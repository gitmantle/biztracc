<!DOCTYPE html>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>A jQuery Confirm Dialog Replacement with CSS3 | Tutorialzine Demo</title>

<link rel="stylesheet" type="text/css" href="includes/jquery.confirm/jquery.confirm.css" />
<script src="includes/jquery/js/jquery.js"></script>
<script src="includes/jquery.confirm/jquery.confirm.js"></script>

<script>

$(document).ready(function(){
	$('#btndelete').click(function(){
		$.alertbox({
			'title'		: 'Delete Confirmation',
			'message'	: 'You are about to delete this item. <br />It cannot be restored at a later time! Continue?',
			'buttons'	: {
				'Yes'	: {
					'class'	: 'blue',
					'action': function(){
						alert('are you sure?');
					}
				},
				'No'	: {
					'class'	: 'gray',
					'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
	});
	
	$('#btnalert').click(function(){
		$.alertbox({
			'title'		: 'You got the message!',
			'message'	: 'You may now carry on <br /> in the knowledge this operation has completed.',
			'buttons'	: {
				'OK'	: {
					'class'	: 'blue',
					'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
	});
	
	
	
});

</script>

</head>
<body>


<?php

$num = 1324.50;

$texte = number_format($num,2);
$texte = $num * -1;
$texte = number_format($texte,2);


?>

   <input name="btndelete" type="button" id="btndelete" value="Delete Confirmation">
   <input name="btnalert" type="button" id="btnalert" value="Alert Message">


</body>
</html>
