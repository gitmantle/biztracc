<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Test indemail</title>
<script src="includes/jquery/js/jquery.js" type="text/javascript"></script>
<script>
	function indemail() {
		var name = "Murray Russell";
		var email = "admin@logtracc.co.nz";
		var message = "test indemail again";
		
		jQuery.ajaxSetup({async:false});
		$.get("ajax/ajaxIndexEmail.php", {name: name, email: email, message: message}, function(data){
			alert('Message from email send '+data);																					
																								
																								
		});
		jQuery.ajaxSetup({async:true});
	}
</script>	


</head>

<body>

<script>

indemail();

</script>



</body>
</html>