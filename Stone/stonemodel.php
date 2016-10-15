<?php
	/**
	 * Abstract StoneModel class
	 */
	abstract class StoneModel
	{
		private static $instance = array ( );
		protected $mysql;

		/**
		 * Constructor
		 */
		public function __construct ( )
		{
			$this->mysql = Mysql::init ( );
		}

		/**
		 * Initializer
		 * 
		 * Saving all called classes in static instance variable
		 */
		public static function init ( )
		{
			$className = get_called_class ( );

			if ( isset ( self::$instance[ $className ] ) === FALSE )
			{
				self::$instance[ $className ] = new $className;
			}

			return self::$instance[ $className ];
		}

	}
?>