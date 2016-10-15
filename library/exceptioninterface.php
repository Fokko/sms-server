<?php
	/**
	 * Exception interface
	 * 
	 * interface class that ensures that all parts of the built-in Exception class are preserved in child classes
	 * It also properly pushes all information back to the parent constructor ensuring that nothing is lost. This allows you to quickly create new exceptions on the fly. It also overrides the default __toString method with a more thorough one.
	 * 
	 * @source: http://php.net/manual/en/language.exceptions.php
	 */
	interface IException
	{
		/* Protected methods inherited from Exception class */
		// Exception message
		public function getMessage ( );
		// User-defined Exception code
		public function getCode ( );
		// Source filename
		public function getFile ( );
		// Source line				
		public function getLine ( );
		// An array of the backtrace()
		public function getTrace ( );
		// Formated string of trace
		public function getTraceAsString ( );

		/* Overrideable methods inherited from Exception class */
		// formated string for display
		public function __toString ( );
		public function __construct ( $message = NULL, $code = 0 );
	}
?>