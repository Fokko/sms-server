<?php

	/**
	 * Manager exception
	 */
	class ManagerException extends CustomException
	{
		/**
		 * Constructor
		 */
		public function __construct ( $message = NULL, $code = 0 )
		{
			parent::__construct ( $message, $code );
		}

		/**
		 * Output exceptions
		 */
		public static function printException ( $e = NULL, $code = 0 )
		{
			echo '<pre>Caught exception ("' . $e->getMessage ( ) . '")' . "\n" . $e . '</pre>';
		}

		/**
		 * Handle exceptions if debugmodus is enabled
		 */
		public static function handleException ( $e = NULL, $code = 0 )
		{
			if ( DEBUGMODUS === TRUE )
			{
				self::printException ( $e, $code );
			}
		}

	}
