<?php
	/**
	 * Custom exception 
	 * 
	 * abstract class that ensures that all parts of the built-in Exception class are preserved in child classes
	 */
	abstract class CustomException extends Exception implements IException
	{
		// Exception message
		protected $message = 'Custom exception';
		// Unknown
		private $string;
		// User-defined exception code
		protected $code = 0;
		// Source filename of exception
		protected $file;
		// Source line of exception
		protected $line;
		// Unknown
		private $trace;

		/**
		 * Construct
		 */
		public function __construct ( $message = NULL, $code = 0 )
		{
			if ( $message == NULL )
			{
				throw new $this ( 'Unknown ' . get_class ( $this ) );
			}

			parent::__construct ( $message, $code );
		}

		/**
		 * Override toString
		 */
		public function __toString ( )
		{
			$this->message = preg_replace ( '/\s+/', ' ', $this->message );
			return get_class ( $this ) . ' "' . $this->message . '" in ' . $this->file . '(' . $this->line . ')<br />' . $this->getTraceAsString ( );
		}

	}
?>