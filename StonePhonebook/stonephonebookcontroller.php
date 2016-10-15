<?php

	class StonePhonebookController extends StoneController
	{
		public function getAllLocations ( $onlyVisible = false )
		{
			return $this->model->getAllLocations ( $onlyVisible );
		}

		public function createLocation ( $name = '' )
		{
			return $this->model->createLocation ( $name );
		}
		
		public function deletePerson( $personId )
		{
			return $this->model->deletePerson ( $personId );
		}		

		public function getAllPersons ( )
		{
			return $this->model->getAllPersons ( );
		}

		public function deleteLocation( $locationId = 0 )
		{
			return $this->model->deleteLocation( $locationId );
		}
		
		public function createPerson ( $name = '', $number = '', $locationId = 0 )
		{
			return $this->model->createPerson ( $name, $number, $locationId );
		}

		public function getPersonsByLocation ( $location = -1 )
		{
			return $this->model->getPersonsByLocation ( $location );

		}

	}
?>