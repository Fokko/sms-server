<?php

	class StoneMessageController extends StoneController
	{

		public function getLatestMessages ( $type = 'send', $count = PHP_INT_MAX, $from = null, $till = null )
		{
			return $this->model->getLatestMessages ( $type, $count, $from, $till );
		}
		
		public function getLatestMessagesWithContact( $type = 'send', $count = PHP_INT_MAX )
		{
			return $this->model->getLatestMessagesWithContact ( $type, $count );
		}
		

		public function getRouting ( $location = -1, $day = -1 )
		{
			return $this->model->getRouting ( $location, $day );
		}

		public function createRouting ( $location = 0, $day = 0, $person = 0, $moment = 'always' )
		{
			return $this->model->createRouting ( $location, $day, $person, $moment );
		}

		public function deleteRouting( $recordId = 0 )
		{
			return $this->model->deleteRouting( $recordId );			
		}
		
		public function deleteMessage( $messageId = 0 )
		{
			return $this->model->deleteMessage( $messageId );
		}

		public function getKeywords ( $locationId = 0 )
		{
			return $this->model->getKeywords ( $locationId );
		}

		public function getInboundMessage ( $messageId = 0 )
		{
			return $this->model->getInboundMessage ( $messageId );
		}

		public function createKeyword ( $keyword = '', $remark = '', $include = 0, $locations = array() )
		{
			return $this->model->createKeyword ( $keyword, $remark, $include, $locations );
		}
		
		public function getKeyword( $keywordId = 0 )
		{
			return $this->model->getKeyword( $keywordId );
		}
			
		public function updateKeyword( $keywordId, $keyword = array() )
		{
			return $this->model->updateKeyword( $keywordId, $keyword );			
		}		
			
		public function deleteKeyword ( $keywordId = 0 )
		{
			return $this->model->deleteKeyword ( $keywordId );
		}

		public function getUnboundMessages ( )
		{
			return $this->model->getUnboundMessages ( );
		}

	}
?>