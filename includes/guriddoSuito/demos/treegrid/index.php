<!DOCTYPE html>
<html lang="en">
<head>
    <title>Guriddo  Suito - jQuery based HTML5 User Interface suite for Javascript, PHP</title>
    <script src="../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css" media="screen" />
<style type="text/css">
	.examples {padding-left: 10px;height:130px;}
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
      }
</style>
    <script type="text/javascript">
            jQuery(document).ready(function() {
                $("#accordion").accordion();
                $("#demoFrame").attr("src", "nested_model/default.php");
            });
    </script>
</head>	
<body>
    <div style='width:100%;text-align: center;'>
		  <h3>Guriddo TreeGrid PHP Demo</h3>
	  </div>

    <form id="Form1" >
        <div id="wrap">
            <!-- Content -->            
            <table cellspacing="10" cellpadding="10">
                <tr>
                <td width="250px" style="vertical-align:top">
                    <div id="accordion" style="font-size: 87%; height: 450px; width: 240px;">
                        <h3><a href="#">Tree Models</a></h3>
                        <div>		                                        
	                        <ul class="examples">
                                <li>
                                    <a href="nested_model/default.php" target="demoFrame">Nested set model</a>
                                </li>
                                <li>
                                    <a href="adj_model/default.php" target="demoFrame">Adjacency model</a>&nbsp;<sup><font style="color:red">New</font></sup>
                                </li>
                            </ul>		                                        
	                    </div>
	                    <h3><a href="#">Loading</a></h3>
	                    <div>		                                 
	                        <ul class="examples">
                                <li>
                                    <a href="pagging/default.php" target="demoFrame">Pagging with tree <sup><font style="color:red">New</font></sup></a>
                                </li>
                                <li>
                                    <a href="autoloadnodes/default.php" target="demoFrame">Auto loading nodes</a>
                                </li>
                                <li>
                                    <a href="loadallcollapsed/default.php" target="demoFrame">Load all nodes collapsed</a>
                                </li>
                                <li>
                                    <a href="loadallexpanded/default.php" target="demoFrame">Load all nodes expanded</a>
                                </li>
                            </ul>      
	                    </div>
	                    <h3><a href="#">Look and Feel</a></h3>
	                    <div>
	                        <ul class="examples">
                                <li>
                                    <a href="expandcolclick/default.php" target="demoFrame">Expand a node by click the name</a>
                                </li>
                                <li>
                                    <a href="fixedheight/default.php" target="demoFrame">Fixed height</a>
                                </li>
                                <li>
                                    <a href="iconchange/default.php" target="demoFrame">Icon can be changed</a>
                                </li>
                                <li>
                                    <a href="icondb/default.php" target="demoFrame">Use icon from database field</a>
                                </li>
                            </ul>                                                                                           
	                    </div>
	                    <h3><a href="#">Functionalities</a></h3>
	                    <div>
	                        <ul class="examples">
                                <li>
                                    <a href="keyboardnav/default.php" target="demoFrame">Navigation with keyboard</a>
                                </li>
                                <li>
                                    <a href="onselectevent/default.php" target="demoFrame">Action on selecting node</a>
                                </li>
                                <li>
                                    <a href="simpletree/default.php" target="demoFrame">Simulate simple tree</a>
                                </li>
	                        </ul> 
	                    </div>
	                    <h3><a href="#">Add/Update/Delete</a></h3>
	                    <div>
	                        <ul class="examples">
                                <li>
                                    <a href="addnodes/default.php" target="demoFrame">Ading Node</a>
                                </li>
                                <li>
									<a href="delnodes/default.php" target="demoFrame">Delete nodes</a>
								</li>
                                <li>
                                    <a href="addeditdelete/default.php" target="demoFrame">Add, edit, delete nodes</a>
                                </li>
	                        </ul>                                       
						</div>

					</div> 
				</td>
			    <td width="720px" valign="top">
			        <iframe id="demoFrame" name="demoFrame" style="width: 720px; height:600px; border-width:0; border-style:none; border-color:black;"></iframe>			                            
			    </td>
				</tr>
			</table>                            
            <!-- Content -->
        </div>
    </form>
</body>
</html>