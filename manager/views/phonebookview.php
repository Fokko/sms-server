<?php
	class PhonebookView extends View
	{
		public function home ( $persons, $locations = array() )
		{
			$html = $this->leftMenu ( );
			$html .= '	<div id="center-column">
            				<div class="top-bar">
                    			<h1>Telefoonboek</h1>
                			</div>
            				<br />                			
							<table class="listing" cellpadding="0" cellspacing="0">
		                        <tr>
		                            <th>Naam</th>
		                            <th>Nummer</th>
		                            <th>Vestiging</th>
		                            <th></th>
		                        </tr>';
					
					
			foreach( $persons as $personId => $person )
			{							
				$html .=	'<tr>
			                	<td class="style1">' . $person['name'] . '</td>
			                	<td class="style1">' . $person['number'] . '</td>
			                	<td class="style1">' . $person['location'] . '</td>
								<td><a href="' . HTTP_ROOT . "?module=phonebook&action=hom&verwijderGebruiker=" . $person['id'] . '"><img src="img/hr.gif" width="16" height="16" alt="" /></td>
	                        </tr>';			
			}			
				                        
		    $html .= '</table><br /><br />';
							
							
							
            $html .= '		<div class="top-bar">
								<h2>Persoon toevoegen</h2>
                			</div>
                			<form method="POST">
                				<b>Naam:</b><br />
                				<input type="text" name="person_name" id="person_name"></input>
                				<br />
                				<br />
                				<b>Nummer:</b><br />
                				<input type="text" name="person_number" id="person_number"></input>
                				<br />
                				<br />                				
                				<b>Vestiging:</b><br />
								<select id="location_id" name="location_id">';
								
			foreach( $locations as $locationId => $location )
			{						
				$html .= '<option value="' . $locationId . '">' . $location[ 'name' ] . '</option>';
			}
			
			$html .= '			</select>
                				<br />
                				<br />
                				<input value="Opslaan" type="submit"></input>                				
                			</form>
            			</div>';

			return $html;
		}

		public function location ( $locations = array() )
		{
			$html = $this->leftMenu ( );
			$html .= '	<div id="center-column">
            				<div class="top-bar">
                    			<h1>Vestigingen</h1>
                			</div>
							<script type="text/javascript">
								function confirmDelete( name, id )
								{  
									if( confirm("Weet je zeker dat je de locatie " + name + " wilt verwijderen?") )
									{
										window.location = "' . HTTP_ROOT . '?module=phonebook&action=location&deleteLocation=" + id;
									}								
								}
							</script>
                			<br />                			
							<table class="listing" cellpadding="0" cellspacing="0">
		                        <tr>
		                            <th style="width: 80%;">Locatie</th>
		                            <th></th>
		                        </tr>';
					
					
			foreach( $locations as $locationId => $location )
			{							
				$html .='	<tr>
			                	<td class="style1">' . $location['name'] . '</td>
								<td><a href="#" onclick="confirmDelete( \'' . $location['name'] . '\', ' . $location[ 'id' ] . ');"><img src="img/hr.gif" width="16" height="16" alt="" /></a></td>
	                        </tr>';			
			}			
				                        
		    $html .= '</table><br /><br />';
							
							
							
            $html .= '		<div class="top-bar">
								<h2>Vestigingen aanmaken</h2>
                			</div>
                			<form method="POST">
                				<b>Naam:</b><br />
                				<input type="text" name="location_name" id="location_name"></input>
                				<br />
                				<br />
                				<input value="Opslaan" type="submit"></input>                				
                			</form>
            			</div>';

			return $html;
		}

		private function leftMenu ( )
		{
			$html = '<div id="left-column">
		                <h3>Telefoonboek</h3>
		                <ul class="nav">
		                    <li class="' . ((IsVar::set ( $_GET, 'action' ) == 'home' || !isset ( $_GET[ 'action' ] )) ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=phonebook&action=home">Telefoonboek</a></li>
		                    <li class="' . (IsVar::set ( $_GET, 'action' ) == 'location' ? 'active' : '') . '"><a href="' . HTTP_ROOT . '?module=phonebook&action=location">Vestigingen</a></li>
		                </ul>
		            </div>';

			return $html;
		}

	}
?>