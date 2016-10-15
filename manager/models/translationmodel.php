<?php

class TranslationModel extends Model
{
	private $languages;

	public function __construct()
	{
		parent::__construct();

		$this->languages = array();
	}

	public function translateHTML( $html )
	{
		return $this->parseHtml( $html );
	}

	private function parseHtml( $html )
	{
		$translationTexts = $this->getTranslationTexts( $html );
		if( count( $translationTexts ) > 0 )
		{
			$translatedTexts = $this->getTranslations( $translationTexts );
		}

		foreach( $translationTexts as $translationText )
		{
			if( array_key_exists( $translationText, $translatedTexts ) )
			{
				if( $translatedTexts[ $translationText ] != null )
				{
					$html = str_replace( '{{' . $translationText . '}}', $translatedTexts[ $translationText ], $html );
				}
				else
				{
					$html = str_replace( '{{' . $translationText . '}}', $translationText, $html );
				}
			}
			else
			{
				$sql = sprintf( 'SELECT id FROM translation WHERE translation = "%s" AND language_id = 0;', mysql_escape_string( $translationText ) );
				$result = $this->mysql->execute( $sql );

				if( mysql_num_rows( $result ) == 0 )
				{
					$this->addTranslation( $translationText );
				}
				else
				{
					list( $id ) = mysql_fetch_row( $result );
				}

				$html = str_replace( '{{' . $translationText . '}}', $translationText, $html );
			}
		}

		return $html;
	}

	private function getTranslationTexts( $html )
	{
		preg_match_all( '/{{([^(}})]*)}}/', $html, $matches );

		return array_unique( $matches[ 1 ] );
	}

	public function getTranslations( $translationTexts = array() )
	{
		$translations1 = array();
		$translations2 = array();
		$translations3 = array();

		// Get the ids from the dutch translations
		$sql = sprintf( 'SELECT id, translation FROM translation WHERE language_id = 0 AND translation IN ( "%s" )', implode( '", "', $translationTexts ) );
		$result = $this->mysql->execute( $sql );

		if( mysql_num_rows( $result ) )
		{
			while ( $record = mysql_fetch_assoc( $result ) )
			{
				$translations1[ $record[ 'translation' ] ] = $record[ 'id' ];
			}

			// Get the translations for the specific language
			$sql = sprintf( 'SELECT parent_id, translation FROM translation WHERE language_id = %d AND parent_id IN ( %s );', StoneUserController::getUserLanguage(), implode( ', ', $translations1 ) );
			$result = $this->mysql->execute( $sql );

			if( mysql_num_rows( $result ) )
			{
				while ( $record = mysql_fetch_assoc( $result ) )
				{
					$translations2[ $record[ 'parent_id' ] ] = stripslashes( $record[ 'translation' ] );
				}
			}

			// If there is a translation in the specific language, use it, else use the dutch translation
			if( count( $translations1 ) > 0 )
			{
				foreach ( $translations1 as $translation => $id )
				{
					if( isset( $translations2[ $id ] ) )
					{
						$translations3[ $translation ] = $translations2[ $id ];
					}
					else
					{
						$translations3[ $translation ] = $translation;
					}
				}
			}
		}

		return $translations3;
	}

	public function getAllTranslations()
	{
		$translations = array();

		$result = $this->mysql->execute( 'SELECT * FROM translation;' );

		if( mysql_num_rows( $result ) > 0 )
		{
			while( list( $id, $parentId, $langId, $translation ) = mysql_fetch_row( $result ) )
			{
				$translations[ ( $parentId == 0 ? $id : $parentId ) ][ $langId ] = array( 'id' => $id, 'translation' => stripslashes( $translation ) );
			}
		}

		return $translations;
	}

	public function addTranslation( $text )
	{
		$sql = sprintf( 'INSERT INTO translation ( translation ) VALUES ( "%s" )', mysql_escape_string( $text ) );
		return $this->mysql->execute( $sql );
	}

	public function updateTranslations( $updatedTranslations = array() )
	{
		foreach( $updatedTranslations as $var => $value )
		{
			$value = preg_replace( '/\{|\}/', '', trim( $value ) );

			if( is_int( $var ) )
			{
				$this->mysql->execute( sprintf( "UPDATE translation SET translation = '%s' WHERE id = %d", mysql_escape_string( $value ), mysql_escape_string( $var ) ) );
			}
			elseif( !empty( $value ) )
			{
				list( $parent_id, $lang_id ) = explode( ',', $var );

				$this->mysql->execute( sprintf( 'INSERT INTO translation ( parent_id, language_id, translation ) VALUES ( %d, %d, "%s" )', mysql_escape_string( $parent_id ), mysql_escape_string( $lang_id ), mysql_escape_string( $value ) ) );
			}
		}

		return true;
	}

	public function getAllLanguages()
	{
		if( count( $this->languages ) > 0 )
		{
			return $this->languages;
		}

		$this->languages[ 0 ] = array(
			'id' => 0,
			'name' => 'Engels',
			'google_code' => 'en'
		);

		$result = $this->mysql->execute( 'SELECT * FROM language;' );
		if( mysql_num_rows( $result ) > 0 )
		{
			while ( $record = mysql_fetch_assoc( $result ) )
			{
				$this->languages[ $record[ 'id' ] ] = $record;
			}
		};

		return $this->languages;
	}
}