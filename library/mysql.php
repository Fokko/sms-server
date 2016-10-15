<?php
	/**
	 * Mysql
	 */
	class Mysql
	{
		private static $instance;
		public static $queries;
		private $connectionId, $selectedDatabase;
		private $timer;
		private $hostname, $username, $password, $database;
	
		/**
		 * Constructor
		 */
		private function __construct( $connectionId = 0 )
		{
			$this->connectionId		= NULL;
			$this->selectedDatabase		= NULL;
			self::$queries			= array();
			$this->timer			= new Timer();
	
			// Put this in variables and passing trough connect function. Else, on errors, information will be shown in PHP trackback.
			$this->hostname = DB_HOSTNAME;
			$this->username = DB_USERNAME;
			$this->password = DB_PASSWORD;
			$this->database = DB_DATABASE;
	
			$this->connect();
		}
	
		/**
		 * Initialize
		 */
		public static function init()
		{
			if( isset( self::$instance ) === FALSE )
			{
				$c = __CLASS__;
				self::$instance = new $c();
			}
		
			return self::$instance;
		}
	
		/**
		 * Connect
		 * 
		 * Connect to database
		 */
		public function connect()
		{
			$this->connectionId = mysql_connect( $this->hostname, $this->username, $this->password, TRUE ) or $this->error( 'Unable to connect to the database server. Error: ' . mysql_error() . ' => Errornr: ' . mysql_errno() );
	
			if( $this->connectionId !== false )
			{
				$databaseName = 'Resource: #' . (int) $this->connectionId;
	
				if( $this->database != '' )
				{
					$this->select_db( $this->database );
				}
				elseif( $databaseName !== FALSE )
				{
					$this->select_db( $databaseName );
				}
	
				// Execute these queries
				$this->query( 'SET CHARACTER SET UTF8' );
				$this->query( 'SET NAMES UTF8' );
			}
			else
			{
				$this->error( 'Unable to connect to the database server.' );
			}
		}
	
		/**
		 * Destruct
		 */
		public function __destruct()
		{
			$this->close();
		}
	
		/**
		 * Close
		 * 
		 * Function in seperate function so it can be called manually
		 */
		public function close()
		{
			if( is_resource( $this->connectionId ) )
			{
				mysql_close( $this->connectionId ) or $this->error( 'Could not close database "' . $this->connectionId . '"' );
			}
		}
	
		/**
		 * Select database
		 */
		public function select_db( $database )
		{
			if( $this->selectedDatabase == NULL )
			{
				$this->selectedDatabase = mysql_select_db( $database, $this->connectionId ) or $this->error( 'Could not select database "' . $database . '".' );
			}
	
			return $this->selectedDatabase;
		}
	
		/**
		 * Get current resource
		 */
		public function get_resource()
		{
			return $this->connectionId;
		}
	
		/**
		 * Same as query function
		 */
		public function execute( $sql )
		{
			return $this->query( $sql );
		}
	
		/**
		 * Execute query
		 */
		public function query( $sql )
		{
			if( $this->connectionId != NULL )
			{
				// If debug is not enabled, log queries
				if( DEBUGMODUS === FALSE AND ( preg_match( '/INSERT/i', $sql ) OR preg_match( '/DELETE/i', $sql ) OR preg_match( '/UPDATE/i', $sql ) ) )
				{
					// quote insert because its a reserved string for mysql
					mysql_query( sprintf( 'INSERT INTO log ( `insert`, query, user_id, ip_address ) VALUES ( NOW(), "%s", %d, INET_ATON( "%s" ) )', mysql_real_escape_string( $sql ), StoneUserController::getCurrentUserId(), StoneUserController::getCurrentUserIP() ) );
				}
	
				$this->timer->reset();
				$this->timer->start();
				$resource = mysql_query( $sql, $this->connectionId ) or $this->error( $sql );
				$this->timer->stop();
	
				// Save queries if debugmodus is enabled
				if( DEBUGMODUS === TRUE )
				{
					array_push( self::$queries, array( $sql, number_format( $this->timer->get( Timer::$SECONDS ), 6 ) ) );
				}
	
				return $resource;
			}
			else
			{
				$this->error( 'Unable to execute sql statement "' . $sql . '".' );
			}
		}
	
		/**
		 * Mysql Insert Id for current connection resource
		 */
		public function insert_id()
		{
			return mysql_insert_id( $this->connectionId );
		}
	
		/**
		 * Fetch error
		 */
		private function error( $error = '' )
		{
			$error = preg_replace( '/\s+/', ' ', $error );
	
			error_log( sprintf( 'MySQL: %s" => "%s', $error, mysql_error() ) );
			throw new MysqlException( sprintf( '%s" => "%s', $error, mysql_error() ) );
		}
	
		/**
		 * Debug - print executed queries
		 */
		public static function printExecutedQueries()
		{
			$html = '<div id="sql_debug">';
			if( count( self::$queries ) > 0 )
			{
				$html .= '<ul>';
	
				$totalQueries	= count( self::$queries );
				$length			= strlen( $totalQueries );
				$html .= '<li class="strong"><span>' . $totalQueries . '</span> ';
	
				$sql = '';
				$totalTime = 0;
				foreach( self::$queries as $num => $query )
				{
					$sql .= '<li' . ( $query[ 1 ] > 0.005 ? ' class="warning"' : '' ) . '><span>' . sprintf( "%0" . $length . "d", ( $num + 1 ) ) . '</span> <span>' . $query[ 1 ] . '</span> ' . preg_replace( array( '/^(UPDATE|ALTER|ANALYZE|DELETE)/i', '/^(SET)/' ), '<strong><u>$0</u></strong>', $query[ 0 ] ) . '</li>';
					$totalTime += $query[ 1 ];
				}
				$html .= '<span>' . number_format( $totalTime, 6 ) . '</span> Query</li>';
				$html .= $sql;
	
				$html .= '</ul>';
			}
			else
			{
				$html .= 'GEEN MYSQL QUERIES UITGEVOERD';
			}
			$html .= '</div>';
			return $html;
		}
	}
	
	/**
	 * Mysql exception
	 */
	class MysqlException extends CustomException
	{
		/**
		 * Constructor
		 */
		public function __construct( $message = NULL, $code = 0 )
		{
			parent::__construct( $message, $code );
		}
	
		/**
		 * Ouput exceptions
		 */
		public static function printException( $e = NULL, $code = 0 )
		{
			$content = ob_get_contents();
			ob_get_clean();
			echo '<pre>Caught exception ("' . $e->getMessage() . '")' . "\n" . $e . '</pre>';
			echo $content;
		}
	}
?>