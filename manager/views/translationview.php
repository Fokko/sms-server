<?php

class TranslationView extends View
{
	public function showDashboardMenu()
	{
		$html = '	<div id="icondock" class="grid_12">' . PHP_EOL;
		$html .= '		<ul>';
		$html .= '			<li><a href="' . HTTP_ROOT . 'index.php?module=translation&action=view" rel="tipsy" title="{{Translations dashboard}}"><img src="' . HTTP_ROOT . 'images/icondock/reload.png" alt="{{Translations dashboard}}" /><br />{{Translations dashboard}}</a></li>' . PHP_EOL;
		$html .= '		</ul>';
		$html .= '	</div>';

		return $html;
	}

	public function showTranslations( $translations, $languages )
	{
		$html = $this->showDashboardMenu();

		$colWidth = 100 / ( count( $languages ) + 1 );

		$html .= '<h1>{{Translations}}</h1>' . PHP_EOL;
		$html .= '<a href="' . HTTP_ROOT . 'index.php?module=translation&action=edit" title="{{Edit translations}}">{{Edit translations}}</a><br /><br />' . PHP_EOL;

		$html .= '<table cellspacing="0" cellpadding="0">' . PHP_EOL;
		$html .= '	<tr>' . PHP_EOL;
		$html .= '		<th width="' . $colWidth . '%">{{English}} ({{Initial language}})</th>' . PHP_EOL;

		foreach ( $languages as $language )
		{
			$html .= '		<th width="' . $colWidth . '%">{{' . $language[ 'name' ] . '}}</th>' . PHP_EOL;
		}

		$html .= '	</tr>' . PHP_EOL;

		foreach ( $translations as $translation )
		{
			$html .= '	<tr>' . PHP_EOL;
			$html .= '		<td>' . ( isset( $translation[ 0 ] ) ? $translation[ 0 ][ 'translation' ] : '&nbsp;' ) . '</td>' . PHP_EOL;

			foreach ( $languages as $lang_id => $language )
			{
				if( isset( $translation[ $lang_id ] ) )
				{
					$html .= '		<td>' . $translation[ $lang_id ][ 'translation' ] . '&nbsp;</td>' . PHP_EOL;
				}
				else
				{
					$html .= '		<td>&nbsp;</td>' . PHP_EOL;
				}
			}

			$html .= '	</tr>' . PHP_EOL;
		}

		$html .= '</table>' . PHP_EOL;
		return $html;
	}

