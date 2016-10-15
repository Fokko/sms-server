<?php

	/**
	 * Is Var Set
	 * 
	 * Check if var is set in array, if not return custom value.
	 * Presistant mode to loop trough array 
	 */
	class IsVar
	{
		public static function set ( $array = array(), $key = '', $return = '', $presistant = FALSE )
		{
			if ( is_array ( $array ) )
			{
				if ( isset ( $array[ $key ] ) )
				{
					return $array[ $key ];
				}
				elseif ( $presistant === TRUE )
				{
					// If presistant, recursion
					$value = self::findKey ( $array, $key );
					if ( $value !== FALSE )
					{
						return $value;
					}
				}
			}

			return $return;
		}

		/**
		 * Find key in array
		 */
		private static function findKey ( $array, $key )
		{
			foreach ( $array as $_key => $_value )
			{
				if ( $_key == $key )
				{
					return $array[ $_key ];
				}
			}

			foreach ( $array as $_key => $_value )
			{
				if ( is_array ( $_value ) )
				{
					return self::findKey ( $_value, $key );
				}
			}

			return false;
		}

	}
