<?php
	class MessageController extends Controller
	{
		private $stoneController;

		public function __construct ( )
		{
			parent::__construct ( );
			$this->stoneController = StoneController::getClass ( 'StoneMessage' );
		}
		
		public function home ( )
		{
			if ( isset ( $_GET[ 'deleteKeyword' ] ) )
			{
				$this->stoneController->deleteKeyword ( $_GET[ 'deleteKeyword' ] );
			}
			$phonebook = StoneController::getClass ( 'StonePhonebook' );
			$locations = $phonebook->getAllLocations ( );

			$locationId = 0;
			if( isset( $_POST[ 'location' ] ) )
			{
				$locationId = (int)$_POST[ 'location' ];
			}
			
			$keywords = $this->stoneController->getKeywords ( $locationId );

			return $this->view->home ( $keywords, $locations );
		}

		public function unknown ( )
		{
			if( isset( $_GET[ 'deleteMessageId' ] ) && is_numeric( $_GET[ 'deleteMessageId' ] ) )
			{
				$this->stoneController->deleteMessage( $_GET[ 'deleteMessageId' ] );
			}
		
			$unboundMessages = $this->stoneController->getUnboundMessages ( );
			return $this->view->unknown ( $unboundMessages );
		}
		
		public function editKeyword() 
		{
			if( isset( $_POST ) && count( $_POST ) > 0 )
			{
				$keyword = $this->stoneController->updateKeyword ( (int)$_GET[ 'keywordId'], $_POST  );

				header ( 'location: ' . HTTP_ROOT . '?module=message&action=home' );
			}
			else
			{
				$phonebook = StoneController::getClass ( 'StonePhonebook' );
				$locations = $phonebook->getAllLocations ( );
				
				$keyword = $this->stoneController->getKeyword ( (int)$_GET[ 'keywordId']  );
			
				return $this->view->editKeyword( $keyword, $locations );			
			}		
		}

		public function newKeyword ( )
		{
			if ( count ( $_POST ) > 0 )
			{
				$message = $this->stoneController->createKeyword ( $_POST[ 'keyword' ],$_POST[ 'remark' ],$_POST[ 'include' ], $_POST[ 'locations' ] );
				header ( 'location: ' . HTTP_ROOT . '?module=message&action=home' );
			}
			else
			{
				$phonebook = StoneController::getClass ( 'StonePhonebook' );
				$locations = $phonebook->getAllLocations ( );

				$message = $this->stoneController->getInboundMessage ( (int)$_GET[ 'messageId' ] );
				return $this->view->newKeyword ( $message[ 'msg' ], $locations );
			}

		}

	}
?>