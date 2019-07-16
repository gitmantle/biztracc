<!DOCTYPE html>
<html lang="en">
<head>
    <title>Trirand jqSuite - jQuery based HTML5 User Interface suite for Javascript, PHP, ASP.NET WebForms, ASP.NET MVC</title>
	<script src="../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css" media="screen" />
    <style type="text/css">
		.ui-accordion .ui-accordion-content {
			padding: 1em 0.5em;
		}
	</style>
    <script type="text/javascript">
            jQuery(document).ready(function() {                
                $("#accordion").accordion().css("font-size","85%");
                $("#demoFrame").attr("src", "defaultnodb/default.php");
            });
    </script>
</head>	
<body>
    <form id="Form1">
        <div id="wrap">
            <!-- Content -->                  
            
            <table cellspacing="10" cellpadding="10">
                <tr>
                    <td width="250px" style="vertical-align:top">
                        <div id="accordion" style="font-size: 95%; height: 600px; width: 240px;">
                            <h3><a href="#">Form Views</a></h3>
                            <div>		                                        
                                <ul class="examples">
                                    <li>
                                        <a href="bootstarpside/default.php" target="_blank">Boootstrap: horizontal <sup style="color:red"> New </sup> </a> 
                                    </li>
                                    <li>
                                        <a href="bootstrapvert/default.php" target="_blank">Boootstrap: vertical <sup style="color:red"> New </sup> </a> 
                                    </li>
                                    <li>
                                        <a href="bootstrapinline/default.php" target="_blank">Boootstrap: vertical <sup style="color:red"> New </sup> </a> 
                                    </li>
                                    <li>
                                        <a href="defaultnodb/default.php" target="demoFrame">Different input types</a>
                                    </li>
                                    <li>
                                        <a href="defaultdef/default.php" target="demoFrame">Two column layout</a>
                                    </li>
                                    <li>
                                        <a href="defaulticon/default.php" target="demoFrame">Header and icon</a>
                                    </li>
                                    <li>
                                        <a href="defaultfooter/default.php" target="demoFrame">Footer</a>
                                    </li>
                                    <li>
                                        <a href="labelstyle/default.php" target="demoFrame">Label styling</a>
                                    </li>
                                    <li>
                                        <a href="onecolumn/default.php" target="demoFrame">One column layout</a>
                                    </li>
                                </ul>		                                        
                            </div>
                            <h3><a href="#">HTML5 properties</a></h3>
                            <div>		                                 
                                <ul class="examples">
                                    <li>
                                        <a href="html5placeholder/default.php" target="demoFrame">Placeholder</a>
                                    </li>
                                    <li>
                                        <a href="html5pattern/default.php" target="demoFrame">Patterns</a>
                                    </li>
                                    <li>
                                        <a href="html5types/default.php" target="demoFrame">More input types</a>
                                    </li>
                                </ul>      
                            </div>
                            <h3><a href="#">File Upload and Java Script</a></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="fileupload/default.php" target="demoFrame">File Upload</a>
                                    </li>
                                    <li>
                                        <a href="jsevents/default.php" target="demoFrame">Field Events</a>
                                    </li>
                                    <li>
                                        <a href="jscode/default.php" target="demoFrame">Custom Java Script</a>
                                    </li>
                                </ul> 
                            </div>
                            <h3><a href="#">Using selects</a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="selectstring/default.php" target="demoFrame">Define select as string or array</a>
                                    </li>
                                    <li>
                                        <a href="selectdb/default.php" target="demoFrame">Select as Database query</a>
                                    </li>
                                    <li>
                                        <a href="selectdbstring/default.php" target="demoFrame">Select as query and string</a>
                                    </li>
                                </ul>                                       
                            </div>
                            <h3><a href="#">SQL queries</a></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="sqlquerydata/default.php" target="demoFrame">SQL query data with patrmeter</a>
                                    </li>
                                    <li>
                                        <a href="sqlcheckdata/default.php" target="demoFrame">Custom user input check</a>
                                    </li>
                                    <li>
                                        <a href="sqlsuccess/default.php" target="demoFrame">Custom success function</a>
                                    </li>
                                </ul>
                            </div>
                            <h3><a href="#">Validations</a> <sup style="color:red">New</sup></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="servervalidation/default.php" target="demoFrame">Server validation</a>
                                    </li>
                                    <li>
                                        <a href="sanitize/default.php" target="demoFrame">Sanitize input</a>
                                    </li>
                                    <li>
                                        <a href="sanitizevalidate/default.php" target="demoFrame">Sanitize and Validate at the same time</a>
                                    </li>
                                </ul>
                            </div>
                        </div> 
                    </td>
                    <td width="700px" valign="top">
                        <iframe id="demoFrame" 
                                name="demoFrame" 
                                style="width: 700px; height:600px; border-width:0; border-style:none; border-color:black;">
                        </iframe>			                            
                    </td>
                </tr>
            </table> 
            <!-- Content -->
        </div>
    </form>
</body>
</html>
