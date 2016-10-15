<?php
class ExportView extends View
{
	public function home(  )
	{
		$html = $this->leftMenu();	
	
		$html .= '<script type="text/javascript" src="' .HTTP_ROOT . 'js/jquery.min.js"></script>';
		$html .= '<script type="text/javascript" src="' .HTTP_ROOT . 'js/jquery-ui-1.8.13.custom.min.js"></script>';
		$html .= '<script type="text/javascript" src="' .HTTP_ROOT . 'js/jquery.ui.datepicker-nl.js"></script>';
		$html .= '<link  href="'. HTTP_ROOT . 'css/cupertino/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css" />';
		$html .= '
			<script type="text/javascript">
				$(function() {
					$(\'.datum\').datepicker();
				});
			</script>';
	
		$html .= '<div id="center-column">
					<div class="top-bar">
						<h1>Gegevens exporteren</h1>';
		$html .= '	</div><br /><br />';
		$html .= '<form method="POST" action="?module=export&action=recieved">';
		$html .= '<h3>Binnengekomen berichten</h3>';
		
		$html .= 'Vanaf: <input type="text" name="van" class="datum" value="' . date('d-m-Y', strtotime('-2 month')) . '"></input><br />';
		$html .= 'Tot: <input type="text" name="tot" class="datum" value="' . date('d-m-Y', strtotime('+1 day')) . '"></input><br /><br />';
		
		$html .= '	<input type="submit" value="Download Excel"></input><br />';
		
		$html .= '</form>';
		
		$html .= '<form method="POST" action="?module=export&action=send">';
		$html .= '<h3>Binnengekomen berichten</h3>';
		
		$html .= 'Vanaf: <input name="van" type="text" class="datum" value="' . date('d-m-Y', strtotime('-2 month')) . '"></input><br />';
		$html .= 'Tot: <input name="tot" type="text" class="datum" value="' . date('d-m-Y', strtotime('+1 day')) . '"></input><br /><br />';
		
		$html .= '	<input type="submit" value="Download Excel"></input><br />';
		
		$html .= '</form>';
		
		$html .= '</div>';
	
		return $html;
	}

	private function leftMenu ( )
	{
		$html = '<div id="left-column">
					<h3>Pagina\'s</h3>
					<ul class="nav">';

		$html .= '<li class="active"><a href="' . HTTP_ROOT . '?module=config&action=home">Excel</a></li>';

		$html .= '</ul></div>';

		return $html;
	}
}
?>