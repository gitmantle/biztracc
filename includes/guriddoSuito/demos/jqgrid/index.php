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
		$("#demoFrame").attr("src", "loading_data/million_sql/default.php");
	});
</script>
</head>	
<body>
    <div style='width:100%;text-align: center;'>
		  <h3>Guriddo jqGrid PHP Demo</h3>
	  </div>
<div style="text-align: center;font-size: 12px">
	<span ><img alt="" src="../css/control_icon.png">  More Demos </span> &nbsp;	
	<a href="http://www.guriddo.net/demo/demos/treegrid/" ><img alt="" src="../../css/control_icon.png">  TreeGrid PHP </a> &nbsp;
	<a href="http://www.guriddo.net/demo/demos/pivotgrid/" ><img alt="" src="../../css/control_icon.png">  PivotGrid PHP </a> &nbsp;
	<a href="http://www.guriddo.net/demo/guriddojs/" ><img alt="" src="../../css/control_icon.png"> jqGrid Java Script</a> &nbsp;
	<a href="http://www.guriddo.net/demo/schedulerphp/" ><img alt="" src="../../css/control_icon.png"> Scheduler </a> &nbsp;
	<a href="http://www.guriddo.net/demo/demos/jqform/" ><img alt="" src="../../css/control_icon.png"> PHP Form </a> &nbsp;
	<a href="http://www.guriddo.net/demo/formphpvb/" ><img alt="" src="../../css/control_icon.png">  PHP Form Visual Builder</a> &nbsp;
