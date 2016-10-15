<?php
class InformationView extends View
{
	public function home(  )
	{	
		$html = '<div id="center-column">
					<div class="top-bar">
						<h1>Informatie</h1>';
		$html .= '	</div><br /><br />';
		
		$html .= '<p><b>Dashboard</b><br />';
		$html .= 'Het dashboard is om overzicht te houden tussen de binnengekomen en uitgaande berichten.';
		$html .= '</p>';
		$html .= '<p><b>Telefoonboek</b><br />';
		$html .= 'Het telefoonboek wordt gebruikt om gebruikers te beheren en verstingen aan te maken en te verwijderen.';
		$html .= '</p>';
		$html .= '<p><b>Routering</b><br />';
		$html .= 'Binnen de routering wordt bijgehouden, per vestiging, wie op wat voor moment berichten moet ontvangen.';
		$html .= '</p>';
		$html .= '<p><b>Berichten</b><br />';
		$html .= 'Bij de berichten wordt beheerd welke berichten naar welke vestigingen moeten worden doorgestuurd.';
		$html .= '</p>';
		$html .= '<p><b>Exporteren</b><br />';
		$html .= 'Alle berichten kunnen worden geexporteerd naar Excel.';
		$html .= '</p>';
		$html .= '<p><b>Instellingen</b><br />';
		$html .= 'Onder instellingen worden alle globale instellingen gedaan. Wanneer deze eenmaal goed zijn ingesteld zal dit niet vaak worden gebruikt.';
		$html .= '</p>';
	
		return $html;
	}
}
?>