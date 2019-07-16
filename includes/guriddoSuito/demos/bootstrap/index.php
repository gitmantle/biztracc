<!DOCTYPE html>
<html lang="en">
<head>
    <title>Guriddo jqGrid - jQuery based grid HTML5 component for Javascript</title>

    <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<!-- Optional theme -->
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"> -->
	<style type="text/css">
	.examples {padding-left: 10px;}
    .wrp { width: 100%; text-align: center; margin-top:15px;}
    .frm { text-align: left; width: 1050px; margin: auto;  }
    .fldLbl { white-space: nowrap; } 
	.panel-body {
		font-size: 85%; 
		height: 300px; 
		overflow: auto;
	}
	</style>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            $("#accordion").collapse();
            $("#demoFrame").attr("src", "functionality/footer/default.php");
			$(".list-group-item").on("click", function(){
				$("span",".gheader").html( $(this).text());
			});
			$("span",".gheader").html( 'Footer customization');
        });
    </script>
</head>
<body>
	<h4 style="text-align: center;" class ="gheader"> Guriddo jqGrid Bootstrap Demo:: <span style="font-size: medium"></span></h4>
	<div id="Form1" class="wrap">
        <div id="wrap" class="frm">
			<div class="btn-group btn-group-justified" role="group" style="margin-bottom:25px;">
				<div class="btn-group" role="group">
					<button class="btn btn-default" > More Demos <span class ="glyphicon glyphicon-hand-right"></span></button>	
				</div>
				<div class="btn-group" role="group">
					<a class="btn btn-default" href="http://www.guriddo.net/demo/treegridjs/" >  TreeGrid JS </a>
				</div>
				<div class="btn-group" role="group">
					<a class="btn btn-default" href="http://www.guriddo.net/demo/pivotgridjs/" >  PivotGrid JS </a>
				</div>
				<div class="btn-group" role="group">
					<a class="btn btn-default" href="http://www.guriddo.net/demo/jqgridphp/" > PHP jqGrid</a>
				</div>
				<div class="btn-group" role="group">
					<a class="btn btn-default" href="http://www.guriddo.net/demo/schedulerphp/" > Scheduler </a>
				</div>
				<div class="btn-group" role="group">
					<a class="btn btn-default" href="http://www.guriddo.net/demo/formphp/" > PHP Form </a>
				</div>
				<div class="btn-group" role="group">
					<a class="btn btn-default" href="http://www.guriddo.net/demo/formphpvb/" > PHP Form  Builder</a>
				</div>
			</div>

            <!-- Content -->
            <table cellspacing="10" cellpadding="10">
                <tr>
                    <td style="vertical-align: top;width:250px;">
						<div class="panel-group" id="accordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Functionality</a>
									</h4>
								</div>
								<div id="collapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="examples list-group">
                                        <a href="functionality/bootstrap4/default.php" class="list-group-item" target="demoFrame">Bootstrap 4 support<sup style="color:red">New</sup></a>
                                        <a href="functionality/savestate/default.php" class="list-group-item" target="demoFrame">Save and Load Grid State <sup style="color:red">New</sup></a>
                                        <a href="functionality/localization/default.php" class="list-group-item" target="demoFrame">Dynamic localization <sup style="color:red">New</sup></a>
                                        <a href="functionality/frozen/default.php" class="list-group-item" target="demoFrame">Frozen Columns</a>
                                        <a href="functionality/frozendyn/default.php" class="list-group-item" target="demoFrame">Dynamic Frozen Columns</a>
                                        <a href="functionality/datacolspan/default.php" class="list-group-item" target="demoFrame">Data colspan</a>
                                        <a href="functionality/keynav/default.php" class="list-group-item" target="demoFrame">Keyboard navigation </a>
                                        <a href="functionality/colmodeltmpl/default.php" class="list-group-item" target="demoFrame">Column model template</a>
                                        <a href="functionality/righttoleft/default.php" class="list-group-item" target="demoFrame">Right-To-Left (RTL) support</a>
                                        <a href="functionality/footer/default.php" class="list-group-item" target="demoFrame">Footer customization</a>
                                        <a href="functionality/footer_format/default.php" class="list-group-item" target="demoFrame">Footer formatting</a>
                                        <a href="functionality/formatters/default.php" class="list-group-item" target="demoFrame">Cell Formatters (built-in)</a>
                                        <a href="functionality/custom_formatter/default.php" class="list-group-item" target="demoFrame">Cell Formatters (custom)</a>
                                        <a href="functionality/custom_button/default.php" class="list-group-item" target="demoFrame">Custom toolbar button</a>
                                        <a href="functionality/custom_link/default.php" class="list-group-item" target="demoFrame">Custom link with function</a>
										</div>
									</div>
								</div>
							</div>
						</div>
                    </td>
                    <td style="vertical-align: top;width:800px;">
                        <iframe id="demoFrame"
                            name="demoFrame"
                            style="width: 803px; height: 1000px; border-width: 0;"></iframe>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
