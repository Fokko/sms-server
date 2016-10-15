<?php
	class PhonebookController extends Controller
	{
		private $stoneController;

		public function __construct ( )
		{
			parent::__construct ( );
			$this->stoneController = StoneController::getClass ( 'StonePhonebook' );			
		}

		public function home ( )
		{
			if ( isset ( $_POST[ 'person_name' ] ) && $_POST[ 'person_name' ] != '' )
			{
				$this->stoneController->createPerson ( $_POST[ 'person_name' ], $_POST[ 'person_number' ], (int)$_POST[ 'location_id' ] );
			}
			
			if( isset( $_GET[ 'verwijderGebruiker'  ] ) && is_numeric( $_GET[ 'verwijderGebruiker' ] ) )
			{ 
				$this->stoneController->deletePerson( $_GET[ 'verwijderGebruiker' ] );
			}
			
			$persons = $this->stoneController->getAllPersons ( );
			$locations = $this->stoneController->getAllLocations ( );
			return $this->view->home ( $persons, $locations );
		}

		public function location ( )
		{
			if ( isset ( $_GET[ 'deleteLocation' ] ) && $_GET[ 'deleteLocation' ] != '' && is_numeric( $_GET[ 'deleteLocation' ] ) )
			{
				$this->stoneController->deleteLocation( $_GET[ 'deleteLocation' ] );
			}

			if ( isset ( $_POST[ 'location_name' ] ) && $_POST[ 'location_name' ] != '' )
			{
				$this->stoneController->createLocation ( $_POST[ 'location_name' ] );
			}

			$locations = $this->stoneController->getAllLocations ( true );

			return $this->view->location ( $locations );
		}

	}
?>
