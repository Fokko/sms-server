<?php

class TranslationController extends Controller {
	private $translations, $languages;

	public function __construct()
	{
		parent::__construct();

		$this->translations	= array();
		$this->languages	= array();
	}

	public function view()
	{
		$translations = $this->model->getAllTranslations();
		$languages = $this->model->getAllLanguages();
		unset( $languages[ 0 ] );

		return $this->view->showTranslations( $translations, $languages );
	}

	public function edit()
	{
		$translations = $this->model->getAllTranslations();
		$languages = $this->model->getAllLanguages();
		unset( $languages[ 0 ] );

		return $this->view->showEditTranslationsForm( $translations, $languages );
	}

	public function save()
	{
		$result = $this->model->updateTranslations( $_POST );
		return $this->view->showActionResult( $result );
	}

	public function doTranslate( $html = '' )
	{
		if( StoneUserController::getUserLanguage() == 0 OR USE_MULTILANGUAGE === FALSE )
		{
			$html = preg_replace( '/{{([^(}})]*)}}/', '$1', $html );
			return $html;
		}

		$model = new TranslationModel();
		return $model->translateHTML( $html );
	}

	public static function getLanguages()
	{
		$model = new TranslationModel();
		return $model->getAllLanguages();
	}
}
?>