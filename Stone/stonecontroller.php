<?php
abstract class StoneController
{
	private static $instance = array();
	protected $model;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$cleanClassName = str_replace( 'Controller', '', get_called_class() );
		if(	method_exists( $cleanClassName . 'Model', 'init' ) )
		{
			$this->model = call_user_func( $cleanClassName . 'Model::init' );
		}

		if(	method_exists( $cleanClassName . 'View', 'init' ) )
		{
			$this->view = call_user_func( $cleanClassName . 'View::init' );
		}
	}

	/**
	 * Initializer
	 */
	public static function init( $className = '' )
	{
		if( $className == '' )
		{
			$className = get_called_class();
		}
		else
		{
			$className .= 'Controller';
		}
		
		if( array_key_exists( $className, self::$instance ) === FALSE )
		{
			self::$instance[ $className ] = new $className;
		}
		
		return self::$instance[ $className ];
	}

	/**
	 * Call function
	 * 
	 * Is triggered when invoking inaccessible methods
	 */
	public function __call( $name, $args )
	{
		throw new ManagerException( 'Undefined ' . __CLASS__ . ' method "' . $name . '()" called' );
		return $this->view->pageNotFound();
	}

	/**
	 * Get class
	 * 
	 * Calling class
	 */
	public static function getClass( $class )
	{
		return call_user_func( $class . 'Controller::init', $class );
	}
}
?>