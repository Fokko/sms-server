<?php
ini_set( 'memory_limit', '512M' );

/* Define predefined variables */
// Set locale information
setlocale( LC_ALL, 'nl_NL' );
// Set default date timezone
date_default_timezone_set( 'Europe/Amsterdam' );

/* Define system variables */
// Enable or disable debug mode
define( 'DEBUGMODUS', false );
// System name
define( 'SYSTEM_NAME', 'Feenstra SMSService' );
// Relative document root path
define( 'DOCUMENT_ROOT', realpath( dirname( __FILE__ ) ) . '/' );
// Relative full web address
define( 'HTTP_ROOT', str_replace( $_SERVER[ 'DOCUMENT_ROOT' ], '', substr( $_SERVER[ 'SCRIPT_FILENAME' ], 0, strrpos( $_SERVER[ 'SCRIPT_FILENAME' ], '/' ) + 1 ) ) );
// System root
define( 'SYSTEM_ROOT', __DIR__ );
// Absolute full web address
define( 'COMPLETE_HTTP_PATH', 'http://' . $_SERVER[ 'HTTP_HOST' ] . HTTP_ROOT );
// E-mail dir containing e-mail templates
define( 'SYSTEM_MAIL_DIR', DOCUMENT_ROOT . 'manager/mails/' );
// Default e-mail address used by mailing
define( 'SYSTEM_MAIL', 'fokko@driesprongen.nl' );

/* Database configuration */
// Hostname
define( 'DB_HOSTNAME', 'localhost' );
// Username
define( 'DB_USERNAME', 'root' );
// Password
define( 'DB_PASSWORD', '' );
// Database
define( 'DB_DATABASE', 'smsserver' );

/* User configuration */
// Login expire 
// 31536000 seconds is one year
define( 'LOGIN_EXPIRE', 31536000 );
// Default language id (dutch)
define( 'DEFAULT_LANGUAGE_ID', 1 );
// Enable or disable multilanguage
define( 'USE_MULTILANGUAGE', TRUE );

require_once( 'debug/debug.php' );
