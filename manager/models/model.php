<?php

abstract class Model
{
	private static $instance;
	protected $mysql;

	public function __construct()
	{
		$this->mysql = Mysql::init();
	}

    // The singleton method
    public static function init()
    {
        if( !isset( self::$instance ) ) {
			$c = get_called_class();

			if( $c == 'Model' )
				return;

            self::$instance = new $c;
        }

        return self::$instance;
    }

	public function __call( $name, $args )
    {
        throw new ManagerException( 'Undefined ' . __CLASS__ . ' method "' . $name . '()" called' );
    }
}

?>