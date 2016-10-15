<?php

	class StoneSettingController extends StoneController
	{
		public function getSettings()
		{
			return $this->model->getSettings();			
		}
		
		public function setSettings( $settings = array() )
		{
			return $this->model->setSettings( $settings );	
		}
	}
?>