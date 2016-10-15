<?php
class StoneUserModel extends StoneModel
{
	private $currentUser, $fetchedUsers, $users, $userProperties;

	public function __construct()
	{
		parent::__construct();

		$this->fetchedUsers				= FALSE;
		$this->users					= array();
		$this->currentUser				= array();
	}

	public function saveLoginLog( $userId = 0, $ipAddress = '', $countryCode = '' )
	{
		return $this->mysql->execute( sprintf( 'INSERT IGNORE INTO user_login_log ( user_id, ip_address, timestamp, country_code ) VALUES ( %u, INET_ATON( "%s" ), NOW(), "%s" );', $userId, $ipAddress, $countryCode ) );
	}
	

	public function getLastLoginLogByUserId( $userId = 0 )
	{
		$result = $this->mysql->execute( sprintf( 'SELECT *, INET_NTOA( ip_address ) as ip_address, UNIX_TIMESTAMP( timestamp ) as timestamp FROM user_login_log WHERE user_id = %u ORDER BY timestamp DESC LIMIT 0,1;', $userId ) );
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				return $record;
			}
		}

		return array();
	}

	public function getLoginLogByUserId( $userId = 0 )
	{
		$result = $this->mysql->execute( sprintf( 'SELECT *, INET_NTOA( ip_address ) as ip_address FROM user_login_log WHERE user_id = %u;', $userId ) );
		$logs = array();
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$logs[ $record[ 'user_id' ] ][] = $record;
			}
		}

		return $logs;
	}

	public function getUsers( $userProperties = TRUE )
	{
		if( $this->fetchedUsers === TRUE )
		{
			return $this->users;
		}

		if( $userProperties === TRUE )
		{
			$this->getAllUserProperties();
			$this->fetchedUsers = TRUE;
		}

		$result = $this->mysql->execute( 'SELECT * FROM user;' );
		$users = array();
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$users[ $record[ 'id' ] ] = $record;
				if( $userProperties === TRUE )
				{
					$users[ $record[ 'id' ] ][ 'properties' ] = $this->getUserPropertiesByUserId( $record[ 'id' ] );
				}
			}
		}

		if( $userProperties === TRUE )
		{
			$this->users = $users;
		}

		return $users;
	}

	public function getUserParentId( $userId = 0 )
	{
		$sql = sprintf( 'SELECT parent_id FROM user WHERE id = %u;', $userId );
		$result = $this->mysql->execute( $sql );

		return mysql_result( $result, 0 );
	}

	public function getUserByEmail( $email )
	{
		$result = $this->mysql->execute( sprintf( 'SELECT * FROM user WHERE email = "%s";', $email ) );
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				return $record;
			}
		}

		return array();
	}

	public function getUserFilesByUserId( $userId = 0 )
	{
		$result = $this->mysql->execute( sprintf( 'SELECT * FROM user_file WHERE user_id = %d;', $userId ) );
		$files = array();
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$files[ $record[ 'media_id' ] ] = $record;
			}
		}

		return $files;
	}

	public function getUserFilesByUserIdOnTypes( $userId = 0 )
	{
		$result = $this->mysql->execute( sprintf( 'SELECT * FROM user_file WHERE user_id = %d;', $userId ) );
		$files = array();
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$files[ $record[ 'file_type' ] ] = $record;
			}
		}

		return $files;
	}

	public function getUserById( $userId = 0, $properties = TRUE )
	{
		if( isset( $this->users[ $userId ] ) === TRUE )
		{
			return $this->users[ $userId ];
		}

		$result = $this->mysql->execute( sprintf( 'SELECT * FROM user WHERE id = %d;', $userId ) );
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$user = $record;

				if( $properties === TRUE )
				{
					$user[ 'properties' ] = $this->getUserPropertiesByUserId( $userId );
					$this->users[ $userId ] = $user;
				}
			}

			return $user;
		}

		return array();
	}

	private function getAllUserProperties()
	{
		if( $this->fetchedUsers === TRUE )
		{
			return $this->userProperties;
		}

		$result = $this->mysql->execute( 'SELECT * FROM user_property;' );
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$this->userProperties[ $record[ 'user_id' ] ][ $record[ 'property' ] ] = $record[ 'value' ];
			}
		}

		return $this->userProperties;
	}

	public function getUserPropertiesByUserId( $userId = 0 )
	{
		if( isset( $this->userProperties[ $userId ] ) )
		{
			return $this->userProperties[ $userId ];
		}

		$result = $this->mysql->execute( sprintf( 'SELECT property, value FROM user_property WHERE user_id = %d;', $userId ) );
		$userProperties = array();
		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				$userProperties[ $record[ 'property' ] ] = $record[ 'value' ];
			}

			// Save for later use
			$this->userProperties[ $userId ] = $userProperties;
		}

		return $userProperties;
	}

	public function getSubUsersIds( $userId = 0, $deep = 0 )
	{
		$userIds = array();

		if( $deep > 0 )
		{
			$sql = sprintf( 'SELECT id, ( SELECT count(*) FROM user as us WHERE us.parent_id = user.id ) as subusers FROM user WHERE parent_id = %d;', $userId );
		}
		else
		{
			$sql = sprintf( 'SELECT id FROM user WHERE parent_id = %d;', $userId );
		}

		$result = $this->mysql->execute( $sql );

		if( mysql_num_rows( $result ) )
		{
			while( $record = mysql_fetch_assoc( $result ) )
			{
				array_push( $userIds, $record[ 'id' ] );

				if( $deep > 0 AND IsVar::set( $record, 'subusers', 0 ) > 0 )
				{
					$userIds = array_merge( $userIds, $this->getSubUsersIds( $record[ 'id' ], $deep-- ) );
				}
			}
		}

		return $userIds;
	}

	public function getUsersByIds( $userIds = array() )
	{
		$fetchedUsers = array();

		if( count( $userIds ) > 0 )
		{
			$result = $this->mysql->execute( sprintf( 'SELECT * FROM user WHERE id IN (%s);', implode( ',', $userIds ) ) );
			if( mysql_num_rows( $result ) )
			{
				while( $record = mysql_fetch_assoc( $result ) )
				{
					$fetchedUsers[ $record[ 'id' ] ] = $record;
					$fetchedUsers[ $record[ 'id' ] ][ 'properties' ] = array();

					$this->users[ $record[ 'id' ] ] = $fetchedUsers[ $record[ 'id' ] ];
				}
			}

			if( count( $this->userProperties ) > 0 )
			{
				$fetchProperties = array_diff( array_keys( $fetchedUsers ), array_keys( $this->userProperties ) );
			}
			else
			{
				$fetchProperties = array_keys( $fetchedUsers );
			}

			if( count( $fetchProperties ) > 0 )
			{
				$result_properties = $this->mysql->execute( sprintf( 'SELECT * FROM user_property WHERE user_id IN (%s);', implode( ',', $fetchProperties ) ) );
				if( mysql_num_rows( $result_properties ) )
				{
					while( $record_properties = mysql_fetch_assoc( $result_properties ) )
					{
						$fetchedUsers[ $record_properties[ 'user_id' ] ][ 'properties' ][ $record_properties[ 'property' ] ] = $record_properties[ 'value' ];
						$this->userProperties[ $record_properties[ 'user_id' ] ][ $record_properties[ 'property' ] ] = $record_properties[ 'value' ];

						$this->users[ $record_properties[ 'user_id' ] ] = $fetchedUsers[ $record_properties[ 'user_id' ] ];
					}
				}
			}
		}

		return $fetchedUsers;
	}

	public function deleteUser( $userId = 0 )
	{
		$documents = new Documents();
		$documentsByUser = $documents->getDocumentsByUser( $userId );
		foreach( $documentsByUser as $documentId => $document )
		{
			$documents->deleteDocument( $documentId );
		}

		$this->mysql->execute( sprintf( 'DELETE FROM user_file WHERE user_id = %d;', $userId ) );
		$this->mysql->execute( sprintf( 'DELETE FROM user_property WHERE user_id = %d;', $userId ) );
		$this->mysql->execute( sprintf( 'DELETE FROM user WHERE id = %d;', $userId ) );
	}

	public function saveUser( $userId = 0, $values )
	{
		$values = Deep::db_escape( $values );

		if( $userId > 0 )
		{
			$this->mysql->execute( sprintf( 'UPDATE user SET parent_id = %d, email = "%s", role = "%s" WHERE id = %d;', $values[ 'parent_id' ], $values[ 'email' ], $values[ 'role' ], $userId ) );
			$this->deleteUserProperties( $userId );
		}
		else
		{	
			$this->mysql->execute( sprintf( 'INSERT INTO user ( parent_id, email, role ) VALUES ( %d, "%s", "%s" );', $values[ 'parent_id' ], $values[ 'email' ], $values[ 'role' ] ) );
			$userId = $this->mysql->insert_id();
		}

		if( isset( $values[ 'properties' ] ) AND is_array( $values[ 'properties' ] ) )
		{
			foreach( $values[ 'properties' ] as $property => $value )
			{
				if( trim( $value ) != '' )
				{
					$this->saveUserProperty( $userId, $property, $value );
				}
			}
		}

		return $userId;
	}

	public function saveUserVerifyCode( $userId, $verifyCode )
	{
		return $this->mysql->execute( sprintf( 'UPDATE user SET verify_code = "%s" WHERE id = %d;', $verifyCode, $userId ) );
	}

	public function saveUserPassword( $userId, $password )
	{
		return $this->mysql->execute( sprintf( 'UPDATE user SET password = "%s" WHERE id = %d;', $password, $userId ) );
	}

	public function deleteUserProperties( $userId )
	{
		return $this->mysql->execute( sprintf( 'DELETE FROM user_property WHERE user_id = %d;', $userId ) );
	}

	public function deleteUserProperty( $userId, $property )
	{
		return $this->mysql->execute( sprintf( 'DELETE FROM user_property WHERE user_id = %d AND property = "%s";', $userId, $property ) );
	}

	public function saveUserProperty( $userId, $property, $value )
	{
		return $this->mysql->execute( sprintf( 'INSERT INTO user_property ( user_id, property, value ) VALUES ( %d, "%s", "%s" );', $userId, $property, $value ) );
	}

	public function saveUserFile( $userId, $imageId, $fileType )
	{
		return $this->mysql->execute( sprintf( 'INSERT INTO user_file ( user_id, media_id, file_type ) VALUES ( %d, %d, "%s" );', $userId, $imageId, $fileType ) );
	}

	public function deleteUserFile( $userId, $fileType )
	{
		return $this->mysql->execute( sprintf( 'DELETE FROM user_file WHERE user_id = %d AND file_type = "%s";', $userId, $fileType ) );
	}
}
?>
