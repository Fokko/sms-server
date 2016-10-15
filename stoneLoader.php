<?php

/**
 * Stone loader
 * 
 * Loads stones automatically if called
 */
class StoneLoader
{
	private static $instance;
	private $modules = array( 'library', 'StonePhonebook', 'StoneMessage', 'StoneUser', 'Stone', 'StoneSetting' );

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
		spl_autoload_register( array( $this, 'loadModules' ) );
	}

	/**
	 * Load modules
	 * 
	 * Load classes in 'stone' module
	 */
	private function loadModules( $className )
	{
		foreach( $this->modules as $module )
		{
			set_include_path( DOCUMENT_ROOT . $module . '/' );
			spl_autoload_extensions( '.php' );
			spl_autoload( $className );
		}
	}
}
?>