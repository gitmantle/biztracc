<?php
require_once '../lang/info_en_GB.inc';
?>

<form id="input_text">
	<table style="table-layout: auto;width:600px;">
		<tbody>
		<tr>
			<td style="height:0px;width:35%"></td>
			<td style="height:0px"></td>
			<td style="height:0px"></td>
		</tr>
		<tr>
			<td colspan="3" style="">
				<div class="ui-state-default ui-corner-all" style="padding:6px;">
					<span class="ui-icon ui-icon-triangle-1-s" id="showother" style="float:left; margin:-2px 5px 0 0;cursor:pointer;" title="Hide Form Properties"></span>
					Input Text  Properties
				</div>
			</td>
		</tr>
		<tr class="otherparam">
			<td style="width:35%;">
				<label for="html"> HTML Tags</label>
			</td>
			<td>
				<textarea id="html" name="html" style="width:98%;font-size: 14px" rows="4" cols="80" class="ui-widget-content ui-corner-all input-ui"></textarea>
			</td>
			<td class="help" title="<?php echo $html?>"><span class='ui-icon ui-icon-info'></span></td>
		</tr>
		</tbody>
	</table>
</form>