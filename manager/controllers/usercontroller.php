<?php
class UserController extends Controller
{
	public $controller, $model;
	public static $userRoles;

	public function __construct()
	{
		parent::__construct();

		$this->controller	= StoneController::getClass( 'StoneUser' );
		$this->model		= $this->controller->getModel();

		self::$userRoles	= array(
			1 =>	'administrator',
				'busdriver',
				'operator',
				'client'
		);
	}

	public function register()
	{
		if( isset( $_POST ) && count( $_POST ) > 0 )
		{
			
			$_POST[ 'properties' ][ 'parent_id' ] 	= 2;
			$_POST[ 'properties' ][ 'role' ] 		= 'client';
			
			$this->controller->saveUser( $_POST[ 'properties' ] );
			
			return $this->view->loginForm( false, true );
		}
	}

	public function showTable()
	{
		return $this->view->showUsersTable( $this->getCurrentUserHierarchy() );
	}

	public function requestpassword()
	{
		if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
		{
			$mailSend = $this->controller->requestNewPassword( $_POST[ 'email' ] );
		}
		else
		{
			unset( $_SESSION[ 'requestedPassword' ] );
			$mailSend = NULL;
		}

		return $this->view->requestPasswordForm( $mailSend );
	}

	public function view()
	{
		return $this->view->showUsers( $this->getCurrentUserHierarchy() );
	}

	public function deleteUser()
	{
		return $this->model->deleteUser( IsVar::set( $_GET, 'userId', 0 ) );
	}	

	private function getCurrentUserHierarchy()
	{
		$userId						= StoneUserController::getCurrentUserId();
		$userHierarchy[ $userId ]			= $this->model->getUserById( $userId );
		$userHierarchy[ $userId ][ 'subusers' ]	= $this->model->getUsersByIds( $this->model->getSubUsersIds( $userId, 99 ) );

		return $userHierarchy;
	}

	public function save()
	{
		$_POST[ 'id' ] = IsVar::set( $_GET, 'userId', 0 );
		$userId = $this->controller->saveUser( $_POST );
	}

	public function add()
	{
		$userId	= StoneUserController::getCurrentUserId();

		if( StoneUserController::getUserRole() == 'administrator' )
		{
			$users = $this->model->getUsers();
		}
		else
		{
			$users = array();
		}

		return $this->view->showUserForm( array(), $this->controller->getUser(), $users );
	}

	public function edit()
	{
		$userId	= IsVar::set( $_GET, 'userId', 0 );
		$user	= $this->model->getUserById( $userId );

		if( count( $user ) > 0 )
		{
			$parentUser = $user[ 'parent_id' ];
		}
		else
		{
			$parentUser = array();
		}

		if( StoneUserController::getUserRole() == 'administrator' )
		{
			$users = $this->model->getUsers();
		}
		else
		{
			$users = array();
		}

		return $this->view->showUserForm( $user, $parentUser, $users );
	}

	public function login()
	{
		$loginAttempt = FALSE;

		if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
		{
			if( $this->loginAttempt() === TRUE )
			{
				if( StoneUserController::getUserRole() == 'administrator' )
				{
					header( 'Location: ' . HTTP_ROOT );
				}
				elseif( StoneUserController::getUserRole() == 'operator' )
				{
					header( 'Location: ' . HTTP_ROOT . 'index.php?module=ride' );
				}
				elseif( StoneUserController::getUserRole() == 'busdriver' )
				{
					header( 'Location: ' . HTTP_ROOT . 'index.php?module=address&action=demo' );
				}
				else
				{					
					header( 'Location: ' . HTTP_ROOT . 'index.php?module=ride&action=view' );
				}
			}

			$loginAttempt = TRUE;
		}

		return $this->view->loginForm( $loginAttempt );
	}

	public function logout()
	{
		$this->controller->logout();
		header( 'Location: index.php' );
	}

	public function loginAttempt()
	{
		if( filter_input_array(
			INPUT_POST,
			array(
				'user'		=> FILTER_SANITIZE_STRING,
				'password'	=> FILTER_SANITIZE_STRING
			)
		) )
		{
			if( $this->controller->loginAttempt( $_POST[ 'user' ], $_POST[ 'password' ] ) )
			{
				return true;
			}
		}

		return false;
	}
}
