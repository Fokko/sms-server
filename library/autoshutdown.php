<?php
	/**
	 * Auto shutdown
	 */
	class Autoshutdown
	{
		private static $callbacks;
		private static $instance;

		/**
		 * Singleton initialize
		 */
		public static function init ( )
		{
			if ( isset ( self::$instance ) === FALSE )
			{
				$c = __CLASS__;
				self::$instance = new $c;
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct ( )
		{
			self::$callbacks = array ( );
			register_shutdown_function ( 
				array (
					$this,
					'callRegisteredShutdown'
				)
			);
		}

		/**
		 * Register shutdown event
		 */
		public static function registerShutdownEvent ( )
		{
			$callback = func_get_args ( );

			if ( empty ( $callback ) )
			{
				trigger_error ( 'No callback passed to ' . __FUNCTION__ . ' method', E_USER_ERROR );
				return false;
			}

			if ( is_callable ( $callback[ 0 ] ) === FALSE )
			{
				trigger_error ( 'Invalid callback passed to the ' . __FUNCTION__ . ' method', E_USER_ERROR );
				return false;
			}

			self::$callbacks[ ] = $callback;
			return true;
		}

		/**
		 * Call registered shutdown event
		 */
		public function callRegisteredShutdown ( )
		{
			foreach ( self::$callbacks as $key => &$arguments )
			{
				$callback = array_shift ( $arguments );
				call_user_func_array ( $callback, $arguments );
			}
		}

	}
?>