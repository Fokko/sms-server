<?php
// Debug settings
ini_set( 'error_log', DOCUMENT_ROOT . 'dashboard4/debug/errors.log' );

if( DEBUGMODUS === TRUE )
{
	ini_set( 'log_errors', FALSE );

	ini_set( 'error_reporting', E_ALL ); // E_ALL
	// E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_STRICT

	ini_set( 'display_errors', TRUE );
	ini_set( 'track_errors', TRUE );
	ini_set( 'html_errors', TRUE );
	ini_set( 'display_errors', TRUE );
}
else
{
	ini_set( 'log_errors', TRUE );

	ini_set( 'error_reporting', E_ERROR );

	ini_set( 'display_errors', FALSE );
	ini_set( 'track_errors', FALSE );
	ini_set( 'html_errors', FALSE );
	ini_set( 'display_errors', FALSE );
}
