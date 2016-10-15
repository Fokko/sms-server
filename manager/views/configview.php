<?php
	class ConfigView extends View
	{
		public function home ( $settings = array() )
		{
			$html = $this->leftMenu ( );
			$html .= '	<div id="center-column">
        				<div class="top-bar">
                			<h1>Instellingen</h1>';
			$html .= '</div>';
			
			$html .= '<form method="POST">';
			
			$html .= '<table>';
			foreach ( $settings as $key => $value )
			{
				$html .= '<tr><td>' . $key . '</td><td><input type="text" id="config[' . $key . ']" name="config[' . $key . ']" value="' . $value . '" /></td></tr>';
			}
			
			$html .= '</table>';

			$html .= '<input type="submit" value="Instellingen opslaan!" />';
			$html .= '</form>';
			$html .= '</div>';
			
			return $html;
		}

		private function leftMenu ( )
		{
			$html = '<div id="left-column">
		                <h3>Pagina\'s</h3>
		                <ul class="nav">';

			$html .= '<li class="active"><a href="' . HTTP_ROOT . '?module=config&action=home">Instellingen</a></li>';

			$html .= '</ul></div>';

			return $html;
		}

	}
?>