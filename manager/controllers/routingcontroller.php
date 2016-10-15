<?php
	class RoutingController extends Controller
	{
		private $stoneController;

		public function __construct ( )
		{
			parent::__construct ( );
			$this->stoneController = StoneController::getClass ( 'StoneMessage' );
		}

		public function home ( )
		{
			$stonePhonebook = StoneController::getClass ( 'StonePhonebook' );
			$locations = $stonePhonebook->getAllLocations ( );

			if ( isset ( $_GET[ 'deleteRoutingRecord' ] ) )
			{
				$this->stoneController->deleteRouting ( (int)$_GET[ 'deleteRoutingRecord' ] );
			}

			if ( !isset ( $_GET[ 'location' ] ) )
			{
				$locationId = array_shift ( array_keys ( $locations ) );
				header ( 'location: ' . HTTP_ROOT . '?module=routing&action=home&day=0&location=' . $locationId );
			}

			if ( isset ( $_POST[ 'person' ] ) )
			{
				$this->stoneController->createRouting ( $_GET[ 'location' ], $_GET[ 'day' ], $_POST[ 'person' ], $_POST[ 'moment' ] );

			}

			$routing = $this->stoneController->getRouting ( (int)$_GET[ 'location' ], (int)$_GET[ 'day' ] );
			$phonebook = $stonePhonebook->getPersonsByLocation ( );

			return $this->view->home ( $locations, $routing, $phonebook );
		}

	}
?>