</div>
    <form id="Form1">
        <div id="wrap">
           
            <!-- Content -->            
            
            <table cellspacing="10" cellpadding="10">
                <tr>
                    <td width="250px" style="vertical-align:top">
                        <div id="accordion" style="font-size: 12px; height: 200px; width: 240px;">
                            <h3><a href="#">Performance <font color="red">!!!</font></a></h3>
                            <div style="height:120px">		                                        
                                <ul class="examples">
                                    <li>
                                        <a href="paging/scrollbar_view/default.php" target="demoFrame">Virtual loading with pager info <sup style="color:red">New</sup></a>
                                    </li>
                                    <li>
                                        <a href="loading_data/million_sql/default.php" target="demoFrame">Paging, sorting and searching with 1,000,000 rows</a>
                                    </li>
                                    <li>
                                        <a href="paging/scrollbar/default.php" target="demoFrame">Paging <font style="color:Red">500,000</font> rows with scrollbar [Virtual Scrolling]</a>
                                    </li>                                  
                                </ul>		                                        
                            </div>
                            <h3><a href="#">Functionality / Misc <sup style="color:red">New</sup></a></h3>
                            <div>		                                 
                                <ul class="examples">
                                    <li>
                                        <a href="functionality/custom_col_menu/default.php" target="demoFrame">Custom column menu <sup style="color:red">New</sup></a>
                                    </li>
                                    <li>
                                        <a href="functionality/menubar/default.php" target="demoFrame">Menu bar with actions <sup style="color:red">New</sup></a>
                                    </li>
                                    <li>
                                        <a href="functionality/column_menu/default.php" target="demoFrame">Column menu with actions <sup style="color:red">New</sup></a>
                                    </li>
                                    <li>
                                        <a href="functionality/savestate/default.php" target="demoFrame">Save and Load Grid State <sup style="color:red">New</sup></a>
                                    </li>
                                    <li>
                                        <a href="functionality/localization/default.php" target="demoFrame">Dynamic localization <sup style="color:red">New</sup></a>
                                    </li>
                                    <li>
                                        <a href="functionality/frozen/default.php" target="demoFrame">Frozen Columns</a>
                                    </li>
                                    <li>
                                        <a href="functionality/frozendyn/default.php" target="demoFrame">Dynamic Frozen Columns</a>
                                    </li>
                                    <li>
                                        <a href="functionality/datacolspan/default.php" target="demoFrame">Data colspan</a>
                                    </li>
                                    <li>
                                        <a href="functionality/keynav/default.php" target="demoFrame">Keyboard navigation </a>
                                    </li>
                                    <li>
                                        <a href="functionality/colmodeltmpl/default.php" target="demoFrame">Column model template</a>
                                    </li>
                                    <li>
                                        <a href="functionality/righttoleft/default.php" target="demoFrame">Right-To-Left (RTL) support</a>
                                    </li>
                                    <li>
                                        <a href="functionality/footer/default.php" target="demoFrame">Footer customization</a>
                                    </li>                                                    
                                    <li>
                                        <a href="functionality/footer_format/default.php" target="demoFrame">Footer formatting</a>
                                    </li>                                                    
                                    <li>
                                        <a href="functionality/formatters/default.php" target="demoFrame">Cell Formatters (built-in)</a>
                                    </li>                                                    
                                    <li>
                                        <a href="functionality/custom_formatter/default.php" target="demoFrame">Cell Formatters (custom)</a>
                                    </li>
                                    <li>
                                        <a href="functionality/column_reorder_resize/default.php" target="demoFrame">Column reorder / resize</a>
                                    </li> 
                                    <li>
                                        <a href="functionality/custom_button/default.php" target="demoFrame">Custom toolbar button</a>
                                    </li> 
                                    <li>
                                        <a href="functionality/custom_link/default.php" target="demoFrame">Custom link with function</a>
                                    </li>
                                </ul>      
                            </div>
                            <h3><a href="#">Local Export <sup style="color:red">New</sup></a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="localexport/localexport/default.php" target="demoFrame">Local Data export</a>
                                    </li>
                                    <li>
                                        <a href="localexport/grpheadexport/default.php" target="demoFrame">Export with group header</a>
                                    </li>
                                    <li>
                                        <a href="localexport/groupexport/default.php" target="demoFrame">Grouped data export</a>
                                    </li>
                                    <li>
                                        <a href="localexport/pivot/default.php" target="demoFrame">Pivot export</a>
                                    </li>
                                </ul>                                                                                           
                            </div>
                            <h3><a href="#">Validations <sup style="color:red">New</sup></a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="validations/serverside_validation/default.php" target="demoFrame">Server side validation</a>
                                    </li>
                                    <li>
                                        <a href="validations/validation_sanitation/default.php" target="demoFrame">Server side validation and sanitation</a>
                                    </li>
                                    <li>
                                        <a href="validations/validation_types/default.php" target="demoFrame">Server validation types</a>
                                    </li>
                                    <li>
                                        <a href="validations/clientside_validation/default.php" target="demoFrame">Edit fields validation (client-side)</a>
                                    </li>
                                </ul>                                                                                           
                            </div>
                            <h3><a href="#">Loading Data</a></h3>
                            <div>
                                <ul class="examples">
								    <li>
                                        <a href="integrations/adodb/default.php" target="demoFrame">Adodb support<sup><font style="color:red">New</font></sup></a>
                                    </li>
								    <li>
                                        <a href="loading_data/dynamicgrid/default.php" target="demoFrame">Loading dynamically via ajax <sup><font style="color:red">New</font></sup></a>
                                    </li>
								    <li>
                                        <a href="loading_data/array_data/default.php" target="demoFrame">Using array driver (SQL way)</a>
                                    </li>
								    <li>
                                        <a href="loading_data/array_data_subgrid/default.php" target="demoFrame">Array driver with Subgrid (SQL way)</a>
                                    </li>
                                    <li>
                                        <a href="loading_data/datatable/default.php" target="demoFrame">Loading data from DataTable</a>
                                    </li>
                                    <li>
                                        <a href="loading_data/load_fromfile/default.php" target="demoFrame">Loading data from file</a>
                                    </li>
                                    <li>
                                        <a href="loading_data/local_data/default.php" target="demoFrame">Loading from local data</a>
                                    </li>
                                    <li>
                                        <a href="loading_data/million_sql/default.php" target="demoFrame">1,000,000 rows in one grid</a>
                                    </li>
                                    <li>
                                        <a href="loading_data/multiple/default.php" target="demoFrame">Multiple tables</a>
                                    </li>
                                    <li>
                                        <a href="loading_data/sqldatasource/default.php" target="demoFrame">Loading from SqlDataSource</a>
                                    </li>                                                    
                                    <li>
                                        <a href="loading_data/subgridlocal/default.php" target="demoFrame">Subgrid with local data</a>
                                    </li>                                                    
                                </ul>                                                                                           
                            </div>
                            <h3><a href="#">Sorting<sup><font style="color:red">New</font></sup></a></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="sorting/multiple_sorting/default.php" target="demoFrame">Sorting on multiple columns <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="sorting/automatic/default.php" target="demoFrame">Automatic sorting</a>
                                    </li>
                                    <li>
                                        <a href="sorting/custom/default.php" target="demoFrame">Custom sorting</a>
                                    </li>
                                    <li>
                                        <a href="sorting/customui/default.php" target="demoFrame">Custom sort UI</a>
                                    </li>
                                    <li>
                                        <a href="sorting/disable/default.php" target="demoFrame">Disable sorting</a>
                                    </li>
                                    <li>
                                        <a href="sorting/clientside/default.php" target="demoFrame">Client-Side Events & Methods</a>
                                    </li>                                                    
                                </ul> 
                            </div>
                            <h3><a href="#">Searching / Filtering <sup><font style="color:red">New</font></sup></a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="searching/custom_button/default.php" target="demoFrame"> Custom Button in search<sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="searching/search_all/default.php" target="demoFrame"> Search on all fields</a>
                                    </li>
                                    <li>
                                        <a href="searching/diloag_uniquie/default.php" target="demoFrame"> Unique field in search list</a>
                                    </li>
                                    <li>
                                        <a href="searching/toolbar_multiselect/default.php" target="demoFrame">Multiselect in toolbar search</a>
                                    </li>
                                    <li>
                                        <a href="searching/toolbar_between/default.php" target="demoFrame">Between operator in toolbar </a>
                                    </li>
                                    <li>
                                        <a href="searching/search_toolbar_oper/default.php" target="demoFrame">Toolbar search with operations </a>
                                    </li>
                                    <li>
                                        <a href="searching/custom_search/default.php" target="demoFrame">Custom search form </a>
                                    </li>
                                    <li>
                                        <a href="searching/search_dynamic/default.php" target="demoFrame">Search Upon Loading </a>
                                    </li>
                                    <li>
                                        <a href="searching/group_search/default.php" target="demoFrame">Complex Search Dialog</a>
                                    </li>
                                    <li>
                                        <a href="searching/show_query/default.php" target="demoFrame">Show query string </a>
                                    </li>
                                    <li>
                                        <a href="searching/client_validation/default.php" target="demoFrame">Validate client input</a>
                                    </li>
                                    <li>
                                        <a href="searching/search_template/default.php" target="demoFrame">Custom search templates</a>
                                    </li>
                                    <li>
                                        <a href="searching/search_params/default.php?inv=10300" target="demoFrame">Search with parameters</a>
                                    </li>
                                    <li>
                                        <a href="searching/search_session/default.php" target="demoFrame">Search with session vars</a>
                                    </li>
                                    <li>
                                        <a href="searching/search_dialog/default.php" target="demoFrame">Search Dialog</a>
                                    </li>
                                    <li>
                                        <a href="searching/multi_search_dialog/default.php" target="demoFrame">Search Dialog (Multiple Filters)</a>
                                    </li>
                                    <li>
                                        <a href="searching/search_toolbar/default.php" target="demoFrame">Search Toolbar</a>
                                    </li>
                                </ul>                                       
                            </div>
                            <h3><a href="#">Edit, Add, Delete Rows </a></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="editing_data/inline_on_enter/default.php" target="demoFrame">Custom actions an inline save<sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="editing_data/html5/default.php" target="demoFrame">HTML 5 Form Check  <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="editing_data/custom_button/default.php" target="demoFrame">Custom Button in form  <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="editing_data/edit_template/default.php" target="demoFrame">Form Template </a>
                                    </li>
                                    <li>
                                        <a href="editing_data/cache_dataurl/default.php" target="demoFrame">Cache dataUrl </a>
                                    </li>
                                    <li>
                                        <a href="editing_data/timepicker/default.php" target="demoFrame">Integrate Time Picker </a>
                                    </li>
                                    <li>
                                        <a href="editing_data/editor/default.php" target="demoFrame">Integrate HTML Editor </a>
                                    </li>
                                    <li>
                                        <a href="editing_data/inlinenav/default.php" target="demoFrame">Inline navigator</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/inlinenavact/default.php" target="demoFrame">Inline navigator with options </a>
                                    </li>
                                    <li>
                                        <a href="editing_data/customerror/default.php" target="demoFrame">Custom error messaging system </a>
                                    </li>
                                    <li>
                                        <a href="editing_data/showerrors/default.php" target="demoFrame">Show the errors from the server</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/after_edit/default.php" target="demoFrame">Run commands after edit</a>
                                    </li>

                                    <li>
                                        <a href="editing_data/edit_dialog/default.php" target="demoFrame">Edit Dialog</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/inlinecustom/default.php" target="demoFrame">Inline editing - autocomplete, datepicker</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/edit_dialog_custom/default.php" target="demoFrame">Edit Dialog Custom Layout</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/inline/default.php" target="demoFrame">Inline cell editing</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/depend_listbox/default.php" target="demoFrame">Dependend Dropdowns in edit dialog</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/depend2/default.php" target="demoFrame">Ajax Dependend Dropdowns in edit dialog</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/add_new_row/default.php" target="demoFrame">Add New Row Dialog</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/delete/default.php" target="demoFrame">Delete Row Dialog</a>
                                    </li>	                                                	                                                
                                    <li>
                                        <a href="editing_data/datepicker/default.php" target="demoFrame">Datepicker Integration [inline]</a>
                                    </li>
                                    <li>
                                        <a href="editing_data/datepicker_editdialog/default.php" target="demoFrame">Datepicker Integration [edit dialog]</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/edit_add_delete/default.php" target="demoFrame">Edit,Add,Delete in the same grid</a>
                                    </li>	                                                 
                                    <li>
                                        <a href="editing_data/edit_add_select/default.php" target="demoFrame">Edit,Add and grid in modal</a>
                                    </li>	                                                 
                                    <li>
                                        <a href="editing_data/edit_types/default.php" target="demoFrame">Edit Form Types (dropdown, textbox, etc)</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/edit_calculations/default.php" target="demoFrame">Automatic calculations</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/add_edit_comma/default.php" target="demoFrame">Edit Numbers with commas</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/add_edit_refresh/default.php" target="demoFrame">Refresh grid id's without reloads at server</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/add_edit_view/default.php" target="demoFrame">View Record in Edit mode</a>
                                    </li>	                                                
                                    <li>
                                        <a href="editing_data/beforeshow/default.php" target="demoFrame">Determine CRUD at client side</a>
                                    </li>	                                                
                                </ul>
                            </div>
                            <h3><a href="#">Grouping </a></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="grouping/newavg/default.php" target="demoFrame">Complex calculations <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="grouping/grphead1/default.php" target="demoFrame">Header Grouping Method call</a>
                                    </li>
                                    <li>
                                        <a href="grouping/grphead2/default.php" target="demoFrame">Header Grroping JavaScript</a>
                                    </li>
                                    <li>
                                        <a href="grouping/basic/default.php" target="demoFrame">Basic Grouping</a>
                                    </li>
                                    <li>
                                        <a href="grouping/summaryfooter/default.php" target="demoFrame">Grouping with Summary Footers</a>
                                    </li>
                                    <li>
                                        <a href="grouping/twogrp/default.php" target="demoFrame">Grouping on multiple columns</a>
                                    </li>
                                    <li>
                                        <a href="grouping/twogrpsum/default.php" target="demoFrame">Multiple grouping with one level summary</a>
                                    </li>
                                    <li>
                                        <a href="grouping/twogrpsumhead/default.php" target="demoFrame">Multiple grouping with header summary</a>
                                    </li>
                                    <li>
                                        <a href="grouping/grandtotal/default.php" target="demoFrame">Grouping with Summaries and GrandTotal</a>
                                    </li>
                                    <li>
                                        <a href="grouping/dynamic/default.php" target="demoFrame">Programatic Group Switching</a>
                                    </li>
                                    <li>
                                        <a href="grouping/wavg/default.php" target="demoFrame">Different Summary Types</a>
                                    </li>
                                </ul>
                            </div>
                            <h3><a href="#">Hierarchy <sup><font style="color:red">New</font></sup></a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="hierarchy/dynamic_subgrid/default.php" target="demoFrame">Dynamic disable subgrid <sup><font style="color:red">New</font></sup> </a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_icons/default.php" target="demoFrame">Custom subgrid icons </a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_expandall/default.php" target="demoFrame">Expand all rows on load </a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_params/default.php" target="demoFrame">Passing parameters</a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_loadonce/default.php" target="demoFrame">Load subgrid data once </a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_2_levels/default.php" target="demoFrame">Sub Grid (2 nested levels)</a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_3_levels/default.php" target="demoFrame">Sub Grid (3 nested levels)</a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_2_level_editing/default.php" target="demoFrame">Edit Subgrid </a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/subgrid_2_level_inlinedit/default.php" target="demoFrame">Inline edit with subgrid </a>
                                    </li>
                                    <li>
                                        <a href="hierarchy/custom_details/default.php" target="demoFrame">Custom row details</a>
                                    </li>
                                     <li>
                                        <a href="hierarchy/simple_subgrid/default.php" target="demoFrame">Simple subgrid</a>
                                    </li>
                                </ul>
                            </div>	                                        
                            <h3><a href="#">Selection</a></h3>
                            <div>
                                 <ul class="examples">
                                    <li>
                                        <a href="selection/masterdetail/default.php" target="demoFrame">Master Detail On Select</a>
                                    </li>
                                    <li>
                                        <a href="selection/masterdetail2/default.php" target="demoFrame">Master Detail With editing</a>
                                    </li>
                                    <li>
                                        <a href="selection/showhidecols/default.php" target="demoFrame">Show/hide Columns</a>
                                    </li>
                                    <li>
                                        <a href="selection/selectedrow_client/default.php" target="demoFrame">Get/Set Selected Row (on client)</a>
                                    </li>
                                    <li>
                                        <a href="selection/multiselect/default.php" target="demoFrame">Multiple Rows Selection</a>
                                    </li>
                                </ul> 
                            </div>
                            <h3><a href="#">Paging</a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="paging/scrollbar/default.php" target="demoFrame">Paging with scrollbar</a>
                                    </li>
                                    <li>
                                        <a href="paging/customui/default.php" target="demoFrame">Custom paging UI</a>
                                    </li>
                                </ul>                                     
                            </div>
                            <h3><a href="#">Appearance</a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="appearance/toolbar/default.php" target="demoFrame">Clone the pager and buttons</a>
                                    </li>
                                    <li>
                                        <a href="appearance/alternate_row_background/default.php" target="demoFrame">Alternate row background</a>
                                    </li>
                                    <li>
                                        <a href="appearance/caption/default.php" target="demoFrame">Customize grid caption</a>
                                    </li>
                                    <li>
                                        <a href="appearance/highlight_on_hover/default.php" target="demoFrame">Highlight row on hover</a>
                                    </li>
                                    <li>
                                        <a href="appearance/rownumbers/default.php" target="demoFrame">Show row numbers</a>
                                    </li>
                                </ul>
                            </div>

                            <h3><a href="#">Export <sup style="color:red">New</sup></a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="export/true_excel7/default.php" target="demoFrame">Export with true Excel 2007 file <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="export/true_excel5/default.php" target="demoFrame">Export with true Excel5 file <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="export/csv/default.php" target="demoFrame">CSV Export</a>
                                    </li>
                                    <li>
                                        <a href="export/csvcomma/default.php" target="demoFrame">CSV Export with options</a>
                                    </li>
                                    <li>
                                        <a href="export/csvsummary/default.php" target="demoFrame">CSV Export with build in Summary</a>
                                    </li>
                                    <li>
                                        <a href="export/excel/default.php" target="demoFrame">Excel Export</a>
                                    </li>
                                    <li>
                                        <a href="export/excelcustom/default.php" target="demoFrame">Export to Excel with custom command</a>
                                    </li>
                                    <li>
                                        <a href="export/excelsummary/default.php" target="demoFrame">Export to Excel with build in Summary</a>
                                    </li>
                                    <li>
                                        <a href="export/pdf/default.php" target="demoFrame">Export to PDF</a>
                                    </li>
                                    <li>
                                        <a href="export/pdfheader/default.php" target="demoFrame">Export to PDF with header</a>
                                    </li>
                                    <li>
                                        <a href="export/pdfcustom/default.php" target="demoFrame">Custom Export to PDF</a>
                                    </li>
                                    <li>
                                        <a href="export/pdfland/default.php" target="demoFrame">Export to PDF - Landscape</a>
                                    </li>
                                    <li>
                                        <a href="export/pdfsummary/default.php" target="demoFrame">Export to PDF with build in Summary</a>
                                    </li>

                                </ul>
                            </div>
                            <h3><a href="#">Integrations<sup><font style="color:red">New</font></sup></a></h3>
                            <div>
                                <ul class="examples">
                                    <li>
                                        <a href="integrations/bootstrap/default.php" target="demoFrame">Bootstrap  Integartion <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="integrations/jqform/default.php" target="demoFrame">jqForm  Integartion <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="integrations/fencybox/default.php" target="demoFrame">Fencybox  Integartion <sup><font style="color:red">New</font></sup></a>
                                    </li>
                                    <li>
                                        <a href="integrations/dragdrop/default.php" target="demoFrame">Drag Drop between two grids </a>
                                    </li>
                                    <li>
                                        <a href="integrations/adodb/default.php" target="demoFrame">AdoDB integrations </a>
                                    </li>
                                    <li>
                                        <a href="integrations/autocomplete/default.php" target="demoFrame">Simple autocomplete </a>
                                    </li>
                                    <li>
                                        <a href="integrations/autocomplete_search/default.php" target="demoFrame">Autocomplete in search dialog</a>
                                    </li>
                                    <li>
                                        <a href="integrations/autocomplete_toolbar/default.php" target="demoFrame">Autocomplete in toolbar search</a>
                                    </li>
                                    <li>
                                        <a href="integrations/autocompl_toolbar2/default.php" target="demoFrame">Autocomplete in toolbar with enter</a>
                                    </li>
                                    <li>
                                        <a href="integrations/auto_editing_search/default.php" target="demoFrame">Autocomplete in edit and search dialogs</a>
                                    </li>
                                    <li>
                                        <a href="integrations/autocompl_sethidden/default.php" target="demoFrame">Advanced Autocomplete</a>
                                    </li>
                                    <li>
                                        <a href="integrations/datepicker/default.php" target="demoFrame">Datepicker component</a>
                                    </li>
                                    <li>
                                        <a href="integrations/autocompl_datepicker_full/default.php" target="demoFrame">Autocomplete and Datepicker on the same grid</a>
                                    </li>
                                </ul>
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