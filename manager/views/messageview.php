<?php

	class MessageView extends View
	{
		public function home ( $keywords = array(), $locations = array() )
		{
			$html = $this->leftMenu ( );
			$html .= '<form method="POST" id="postForm">';
			$html .= '	<div id="center-column">
						<div style="float:right;">
							<select onchange="document.getElementById(\'postForm\').submit();" id="location" name="location">';
			$html .= '<option value="0">Alle vestigingen</option>';
							
			foreach( $locations as $locationId => $location )
			{
				$html .= '<option value="' . $locationId . '"';
				
				if( isset( $_POST[ 'location' ] ) && $_POST[ 'location' ] == $locationId )
				{
					$html .= ' SELECTED';
				}
				
				$html .= '>' . $location[ 'name' ] . '</option>';
			}
								
			$html .= '		</select>
						</div>
			
        				<div class="top-bar" style="float: left; width: 200px;">
                			<h1>Berichten</h1>
            			</div>';
						
			$html .= '<div style="clear: both;" /><br />';

			$html .= '<table class="listing" cellpadding="0" cellspacing="0">';
			$html .= '	<tr>';
			$html .= '  	<th>Sleutelwoord</th>';
			$html .= '  	<th>Vestiging</th>';
			$html .= '  	<th>Opmerking</th>';
			$html .= '  	<th>Meesturen</th>';
			$html .= '      <th>Berichten</th>';
			$html .= '      <th></th>';
			$html .= '	</tr>';

			foreach ( $keywords as $keywordId => $keyword )
			{
				$html .= '<tr>';
				$html .= '	<td>' . $keyword[ 'keyword' ] . '</td>';
				$html .= '	<td>';
				
				foreach( $keyword[ 'locations' ] as $location )
				{
					$html .= $location[ 'name' ] . '<br />';
				}
				
				$html .= '	</td>';
				$html .= '	<td>' . $keyword[ 'remark' ] . '</td>';
				$html .= '	<td>';
				
				if($keyword[ 'include' ] == 0)
				{
					$html .= 'Nee';
				}
				else
				{
					$html .= 'Ja';				
				}
				
				$html .= '</td>';
				$html .= '	<td>' . $keyword[ 'count' ] . '</td>';
				$html .= '	<td>
								<a title="Aanpassen" href="' . HTTP_ROOT . '?module=message&action=editKeyword&keywordId=' .$keywordId . '"><img src="' . HTTP_ROOT . '/images/wrench_orange.png" /></a> 
								<a title="Verwijderen" href="' . HTTP_ROOT . '?module=message&action=home&deleteKeyword=' .$keywordId . '"><img src="' . HTTP_ROOT . '/images/cancel.png" /></a>
							</td>';
				$html .= '</tr>';
			}
			
			$html .= '</table>';
			
			if( count($keywords) == 0 )
			{
				$html .= '<br /><b>Er zijn geen zoekwoorden gevonden.</b>';
			}

			$html .= '</div>';
			$html .= '</form>';

			return $html;
		}

		public function unknown ( $unboundMessages = array() )
		{
			$html = $this->leftMenu ( );
			$html .= '	<div id="center-column">
        				<div class="top-bar">
                			<h1>Ongekoppelde berichten</h1>
            			</div>';

			$html .= '<table class="listing" cellpadding="0" cellspacing="0">';
			$html .= '	<tr>';
			$html .= '  	<th style="width: 400px;">Bericht</th>';
			$html .= '      <th></th>';
			$html .= '	</tr>';

			foreach ( $unboundMessages as $messageId => $message )
			{
				$html .= '<tr>';
				$html .= '	<td>' . $message[ 'msg' ] . '</td>';
				$html .= '	<td>';
				
				$html .= '<a href="' . HTTP_ROOT . '?module=message&action=newKeyword&messageId=' . $messageId . '"><img src="images/icons16x16/database_connect.png" alt="Koppelen" title="Koppelen" /></a>&nbsp;&nbsp;';
				
				$html .= '<a href="' . HTTP_ROOT . '?module=message&action=unknown&deleteMessageId=' . $messageId . '"><img src="images/icons16x16/database_delete.png" alt="Verwijderen" title="Verwijderen" /></a>';
				
				$html .= '	</td>';
				$html .= '</tr>';
			}

			$html .= '</table>';

			$html .= '</div>';
			return $html;
		}
		
		public function editKeyword ( $keyword = '', $locations = array() )
		{		
			$html = $this->leftMenu ( );
			$html .= '	<div id="center-column">
        				<div class="top-bar">
                			<h1>Nieuw sleutelwoord</h1>
            			</div>';

			$html .= '<form method="POST">';
			$html .= '<b>Sleutelwoord:</b><br />';
			$html .= $keyword[ 'keyword' ] . '<br /><br />';
			
			$html .= '<b>Opmerking:</b><br />';
			$html .= '<input type="text" name="remark" id="remark" value="' . $keyword[ 'remark' ] . '" /><br /><br />';
						
			$html .= '<b>Opmerking opnemen in doorgestuurd bericht:</b><br />';
			
			$html .= '<select id="include" name="include">';
			
			if($keyword['include'] == 1)
				$html .= '<option value="1" selected>Wel meesturen</option>';
			else
				$html .= '<option value="1">Wel meesturen</option>';
				
			if($keyword['include'] == 0)
				$html .= '<option value="0" selected>Niet meesturen</option>';
			else
				$html .= '<option value="0">Niet meesturen</option>';
					
			$html .= '</select><br /><br />';

			$html .= '<b>Vestiging:</b><br />';
			
			$locationIds = array_keys( $keyword[ 'locations' ] );
			foreach( $locations as $locationId => $location )
			{
				$html .= ' <input type="checkbox"';

				if( in_array( $locationId, $locationIds ) )
				{
					$html .= 'checked="yes" ';
				}
				
				$html .=' name="locations[]" value="' . $locationId . '"  /> ' . $location[ 'name' ] . '<br />';
			}
			
			
			$html .= '<br /><br />';
			$html .= '<input type="submit" value="Opslaan" />';

			$html .= '</form>';
			$html .= '</div>';

			return $html;
		}

		public function newKeyword ( $message = '', $locations = array() )
		{
			$html = $this->leftMenu ( );
			$html .= '	<div id="center-column">
        				<div class="top-bar">
                			<h1>Nieuw sleutelwoord</h1>
            			</div>';

			$html .= '<form method="POST">';
			$html .= '<b>Bericht:</b><br />';
			$html .= '<pre cols="20" WRAP>' . $message . '</pre><br /><br />';

			$html .= '<b>Sleutelwoord:</b><br />';
			$html .= '<input type="text" id="keyword" name="keyword" /><br /><br />';
			
				$html .= '<b>Opmerking:</b><br />';
			$html .= '<input type="text" name="remark" id="remark" value="" /><br /><br />';
						
			$html .= '<b>Opmerking opnemen in doorgestuurd bericht:</b><br />';
			
			$html .= '<select id="include" name="include">';
			$html .= '<option value="0" selected>Niet meesturen</option>';
			$html .= '<option value="1">Wel meesturen</option>';
			$html .= '</select><br /><br />';
			
			$html .= '<b>Vestiging:</b><br />';
			
			foreach( $locations as $locationId => $location )
			{
				$html .= ' <input type="checkbox" name="locations[]" value="' . $locationId . '"  /> ' . $location[ 'name' ] . '<br />';
			}
			
			
			$html .= '<br /><br />';
			$html .= '<input type="submit" value="Opslaan" />';

			$html .= '</form>';
			$html .= '</div>';

			return $html;
		}

		private function leftMenu ( )
		{
			$html = '<div id="left-column">
		                <h3>Paginas</h3>
		                <ul class="nav">
							<li class="' . ((IsVar::set ( $_GET, 'action' ) == 'home' || !isset ( $_GET[ 'action' ] )) ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=message&action=home">Overzicht</a></li>
							<li class="' . (IsVar::set ( $_GET, 'action' ) == 'unknown' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=message&action=unknown">Ongekoppeld</a></li>
						</ul>
					</div>';

			return $html;
		}

	}
?>