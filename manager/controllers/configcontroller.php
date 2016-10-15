<?php
	class ConfigController extends Controller
	{
		private $stoneController;

		public function __construct ( )
		{
			parent::__construct ( );
			$this->stoneController = StoneController::getClass ( 'StoneSetting' );
		}

		public function home ( )
		{
			if ( isset ( $_POST ) && count ( $_POST ) > 0 )
			{
				$this->stoneController->setSettings ( $_POST['config'] );
			}

			$settings = $this->stoneController->getSettings ( );
			return $this->view->home ( $settings );
		}

	}
?>