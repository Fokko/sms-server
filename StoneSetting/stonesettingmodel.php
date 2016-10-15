<?php

	class StoneSettingModel extends StoneModel
	{

		public function getSettings ( )
		{
			$sql = sprintf ( 'SELECT
								`key`,
								`value`
							FROM
								`settings`
							ORDER BY
								`key`
									ASC' );

			$settings = array ( );

			$result = $this->mysql->query ( $sql );
			while ( $record = mysql_fetch_assoc ( $result ) )
			{
				$settings[ $record[ 'key' ] ] = $record[ 'value' ];
			}

			return $settings;
		}

		public function setSettings ( $settings = array() )
		{
			foreach ( $settings as $key => $value )
			{
				$sql = sprintf( "UPDATE `settings` SET value = '%s' WHERE `key` = '%s';", $value, $key );
				$this->mysql->query ( $sql );
			}
		}

	}
?>