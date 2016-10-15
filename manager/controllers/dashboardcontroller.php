<?php
class DashboardController extends Controller
{
	private $stoneController;
		
	public function __construct ( )
	{
		parent::__construct ( );
		$this->stoneController = StoneController::getClass ( 'StoneMessage' );
	}
		
	public function home()
	{
		$received = $this->stoneController->getLatestMessages( 'received', 10 );
		$send = $this->stoneController->getLatestMessagesWithContact( 'send', 10 );
	
		return $this->view->home( $received, $send );
	}
}
	?>