<?php
	/**
	 * Timer
	 * 
	 * Source: unkown
	 */
	class Timer
	{
		// Constants
		public static $CMD_START = 'start';
		public static $CMD_STOP = 'end';

		public static $SECONDS = 0;
		public static $MILLISECONDS = 1;
		public static $MICROSECONDS = 2;

		public static $USECDIV = 1000000;

		private $_running;
		private $_queue;

		public function __construct ( )
		{
			$this->_running = false;
			$this->_queue = array ( );
		}

		public function start ( )
		{
			$this->_pushTime ( self::$CMD_START );
		}

		public function stop ( )
		{
			$this->_pushTime ( self::$CMD_STOP );
		}

		public function reset ( )
		{
			$this->_queue = array ( );
		}

		private function _pushTime ( $cmd )
		{
			$mt = microtime ( );
			if ( $cmd == self::$CMD_START )
			{
				if ( $this->_running === true )
				{
					trigger_error ( 'Timer has already been started', E_USER_NOTICE );
					return;
				}

				$this->_running = true;

			} elseif ( $cmd == self::$CMD_STOP )
			{
				if ( $this->_running === false )
				{
					trigger_error ( 'Timer has already been stopped/paused or has not yet been started', E_USER_NOTICE );
					return;
				}

				$this->_running = false;

			}
			else
			{
				trigger_error ( 'Invalid command specified', E_USER_ERROR );
				return;
			}

			if ( $cmd === self::$CMD_START )
			{
				$mt = microtime ( );
			}

			list ( $usec, $sec ) = explode ( ' ', $mt );

			$sec = (int) $sec;
			$usec = (float) $usec;
			$usec = (int) ( $usec * self::$USECDIV );

			$time = array (
				$cmd => array (
					'sec' => $sec,
					'usec' => $usec,
				)
			);

			if ( $cmd == self::$CMD_START )
			{
				array_push ( $this->_queue, $time );

			}
			elseif ( $cmd == self::$CMD_STOP )
			{
				$count = count ( $this->_queue );
				$array = &$this->_queue[ $count - 1 ];
				$array = array_merge ( $array, $time );
			}
		}

		public function get ( $format = NULL )
		{
			if ( $this->_running === true )
			{
				trigger_error ( 'Forcing timer to stop', E_USER_NOTICE );
				$this->stop ( );
			}

			if( $format === NULL )
			{
				$format = self::$SECONDS;
			}

			$sec = 0;
			$usec = 0;

			foreach ( $this->_queue as $time )
			{
				$start = $time[ self::$CMD_START ];
				$end = $time[ self::$CMD_STOP ];

				$sec_diff = $end[ 'sec' ] - $start[ 'sec' ];
				if ( $sec_diff === 0 )
				{
					$usec += ($end[ 'usec' ] - $start[ 'usec' ]);

				}
				else
				{
					$sec += $sec_diff - 1;
					$usec += ( self::$USECDIV - $start[ 'usec' ] ) + $end[ 'usec' ];
				}
			}

			if ( $usec > self::$USECDIV )
			{
				$sec += (int) floor ( $usec / self::$USECDIV );
				$usec = $usec % self::$USECDIV;
			}

			switch( $format )
			{
				case self::$MICROSECONDS :
					return ( $sec * self::$USECDIV ) + $usec;
					break;

				case self::$MILLISECONDS :
					return ( $sec * 1000 ) + (int) round ( $usec / 1000, 0 );
					break;

				default :
				case self::$SECONDS :
					return (float) $sec + (float) ( $usec / self::$USECDIV );
					break;
			}
		}

		public static function getAverage ( $format = NULL )
		{
			if( $format === NULL )
			{
				$format = self::$SECONDS;
			}

			$count = count ( $this->_queue );
			$sec = 0;
			$usec = $this->get ( self::$MICROSECONDS );

			if ( $usec > self::USECDIV )
			{
				$sec += (int) floor ( $usec / self::$USECDIV );
				$usec = $usec % self::$USECDIV;
			}

			switch( $format )
			{
				case self::$MICROSECONDS :
					$value = ( $sec * self::$USECDIV ) + $usec;
					return round ( $value / $count, 2 );
					break;

				case self::$MILLISECONDS :
					$value = ( $sec * 1000 ) + (int) round ( $usec / 1000, 0 );
					return round ( $value / $count, 2 );
					break;

				default :
				case self::$SECONDS :
					$value = (float) $sec + (float) ( $usec / self::$USECDIV );
					return round ( $value / $count, 2 );
					break;
			}
		}

	}
?>