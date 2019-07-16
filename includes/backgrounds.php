<?php
error_reporting (E_ERROR | E_WARNING | E_PARSE);
session_start();
$theme = $_SESSION['deftheme'];

switch ($theme) {
	case 'black-tie':
		$bgcolor = "#fcfcfc";
		$bghead = "#4e4e4e";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'redmond':
		$bgcolor = "#fdfefe";
		$bghead = "#87b6d9";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'blitzer':
		$bgcolor = "#fdfefe";
		$bghead = "#ce0b0b";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'vader':
		$bgcolor = "#262626";
		$bghead = "#8a8a8a";
		$thfont = "#000";
		$tdfont = "#fff";
		break;
	case 'cupertino':
		$bgcolor = "#fdfefe";
		$bghead = "#e6f1f9";
		$thfont = "#000";
		$tdfont = "#000";
		break;
	case 'sunny':
		$bgcolor = "#feeebd";
		$bghead = "#fdd44b";
		$thfont = "#000";
		$tdfont = "#000";
		break;
	case 'dark-hive':
		$bgcolor = "#333";
		$bghead = "#666666";
		$thfont = "#fff";
		$tdfont = "#fff";
		break;
	case 'ui-lightness':
		$bgcolor = "#f8f8f8";
		$bghead = "#f7b64b";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'dot-luv':
		$bgcolor = "#2a2a2a";
		$bghead = "#0b3e6f";
		$thfont = "#fff";
		$tdfont = "#fff";
		break;
	case 'swanky-purse':
		$bgcolor = "#4c3a1d";
		$bghead = "#2f220e";
		$thfont = "#efec9f";
		$tdfont = "#efec9f";
		break;
	case 'humanity':
		$bgcolor = "#faf8f7";
		$bghead = "#cd8937";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'trontastic':
		$bgcolor = "#4c4c4c";
		$bghead = "#b2e179";
		$thfont = "#000";
		$tdfont = "#fff";
		break;
	case 'eggplant':
		$bgcolor = "#5b5561";
		$bghead = "#393042";
		$thfont = "#fff";
		$tdfont = "#fff";
		break;
	case 'ui-darkness':
		$bgcolor = "#262626";
		$bghead = "#3f3f3f";
		$thfont = "#fff";
		$tdfont = "#fff";
		break;
	case 'le-frog':
		$bgcolor = "#35650f";
		$bghead = "#468813";
		$thfont = "#fff";
		$tdfont = "#fff";
		break;
	case 'smoothness':
		$bgcolor = "#ffffff";
		$bghead = "#d1d1d1";
		$thfont = "#000";
		$tdfont = "#000";
		break;
	case 'excite-bike':
		$bgcolor = "#f8f8f8";
		$bghead = "#1484e6";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'flick':
		$bgcolor = "#ffffff";
		$bghead = "#dedede";
		$thfont = "#000";
		$tdfont = "#fff";
		break;
	case 'hot-sneaks':
		$bgcolor = "#ffffff";
		$bghead = "#35414f";
		$thfont = "#e4e664";
		$tdfont = "#000";
		break;
	case 'start':
		$bgcolor = "#fdfefe";
		$bghead = "#44a2c9";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
	case 'mint-choc':
		$bgcolor = "#2d2721";
		$bghead = "#4f3e32";
		$thfont = "#fff";
		$tdfont = "#fff";
		break;
	case 'overcast':
		$bgcolor = "#d9d9d9";
		$bghead = "#e0e0e0";
		$thfont = "#000";
		$tdfont = "#000";
		break;
	case 'pepper-grinder':
		$bgcolor = "#e6e4d9";
		$bghead = "#f4f4f4";
		$thfont = "#000";
		$tdfont = "#000";
		break;
	case 'south-street':
		$bgcolor = "#fafaf4";
		$bghead = "#49a006";
		$thfont = "#fff";
		$tdfont = "#000";
		break;
		
}


?>