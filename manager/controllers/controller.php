<?php

class Controller
{
	private static $instance;
	public $view, $model;

    // The singleton method
    public static function init()
    {
		if( isset( self::$instance ) === FALSE )
		{
            $c = get_called_class();
            self::$instance = new $c;
        }

        return self::$instance;
    }

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
}
?>