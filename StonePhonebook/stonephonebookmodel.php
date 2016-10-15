<?php

	class StonePhonebookModel extends StoneModel
	{

		public function getAllPersons ( )
		{
			$sql = 'SELECT
						phonebook.id as id,
						phonebook.name as name,
						phonebook.number as number,
						location.name as location
					FROM
						phonebook
					LEFT JOIN
						location
					ON
						phonebook.location_id = location.id
					ORDER BY
						location.name ASC,
						phonebook.name ASC';

			$result = $this->mysql->query ( $sql );

			$persons = array ( );

			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$persons[ $record[ 'id' ] ] = $record;

			}

			return $persons;

		}
		
		public function deleteLocation( $locationId = 0 )
		{
			$sql = sprintf( 'DELETE FROM routing WHERE location_id = %u', $locationId );
			$this->mysql->query ( $sql );
			
			$sql = sprintf( 'DELETE FROM phonebook WHERE location_id = %u', $locationId );
			$this->mysql->query ( $sql );
			
			$sql = sprintf( 'DELETE FROM keyword_location WHERE location_id = %u', $locationId );
			$this->mysql->query ( $sql );
			
			$sql = sprintf( 'DELETE FROM location WHERE id = %u', $locationId );
			$this->mysql->query ( $sql );
		}
		
		public function deletePerson( $userId )
		{
			$sql = sprintf( 'DELETE FROM phonebook WHERE id = %u', $userId );
			$this->mysql->query ( $sql );			
		}

		public function getPersonsByLocation ( $location = -1 )
		{
			$sql = 'SELECT
						location.name 		as location,
						phonebook.name 		as name,
						phonebook.number 	as number,
						phonebook.id 		as id
					FROM
						location
					LEFT JOIN
						phonebook
					ON
						location.id = phonebook.location_id
					WHERE 
						1';

			if ( $location > 0 )
			{
				$sql .= sprintf ( ' AND location.id = %u', $location );
			}

			$sql .= ' ORDER BY 
						location.name ASC,
						phonebook.name ASC';

			$locations = array ( );
			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$locations[ $record[ 'location' ] ][ $record[ 'id' ] ] = $record;

			}

			return $locations;
		}

		public function getAllLocations ( $onlyVisible )
		{
			$sql = 'SELECT
						location.id as id,
						location.name as name
					FROM
						location
					WHERE 
						1';
						
			if( $onlyVisible )
			{
				$sql .= ' AND `display` = 1';
			}
			$sql .= ' ORDER BY
						location.name ASC';

			$result = $this->mysql->query ( $sql );

			$locations = array ( );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$locations[ $record[ 'id' ] ] = $record;
			}

			return $locations;
		}

		public function createLocation ( $name = '' )
		{
			$sql = sprintf ( "INSERT INTO location( `name` ) VALUES( '%s' );", $name );
			$this->mysql->query ( $sql );

			return mysql_insert_id ( );
		}

		public function createPerson ( $name = '', $number = '', $location = 0 )
		{
			$sql = sprintf ( "INSERT INTO phonebook( `name`,`number`,`location_id` ) VALUES( '%s', '%s', %u );", $name, $number, $location );
			$this->mysql->query ( $sql );

			return mysql_insert_id ( );
		}

	}
?>