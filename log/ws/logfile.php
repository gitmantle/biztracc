<?php
class logfile{
	function write($the_string ) {
		if( $fh = @fopen( "uploaded_log.txt", "a+" ) ) {
			fputs( $fh, $the_string, strlen($the_string) );
			fclose( $fh );
			return( true );
		} else {
			return( false );
		}
	}
}
?>