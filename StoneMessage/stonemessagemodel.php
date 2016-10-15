<?php

	class StoneMessageModel extends StoneModel
	{
		public function getLatestMessages ( $type = 'send', $count = PHP_INT_MAX, $startDate = null, $endDate = null )
		{
			$table = 'ozekimessagein';
			if ( $type == 'send' )
			{
				$table = 'ozekimessageout';
			}

			$sql = sprintf ( 'SELECT
								id,
								sender,
								receiver,
								msg,
								date_send as senttime,
								date_recieved as receivedtime
							FROM
								%s', $table);
			
			if($startDate != null && $endDate != null)
			{
				$sql .= sprintf(' WHERE UNIX_TIMESTAMP( date_send ) BETWEEN %d AND %d ', $startDate, $endDate);	
			}
											
			$sql .= sprintf(' ORDER BY
								id DESC');
								
			if($count!= PHP_INT_MAX)
				$sql .= sprintf(' LIMIT %u', $count );

			$messages = array ( );
			
			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$messages[ $record[ 'id' ] ] = $record;
			}

			return $messages;
		}
		
		public function getLatestMessagesWithContact( $type = 'send', $count = PHP_INT_MAX )
		{
			if ( $type == 'send' )
			{
				$sql = sprintf ( 'SELECT
									ozekimessageout.id as id,
									ozekimessageout.sender as sender,
									ozekimessageout.receiver as receiver,
									ozekimessageout.msg as msg,
									ozekimessageout.date_send as senttime,
									ozekimessageout.status as status,
									ozekimessageout.date_recieved as receivedtime,
									phonebook.name as person
								FROM
									ozekimessageout								
								LEFT JOIN 
									phonebook
								ON
									ozekimessageout.id_phonebook = phonebook.id
								ORDER BY
									date_send DESC
								LIMIT %u', $count );
			}
			else
			{			
				$sql = sprintf ( 'SELECT
									ozekimessagein.id as id,
									ozekimessagein.sender as sender,
									ozekimessagein.receiver as receiver,
									ozekimessagein.msg as msg,
									ozekimessagein.date_send as senttime,
									ozekimessagein.date_recieved as recievedtime,
									\'\' as person
								FROM
									ozekimessagein
								ORDER BY
									date_recieved DESC
								LIMIT %u', $count );
			}
			$messages = array ( );

			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$messages[ $record[ 'id' ] ] = $record;
			}

			return $messages;
		}
		
		
		public function updateKeyword( $keywordId, $keyword = array() )
		{
			$sql = sprintf( '	DELETE FROM
									keyword_location
								WHERE
									keyword_id = %u', $keywordId );
			$this->mysql->query ( $sql );
			
			foreach( $keyword[ 'locations' ] as $locationId )
			{
				$sql = sprintf( 'INSERT INTO keyword_location( keyword_id, location_id ) VALUES( %u, %u );', $keywordId, $locationId );
				
				$this->mysql->query ( $sql );
			}
			
			$sql = sprintf( '	UPDATE
									keyword
								SET
									remark = \'%s\',
									include = %d									
								WHERE
									keyword.id = %u', $keyword['remark'], $keyword['include'], $keywordId );
			$this->mysql->query ( $sql );
			
			return $keywordId;
		}

		public function getRouting ( $location = -1, $day = -1 )
		{
			$sql = 'SELECT 
						routing.id as id,
						routing.location_id as location_id,
						routing.phonebook_id as phonebook_id,
						routing.moment as moment,
						phonebook.name as name,
						phonebook.number as number,
						routing.day as day
					FROM 
						`routing`
					LEFT JOIN
						`phonebook`
					ON
						routing.phonebook_id = phonebook.id
					LEFT JOIN
						`location`
					ON
						routing.location_id = location.id
					WHERE 1';

			if ( $location >= 0 )
			{
				$sql .= sprintf ( ' AND routing.location_id = %u', $location );
			}

			if ( $day >= 0 )
			{
				$sql .= sprintf ( ' AND routing.day = %u', $day );
			}

			$routing = array ( );

			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$routing[ $record[ 'id' ] ] = $record;
			}

			return $routing;
		}

		public function createRouting ( $location = 0, $day = 0, $person = 0, $moment = 'always' )
		{		
			$sql = sprintf ( "INSERT INTO 
								routing( day, phonebook_id, location_id, moment ) 
							VALUES
								( %u, %s, %s, '%s' )", $day, $person, $location, $moment );

			$this->mysql->query ( $sql );

			return mysql_insert_id ( );
		}

		public function deleteRouting( $recordId = 0 )
		{
			$sql = sprintf( 'DELETE FROM routing WHERE id = %u;', $recordId );
			$this->mysql->query ( $sql );
		}
				
		public function deleteKeyword ( $keywordId = 0 )
		{		
			$sql = sprintf( 'DELETE FROM keyword_location WHERE keyword_id = %u;', $keywordId );
			$this->mysql->query ( $sql );
		
			$sql = sprintf( 'DELETE FROM keyword WHERE id = %u;', $keywordId );
			$this->mysql->query ( $sql );			
		}
		
		public function deleteMessage( $messageId = 0 )
		{
			$sql = sprintf( 'DELETE FROM ozekimessagein WHERE id = %u;', $messageId );
			return $this->mysql->query ( $sql );			
		}
		
		public function getKeywords ( $locationId = 0 )
		{
			$sql = 'SELECT
						keyword.id 			AS id,
						keyword.keyword 	AS keyword,
						(
							SELECT
								COUNT(*)
							FROM	
								ozekimessagein
							WHERE
								ozekimessagein.msg
							LIKE							
								CONCAT("%",keyword.keyword,"%")
						) as count,
						keyword.remark as remark,
						keyword.include as include
					FROM
						keyword
					LEFT JOIN
						keyword_location
					ON
						keyword.id = keyword_location.keyword_id';
			
			if( $locationId != 0 )
			{
				$sql .= ' WHERE keyword_location.location_id = ' . $locationId;
			}
					
			$sql .=	' ORDER BY
						keyword.keyword ASC';

			$keywords = array ( );

			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$keywords[ $record[ 'id' ] ] = $record;
								
				// Ophalen van locaties
				$sql = sprintf( 'SELECT
									location.id as `id`,
									location.name as `name`
								FROM
									keyword_location
								LEFT JOIN
									location
								ON
									keyword_location.location_id = location.id
								WHERE
									keyword_location.keyword_id = %u', $record[ 'id' ] );
									
				$result2 = $this->mysql->query ( $sql );
				
				$keywords[ $record[ 'id' ] ][ 'locations' ] = array();
				while( $record2 = mysql_fetch_assoc( $result2 ) )
				{
					$keywords[ $record[ 'id' ] ][ 'locations' ][ $record2[ 'id' ] ] = $record2;
				}
			}

			return $keywords;
		}
		
		public function getKeyword( $keywordId = 0 )
		{
			$sql = sprintf( 'SELECT
						keyword.id 			AS id,
						keyword.keyword 	AS keyword,
						(
							SELECT
								COUNT(*)
							FROM	
								ozekimessagein
							WHERE
								ozekimessagein.msg
							LIKE							
								CONCAT("%%",keyword.keyword,"%%")
						) as count,
						keyword.remark as remark,
						keyword.include as include
					FROM
						keyword
					WHERE
						keyword.id = %u
					ORDER BY
						keyword.keyword ASC', $keywordId );

			$keyword = array ( );

			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$keyword = $record;
								
				// Ophalen van locaties
				$sql = sprintf( 'SELECT
									location.id as `id`,
									location.name as `name`
								FROM
									keyword_location
								LEFT JOIN
									location
								ON
									keyword_location.location_id = location.id
								WHERE
									keyword_location.keyword_id = %u', $record[ 'id' ] );
									
				$result2 = $this->mysql->query ( $sql );
				
				$keyword[ 'locations' ] = array();
				while( $record2 = mysql_fetch_assoc( $result2 ) )
				{
					$keyword[ 'locations' ][ $record2[ 'id' ] ] = $record2;
				}
			}

			return $keyword;
		}

		public function getUnboundMessages ( )
		{
			$sql = 'SELECT
						ozekimessagein.id AS id,
						ozekimessagein.msg AS msg
					FROM
						ozekimessagein
					WHERE
						(
							SELECT
								COUNT(*)
							FROM
								keyword
							WHERE
								ozekimessagein.msg
							LIKE							
								CONCAT("%",keyword.keyword,"%")
						)  = 0
					GROUP BY
						ozekimessagein.msg
					ORDER BY
						ozekimessagein.msg ASC';

			$messages = array ( );

			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$messages[ $record[ 'id' ] ] = $record;
			}

			return $messages;
		}

		public function getInboundMessage ( $messageId = 0 )
		{
			$sql = sprintf ( 'SELECT
								*
							FROM
								ozekimessagein
							WHERE
								ozekimessagein.id = %u', $messageId );

			$result = $this->mysql->query ( $sql );

			if ( mysql_num_rows ( $result ) > 0 )
			{
				return mysql_fetch_assoc ( $result );
			}
		}

		public function createKeyword ( $keyword = '', $remark = '', $include = 0, $locations = array() )
		{
			$sql = sprintf ( "INSERT INTO
								keyword( `keyword`, `remark`, `include` )
							VALUES
								( '%s', '%s', %d )", $keyword, $remark, $include );

			$this->mysql->query ( $sql );
			
			$keywordId = mysql_insert_id();
						
			foreach( $locations as $locationId )
			{
				$sql = sprintf( 'INSERT INTO keyword_location( keyword_id, location_id ) VALUES( %u, %u );', $keywordId, $locationId );
				$this->mysql->query ( $sql );
			}
			
			return $keywordId;
		}

	}
?>