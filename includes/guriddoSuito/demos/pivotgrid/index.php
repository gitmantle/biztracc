<!DOCTYPE html>
<html lang="en">
<head>
    <title>Guriddo  Suito - jQuery based HTML5 User Interface suite for Javascript, PHP</title>
    <script src="../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css" media="screen" />
<style type="text/css">
	.examples {padding-left: 10px;}
</style>
    <script type="text/javascript">
            jQuery(document).ready(function() {
                $("#accordion").accordion();
                $("#demoFrame").attr("src", "grprows/default.php");
            });
    </script>
</head>	
<body>
    <div style='width:100%;text-align: center;'>
		  <h4>Guriddo PivotGrid PHP Demo</h4>
	  </div>
<div style="text-align: center;font-size: 12px">
	<span ><img alt="" src="../css/control_icon.png">  More Demos </span> &nbsp;	
	<a href="http://www.guriddo.net/demo/demos/jqgrid/" ><img alt="" src="../../css/control_icon.png"> jqGrid PHP </a> &nbsp;
	<a href="http://www.guriddo.net/demo/demos/treegrid/" ><img alt="" src="../../css/control_icon.png">  TreeGrid PHP </a> &nbsp;
	<a href="http://www.guriddo.net/demo/demos/jqform/" ><img alt="" src="../../css/control_icon.png">  jqForm PHP </a> &nbsp;
	<a href="http://www.guriddo.net/demo/guriddojs/" ><img alt="" src="../../css/control_icon.png"> jqGrid Java Script</a> &nbsp;
	<a href="http://www.guriddo.net/demo/schedulerphp/" ><img alt="" src="../../css/control_icon.png"> Scheduler </a> &nbsp;
	<a href="http://www.guriddo.net/demo/formphpvb/" ><img alt="" src="../../css/control_icon.png">  PHP Form Visual Builder</a> &nbsp;
</div>

    <form id="Form1" runat="server">
        <div id="wrap">
            <!-- Content -->            
			                <table cellspacing="10" cellpadding="10">
			                    <tr>
			                        <td width="250px" style="vertical-align:top">
                                        <div id="accordion" style="font-size: 87%; height: 250px; width: 240px;">
	                                        <h3><a href="#">Client Side Pivoting</a></h3>
	                                        <div>
	                                            <ul class="examples">
                                                    <li>
                                                        <a href="basic/default.php" target="demoFrame">Basic Pivot</a>
                                                    </li>
                                                    <li>
                                                        <a href="export/default.php" target="demoFrame">Pivot Export <sup style="color:red">New</sup></a>
                                                    </li>
                                                    <li>
                                                        <a href="collapsed/default.php" target="demoFrame">Colapsed data </a>
                                                    </li>
                                                    <li>
                                                        <a href="grprows/default.php" target="demoFrame">Hierarchy </a>
                                                    </li>
                                                    <li>
                                                        <a href="grprowsfoot/default.php" target="demoFrame">Summary groups at footer</a>
                                                    </li>
                                                    <li>
                                                        <a href="frozen/default.php" target="demoFrame">Frozen columns</a>
                                                    </li>
                                                    <li>
                                                        <a href="rpbale/default.php" target="demoFrame">Local data</a>
                                                    </li>
                                                    <li>
                                                        <a href="manymembers/default.php" target="demoFrame">Multiple members</a>
                                                    </li>
                                                    <li>
                                                        <a href="searching/default.php" target="demoFrame">Searching</a>
                                                    </li>
                                                </ul>                                                                                           
	                                        </div>
	                                        <h3><a href="#">More Comming soon</a></h3>
	                                        <div>
	                                        </div>

                                        </div> 
			                        </td>
			                        <td width="720px" valign="top">
			                            <iframe id="demoFrame" 
			                                    name="demoFrame" 
			                                    style="width: 720px; height:600px; border-width:0; border-style:none; border-color:black;">
			                            </iframe>			                            
			                        </td>
			                    </tr>
			                </table>                            
            <!-- Content -->
        </div>
    </form>
</body>
</html>