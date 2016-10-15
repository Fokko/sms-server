<?php

/**
 * Manager loader
 * 
 * Loads MVC inside manager automatically if called
 */
class ManagerLoader
{
	private static $instance;
	private $path;
	private $modules = array( 'views', 'controllers', 'models' );

	/**
	 * Singleton initializer
	 */
	public static function init()
	{
		if( isset( self::$instance ) === FALSE )
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->path = DOCUMENT_ROOT . 'manager/';
		spl_autoload_register( array( $this, 'loadModules' ) );
	}

	/**
	 * Load modules
	 * 
	 * Load classes in MVC structure
	 */
	private function loadModules( $className )
	{
		foreach( $this->modules as $module )
		{
			set_include_path( $this->path . $module . '/' );
			spl_autoload_extensions( '.php' );
			spl_autoload( $className );
		}
	}
}
?>