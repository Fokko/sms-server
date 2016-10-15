<?php
	class InformationController extends Controller
	{
		private $stoneController;

		public function __construct ( )
		{
			parent::__construct ( );
		}
		
		public function home ( )
		{
			return $this->view->home();
		}

	}
?>