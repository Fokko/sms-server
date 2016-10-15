<?php
	/**
	 * Error handler
	 */
	class ErrorHandler
	{
		/**
		 * Handle error
		 */
		public static function handleError ( $errno, $errstr, $errfile, $errline )
		{
			switch( $errno )
			{
				case E_NOTICE :
				case E_USER_NOTICE :
					$errors = "Notice";
					break;
				case E_WARNING :
				case E_USER_WARNING :
					$errors = "Warning";
					break;
				case E_ERROR :
				case E_USER_ERROR :
					$errors = "Fatal Error";
					break;
				default :
					$errors = "Unknown";
					break;
			}

			if ( error_reporting ( ) > $errno )
			{
				if ( isset ( $_GET[ 'ajax' ] ) )
				{
					printf ( "\n%s: %s in %s on line %d\n", $errors, $errstr, $errfile, $errline );
					$backtrace = debug_backtrace ( );
					array_pop ( $backtrace );
					array_shift ( $backtrace );
					foreach ( $backtrace as $row )
					{
						printf ( 'class "%s", function "%s" in %s on line %d' . "\n", IsVar::set ( $row, 'class', 'unknown' ), IsVar::set ( $row, 'function', 'unknown' ), '"' . str_replace ( DOCUMENT_ROOT, '', IsVar::set ( $row, 'file' ) ) . '"', IsVar::set ( $row, 'line' ) );
					}
				}
				else
				{
					printf ( '<table class="debug"><tr class="error"><td colspan="4"><strong>%s</strong>: %s in <b>%s</b> on line <strong>%d</strong></td></tr>', $errors, $errstr, $errfile, $errline );

					$backtrace = debug_backtrace ( );
					array_pop ( $backtrace );
					array_shift ( $backtrace );
					foreach ( $backtrace as $row )
					{
						printf ( '<tr><td>class "<strong>%s</strong>"</td><td>function "<strong>%s</strong>"</td><td>in %s</td><td>on line <strong>%d</strong></td>', IsVar::set ( $row, 'class', 'unknown' ), IsVar::set ( $row, 'function', 'unknown' ), '"<strong>' . str_replace ( DOCUMENT_ROOT, '', IsVar::set ( $row, 'file' ) ) . '</strong>"', IsVar::set ( $row, 'line' ) );
					}
					print '</table>';
				}
			}
			else
			{
				error_log ( sprintf ( 'PHP %s: %s in %s on line %d', $errors, $errstr, $errfile, $errline ) );
			}

			return true;
		}

		/**
		 * Handle shutdown
		 * 
		 * TODO: handle shutdown (close database, show error logs, etc.)
		 */
		public static function handleShutdown ( )
		{
			if ( false === is_null ( $error = error_get_last ( ) ) )
			{
				switch( $error )
				{
					default :
						break;

					case E_ERROR :
						break;

					case E_PARSE :
						break;

					case E_CORE_ERROR :
						break;

					case E_CORE_WARNING :
						break;

					case E_COMPILE_ERROR :
						break;

					case E_COMPILE_WARNING :
						break;

					case E_STRICT :
						break;
				}
			}
		}

	}
