<?php
class ExportController extends Controller
{
	private $stoneController;
		
	public function __construct ( )
	{
		parent::__construct ( );
		$this->stoneController = StoneController::getClass ( 'StoneMessage' );
	}
	
	public function recieved()
	{
		$from = strtotime($_POST['van']);
		$till = strtotime($_POST['tot']);
		$this->gatherData( 'received', $from, $till );	
	}
	
	public function send()
	{
		$from = strtotime($_POST['van']);
		$till = strtotime($_POST['tot']);
		$this->gatherData( 'send', $from, $till );
	}
	
	private function gatherData( $type = 'send', $from, $till )
	{	
		$output = '<table>';
				
		$data = $this->stoneController->getLatestMessages( $type, PHP_INT_MAX, $from, $till );
		
		foreach( $data as $row )
		{
			$keys = array_keys( $row );
			
			$output .= '<tr>';
			
			foreach( $keys as $key )
			{
				$output .= '<td>' . $row[ $key ] . '</td>';
			}
			
			$output .= '</tr>';
		}

		$output .= '</table>';
		
		$filename =  'messages_' . $type . "_" . date('Ymd', $from) . "_" . date('Ymd', $till) . ".xls";

		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		die( $output );
	}
		
	public function home()
	{	
	
		return $this->view->home();
	}
}
?>