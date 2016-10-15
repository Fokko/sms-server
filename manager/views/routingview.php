<?php
	class RoutingView extends View
	{
		public function home ( $locations = array(), $routing = array(), $phonebook = array() )
		{
			$html = $this->rightMenu ( );
			$html .= $this->leftMenu ( $locations );

			$html .= '	<div id="center-column">
            				<div class="top-bar">
                    			<h1>Telefoonboek</h1>
                			</div>
            				<br />';

			$html .= '	<div id="routingColumn" style="border-right: solid 1px gray; height: 100%;">
							<h3>Binnen werktijd</h3>';
			$html .= $this->buildList ( $routing, 'in' );
			$html .= '</div>';

			$html .= '	<div id="routingColumn" style="border-right: solid 1px gray; height: 100%;">
							<h3>Buiten werktijd</h3>';
			$html .= $this->buildList ( $routing, 'out' );
			$html .= '</div>';

			$html .= '	<div id="routingColumn">
							<h3>Altijd</h3>';
			$html .= $this->buildList ( $routing, 'always' );
			$html .= '</div>';

			$html .= '<div style="clear: both;"></div>';
			$html .= '<h2>Persoon toevoegen</h2>';


			$html .= '<form method="POST" action="' . HTTP_ROOT . '?module=routing&action=home&day=' . $_GET[ 'day' ] . '&location=' . $_GET[ 'location' ] . '">';
			$html .= '<b>Persoon:</b><br />';
			$html .= '<select id="person" name="person">';


			foreach ( $phonebook as $locationName => $location )
			{
				if( count( $location ) > 0 )
				{
					$html .= '<optgroup label="' . $locationName . '">';

					foreach ( $location as $personId => $person )
					{
						if( $person[ 'number' ] != null )
						{
							$html .= '<option value="' . $personId . '">' . $person[ 'name' ] . ' (' . $person[ 'number' ] . ')</option>';
						}
					}

					$html .= '</optgroup>';
				}
			}
			
			$html .= '<optgroup label="Verwijder">';
			$html .= '<option value="NULL">Verwijder</option>';
			$html .= '</optgroup>';

			$html .= '</select>';
			$html .= '<br /><br />';
			
			$html .= '<b>Moment:</b><br />';
			
			$html .= '<select id="moment" name="moment">';
			$html .= '	<option value="in">Binnen kantooruren</option>';
			$html .= '	<option value="out">Buiten kantooruren</option>';
			$html .= '	<option value="always">Altijd</option>';			
			$html .= '</select><br /><br />';
			
			$html .= '<input type="submit" value="Opslaan!"></input>';
			
			$html .= '</form>';
			$html .= '</div>';

			$html .= '';

			return $html;
		}

		private function buildList ( $routing = array(), $moment = 'always' )
		{
			$html = '';

			foreach ( $routing as $routeId => $route )
			{
				if ( $moment == $route[ 'moment' ] )
				{
					if( $route[ 'name' ] == '' )
					{
						$route[ 'name' ] = '<b>Verwijderen</b>';
					}
				
					$html .= '<div>';
					$html .= '<a href="' . HTTP_ROOT . '?module=routing&action=home&day=' . $_GET[ 'day' ] . '&location=' . $_GET[ 'location' ] . '&deleteRoutingRecord=' . $routeId . '"><img src="' . HTTP_ROOT . 'images/cancel.png"></img></a>';
					$html .= $route[ 'name' ] . '<br />';
					$html .= '<sub>' . $route[ 'number' ] . '</sub>';
					$html .= '</div>';
				}

			}

			return $html;
		}

		private function rightMenu ( )
		{
			$html = '<div id="right-column">
		                <h3>Dag v/d week</h3>
		                <ul class="nav">
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '0' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=0&location=' . $_GET[ 'location' ] . '">Maandag</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '1' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=1&location=' . $_GET[ 'location' ] . '">Dinsdag</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '2' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=2&location=' . $_GET[ 'location' ] . '">Woensdag</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '3' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=3&location=' . $_GET[ 'location' ] . '">Donderdag</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '4' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=4&location=' . $_GET[ 'location' ] . '">Vrijdag</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '5' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=5&location=' . $_GET[ 'location' ] . '">Zaterdag</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'day' ) == '6' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=6&location=' . $_GET[ 'location' ] . '">Zondag</a></li>
		                </ul>
		            </div>';

			return $html;
		}

		private function leftMenu ( $locations = array() )
		{
			$html = '<div id="left-column">
		                <h3>Vestiging</h3>
		                <ul class="nav">';

			foreach ( $locations as $locationId => $location )
			{
				$html .= '<li class="' . (IsVar::set ( $_GET, 'location' ) == $locationId ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=routing&action=home&day=0&location=' . $locationId . '">' . $location[ 'name' ] . '</a></li>';
			}

			$html .= '</ul></div>';

			return $html;
		}

	}
?>