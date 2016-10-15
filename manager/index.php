<?php
	ob_start();
	session_start();

	// Require needed files
	require_once( '../config.php' );
	// Require loaders to automatically load classes
	require_once( 'managerLoader.php' );
	require_once( '../stoneLoader.php' );

	require_once( '../library/exceptioninterface.php' );
	require_once( '../library/customexception.php' );
	require_once( '../library/autoshutdown.php' );
	require_once( '../library/timer.php' );

	require_once( '../library/errorhandler.php' );
	require_once( '../library/managerexception.php' );

	class Index
	{
		// Default module and action
		private $defaultModule = 'dashboard';
		private $defaultAction = 'home';

		private $timer;

		/**
		 * Constructor
		 */
		public function __construct( $message = null, $code = 0 )
		{
			// Initialize loaders
			ManagerLoader::init();
			StoneLoader::init();

			// Start timer
			$this->timer = new Timer();
			$this->timer->start();
			
			// Initialize shutdown triggers
			Autoshutdown::init();
			Autoshutdown::registerShutdownEvent( 'errorHandler::handleShutdown' );

			if( $this->handleSecurity() === FALSE )
			{
				$this->startControllerAction();
			}
		}

		/**
		 * Handle security
		 */
		private function handleSecurity()
		{
			// Allowed user actions if not logged in
			$allowUserActions = array( 'logout', 'requestpassword', 'register' );
			if( isset( $_GET[ 'module' ] ) AND isset( $_GET[ 'action' ] ) AND $_GET[ 'module' ] == 'user' AND in_array( $_GET[ 'action' ], $allowUserActions ) )
			{
				$controller = StoneUserController::getClass( 'User' );
				if( method_exists( $controller, $_GET[ 'action' ] ) === FALSE )
				{
					// Fetch content from login action
					$content = $controller->login();
				}
				else
				{
					// Fetch content dynamically
					$content = $controller->{ $_GET[ 'action' ] }();

					if( $_GET[ 'action' ] == 'logout' )
					{
						return TRUE;
					}
				}

				$this->showPage( $content );
			}
			elseif( StoneUserController::isLoggedIn() === FALSE )
			{
				// Get user controller and use login action
				$controller = StoneUserController::getClass( 'User' );
				$content = $controller->login();

				$this->showPage( $content );
			}
			else
			{
				return FALSE;
			}

			return TRUE;
		}

		private function startControllerAction()
		{
			// Validate module and action
			if( filter_input_array(
				INPUT_GET,
				array(
					'module'	=> FILTER_SANITIZE_STRING,
					'action'	=> FILTER_SANITIZE_STRING
				)
			) )
			{
				// Get default method if method doesn't exists
				if( method_exists( $_GET[ 'module' ] . 'Controller', 'init' ) === FALSE )
				{
					$_GET[ 'module' ] = $this->defaultModule;
				}

				$controller = call_user_func( $_GET[ 'module' ] . 'Controller::init' );
			}

			if( isset( $controller ) === FALSE OR method_exists( $controller, $_GET[ 'action' ] ) === FALSE )
			{
				$controller = call_user_func( $this->defaultModule . 'Controller::init' );
				$_GET[ 'action' ] = $this->defaultAction;
			}

			// Return called module and action
			return $this->showPage( $controller->{ $_GET[ 'action' ] }() );
		}

		/**
		 * Destructor
		 */
		public function __destruct()
		{
			ob_end_flush();
		}

		/**
		 * Show page
		 * 
		 * Use translationsmodule to translate content if needed and output content. Output Executed MySQL queries if debugmodus is enabled.
		 */
		private function showPage( $content )
		{
			$translations = new TranslationController();
			if( isset( $_GET[ 'ajax' ] ) )
			{
				echo $translations->doTranslate( $content );
			}
			elseif( StoneUserController::isLoggedIn() === FALSE )
			{
				echo $translations->doTranslate( UserView::showCustomHTML( $content ) );

				// Output Executed MySQL queries if debugmodus is enabled
				if( DEBUGMODUS === TRUE )
				{
					echo Mysql::printExecutedQueries();
				}
			}
			else
			{
				// Use timer to calculate page load
				$this->timer->stop();
				echo $translations->doTranslate( View::showHtml( $content, number_format( $this->timer->get( Timer::$SECONDS ), 6 ), StoneUserController::getLastLogin() ) );

				// Output Executed MySQL queries if debugmodus is enabled
				if( DEBUGMODUS === TRUE )
				{
					echo Mysql::printExecutedQueries();
				}
			}
		}
	}

	set_exception_handler( array( 'ManagerException', 'handleException' ) );
	set_error_handler( array( 'ErrorHandler', 'handleError' ) );

	new Index();

?>