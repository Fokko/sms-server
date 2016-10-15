<?php
	include('config.php');
	set_time_limit(0);

	class processImcommingMewssages
	{
		private $keywords = array();
		private $settings = array();
		
		public function start()
		{
			$this->connectDatabase();
			$this->loadSettings();
			$this->loadKeywords();
			
			$messages = $this->getUnprocessedMessages();
			$this->processMessages( $messages );	
			
			$this->bookkeeping();
		}
		
		private function bookkeeping()
		{
			$sql = sprintf('SELECT `id`, `receiver` FROM ozekimessageout WHERE id_phonebook IS NULL');
			
			$result = mysql_query( $sql ) or die( mysql_error() . $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$sql = sprintf('SELECT id FROM phonebook WHERE `number` = \'%s\' LIMIT 1;', $record['receiver']);
				$innerResult = mysql_query( $sql ) or die( mysql_error() . $sql);
				
				if(mysql_num_rows($innerResult) > 0)
				{
					$phonebookId = mysql_result($innerResult, 0);
					$sql = sprintf('UPDATE ozekimessageout SET id_phonebook = %d WHERE id = %d', $phonebookId, $record['id']);
					mysql_query( $sql ) or die( mysql_error() . $sql);					
				}			
			}
		}
		
		private function connectDatabase()
		{
			$db = mysql_connect( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD ) or die( "Kan niet verbinden met de database: " . mysql_error() );
			mysql_select_db( DB_DATABASE, $db ) or die( "Kan de database niet selecteren! " . mysql_error() );
			
			return $db;
		}
		
		private function loadKeywords()
		{
			$sql = 'SELECT
						keyword.id 			AS id,
						keyword.keyword 	AS keyword,
						keyword.remark		AS remark,
						keyword.include		AS include
					FROM
						keyword
					ORDER BY
						keyword.keyword ASC';

			$result = mysql_query( $sql ) or die( mysql_error() );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$this->keywords[ $record[ 'id' ] ] = $record;
								
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
									
				$result2 = mysql_query( $sql ) or die( mysql_error() );
				
				$this->keywords[ $record[ 'id' ] ][ 'locations' ] = array();
				while( $record2 = mysql_fetch_assoc( $result2 ) )
				{
					$this->keywords[ $record[ 'id' ] ][ 'locations' ][ $record2[ 'id' ] ] = $record2;
				}
			}
		}
		
		private function loadSettings()
		{
			$sql = 'SELECT
						`key`,
						`value`
					FROM
						`settings`
					ORDER BY
						`key`
							ASC';
						
			$result = mysql_query( $sql ) or die( mysql_error() );
			
			while( $record = mysql_fetch_array( $result ) )
			{
				$this->settings[ $record['key'] ] = $record['value'];				
			}
		}
		
		private function getUnprocessedMessages()
		{
			$sql = 'SELECT
						*
					FROM
						ozekimessagein
					WHERE
						ozekimessagein.processed = 0';
						
			$result = mysql_query( $sql ) or die( mysql_error() );
			
			$messages = array();
			while( $record = mysql_fetch_array( $result ) )
			{
				$messages[ $record['id'] ] = $record;				
			}			
			
			return $messages;
		}
		
	 	function processMessages( $messages = array() )
		{
			// Door de onverwerkte berichten loopen
			foreach( $messages as $messageId => $message )
			{
				$found = false;
				foreach( $this->keywords as $keyword )
				{
					if( strpos($message['msg'],$keyword['keyword']) !== FALSE )
					{
						$messageOriginal = $message['msg'];
						if($keyword['include'] != 0 )
						{
							$message['msg'] = trim( $keyword['remark'] ) . ' ' . $message['msg'];
						}
						$message['msg'] = trim($message['msg']);
					
						if( count( $keyword[ 'locations' ] ) > 0 )
						{
							$found = TRUE;						
							foreach( $keyword[ 'locations' ] as $locationId => $location )
							{
								$this->processMessage( $message, $locationId );							
							}						
						}

						$message['msg'] = $messageOriginal;
					}
				}
				
				if(!$found)
				{
					$this->processUnroutedMessage( $message );	
				}

				$sql = sprintf( 'UPDATE 
							`ozekimessagein`
						SET
							`processed` = 1
						WHERE
							`id` = %u', $message[ 'id' ] );
				mysql_query( $sql ) or die( mysql_error() );
			}
		}
			
		function processMessage( $message = array(), $locationId = 0 )
		{		
			$date = strtotime( $message[ 'date_recieved' ] );		
			$dayOfWeek = ( date("N", $date ) - 1 ); // Ons systeem begint bij 0
			$vergelijkDag = strtotime( date("H:i:s", $date ) );
			
			$sql = sprintf( '	SELECT
									id,
									workingdayBegin,
									workingdayEnd
								FROM
									routing_day
								WHERE
									dayNumber = %u', $dayOfWeek );
			
			$result = mysql_query( $sql ) or die( mysql_error() );
			list( $routingId, $werkdagBegin, $werkdagEinde ) = mysql_fetch_array( $result );		
	
			// Omzetten in correcte formaten :)
			$werkdagBegin = strtotime( $werkdagBegin );
			$werkdagEinde = strtotime( $werkdagEinde );		
			
			$moment = '';
			if( $vergelijkDag >= $werkdagBegin && $vergelijkDag <= $werkdagEinde )
			{
				$moment = 'in';
			}
			else
			{
				$moment = 'out';
			}
			
			$sql = sprintf( "	SELECT
									DISTINCT phonebook.number as phonenumber
								FROM 
									routing
								LEFT JOIN
									phonebook
								ON
									routing.phonebook_id = phonebook.id
								WHERE
									routing.day = %u
								AND
									routing.location_id = %u
								AND
								(
									routing.moment = 'always'
								OR
									routing.moment = '%s'							
								)", $dayOfWeek, $locationId, $moment );
								
			$result = mysql_query( $sql ) or die( mysql_error() );
	
			if( mysql_num_rows( $result ) > 0 )
			{
				while( $record = mysql_fetch_array( $result ) )
				{
					$this->sendMessage( $record[ 'phonenumber' ], $message );
				}
			}
			elseif( $locationId == 8 || $locationId == 11 )
			{
				// dit is een geblokeerd of niet actueel object
			}			
			else 
			{
				$this->processUnroutedMessage( $message );
			}
		}
		
		function sendMessage( $number, $message = array() )
		{	
			if( $number != '' )
			{
				$sql = sprintf( "INSERT INTO ozekimessageout( receiver, msg ) VALUES( '%s', '%s' );", $number, addslashes( $message['msg'] ) );
				mysql_query( $sql ) or die( mysql_error() );	
			}
		}
		
		function processUnroutedMessage( $message = array() )
		{
			$message['msg'] = 'Onbekend!' . $message['msg'];
			
			$this->sendMessage($this->settings['Onbekend_bericht'], $message);
		}
	}


$processer = new processImcommingMewssages();
$processer->start();
?>
<html>
	<head>
		<title>Scripting</title>
		<script type="text/javascript">
			window.onload = function()
			{
				timerTick();
			}

			function timerTick()
			{
				document.getElementById( 'counter' ).innerHTML = ( document.getElementById( 'counter' ).innerHTML - 1 );

				if( document.getElementById( 'counter' ).innerHTML <= 0 )
				{
					location.reload( true );
				}
				else
				{
					setTimeout( 'timerTick();', 1000 );
				}
			}
		</script>
		<style>
		body
		{
			font-family: tahoma;
		}
		</style>
	</head>
	<body>
		<h1>Herladen</h1>
		<p>Deze pagina wordt over <span style="font-weight: bold;" id="counter">60</span> seconden herladen.</p>
	</body>
</html>