	public function showEditTranslationsForm( $translations, $languages )
	{
		$html = $this->showDashboardMenu();

		$colWidth = 100 / ( count( $languages ) + 1 );

		$html = '<br /><p>' . PHP_EOL;
		if( isset( $_SERVER[ 'HTTP_REFERER' ] ) )
		{
			$html .= '<a href="' . $_SERVER[ 'HTTP_REFERER' ] . '" title="{{Go back}}">&laquo; {{Go back}}</a>' . PHP_EOL;
		}
		else
		{
			$html .= '<a href="' . HTTP_ROOT . 'index.php?module=translation&action=show" title="{{Back to overview}}">&laquo; {{Back to overview}}</a>' . PHP_EOL;
		}

		$html .= '</p><h1>{{Edit translations}}</h1>' . PHP_EOL;
		$html .= '<form action="?module=translation&action=save" method="POST">' . PHP_EOL;
		$html .= '	<table cellspacing="0" cellpadding="0">' . PHP_EOL;
		$html .= '		<tr>' . PHP_EOL;
		$html .= '			<th width="' . $colWidth . '%">{{English}}<br />({{Initial language}})</th>' . PHP_EOL;

		foreach( $languages as $language )
		{
			$html .= '			<th width="' . $colWidth . '%">{{' . $language[ 'name' ] . '}}<br />(<a href="javascript:goTranslate( \'.vertaling_' . $language[ 'google_code' ] . '\', \'' . $language[ 'google_code' ] . '\' );"><img src="' . HTTP_ROOT . 'images/icons/google.png"> {{Google translation}}</a>)</th>' . PHP_EOL;
		}
		$html .= '		</tr>' . PHP_EOL;

		foreach( $translations as $parentId => $translation )
		{
			$html .= '		<tr>' . PHP_EOL;
			$html .= '			<td class="engelse_vertaling">' . $translation[ 0 ][ 'translation' ] . '</td>' . PHP_EOL;
			foreach ( $languages as $langId => $language )
			{
				$html .= '			<td>';

				if( !empty( $translation[ $langId ][ 'translation' ] ) )
				{
					$html .= '				<input type="text" name="' . $translation[ $langId ][ 'id' ] . '" value="' . $translation[ $langId ][ 'translation' ] . '" />';
				}
				else
				{
					$html .= '				<input type="text" name="' . $parentId . ',' . $langId . '" value="" class="vertaling_' . $language[ 'google_code' ] . '" />';
				}

				$html .= '</td>' . PHP_EOL;
			}

			$html .= '		</tr>' . PHP_EOL;
		}

		$html .= '		<tr>' . PHP_EOL;
		$html .= '			<td colspan="' . ( count( $languages ) + 1 ) . '"><input type="submit" value="{{Save translations}}" /></td>' . PHP_EOL;
		$html .= '		</tr>' . PHP_EOL;
		$html .= '	</table>' . PHP_EOL;
		$html .= '</form>' . PHP_EOL;

		$html .= '	<script type="text/javascript" src="http://www.google.com/jsapi"></script>' . PHP_EOL;
		$html .= '	<script type="text/javascript">' . PHP_EOL;
		$html .= '	google.load( "language", "1" );' . PHP_EOL;
		$html .= '	function goTranslate( languageField, shortCode ) {' . PHP_EOL;
		$html .= '		$( languageField ).each( function() {' . PHP_EOL;
		$html .= '			var $target = $( this );' . PHP_EOL;
		$html .= '			var foundSource = false;' . PHP_EOL;
		$html .= '			if( englishSource = $( this ).parent().prevUntil( ".engelse_vertaling" ).prev().text() ) {' . PHP_EOL;
		$html .= '				foundSource = true;' . PHP_EOL;
		$html .= '			}' . PHP_EOL;
		$html .= '			else {' . PHP_EOL;
		$html .= '				if( englishSource = $( this ).parent().prev().text() ) {' . PHP_EOL;
		$html .= '					foundSource = true;' . PHP_EOL;
		$html .= '				}' . PHP_EOL;
		$html .= '			}' . PHP_EOL;
		$html .= '			if( foundSource ) {' . PHP_EOL;
		$html .= '				google.language.translate( englishSource, "en", shortCode, function( result ) {' . PHP_EOL;
		$html .= '					if( result.translation && englishSource != result.translation ) {' . PHP_EOL;
		$html .= '						$target.val( result.translation ).addClass( "vertaling" );' . PHP_EOL;
		$html .= '					}' . PHP_EOL;
		$html .= '				} );' . PHP_EOL;
		$html .= '			}' . PHP_EOL;
		$html .= '		} );' . PHP_EOL;
		$html .= '	}' . PHP_EOL;
		$html .= '	</script>' . PHP_EOL;

		return $html;
	}

	public function showActionResult( $result = TRUE )
	{
		$html = $this->showDashboardMenu();

		if( $result === TRUE )
		{
			return '<h1>{{Successful}}</h1> <p>{{Translations saved}}.</p><br /><br /><p><a href="' . HTTP_ROOT . 'index.php?module=translation&action=view">{{Click here to return to the overview}}</a></p>' . PHP_EOL;
		}
		else
		{
			return '<h1>{{Error}}</h1> <p>{{An error occurred}}.</p><br /><br /><a href="' . HTTP_ROOT . 'index.php?module=translation&action=view">{{Click here to return to the overview}}</a>' . PHP_EOL;
		}
	}
}