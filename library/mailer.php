<?php

	/**
	 * Extention of PHPMailer
	 */
	class Mailer
	{
		public $PHPMailer;
		public $translations;
		public $mailTemplate;
		public $mailSubject;

		private $search;
		private $replace;

		public function __construct ( $mailTemplate = '', $mailSubject = '' )
		{
			$this->translations = new TranslationController ( );
			$this->PHPMailer = new PHPMailer ( );
			$this->PHPMailer->IsHTML ( true );
			$this->PHPMailer->IsMail ( true );

			$this->mailTemplate = $mailTemplate;
			$this->mailSubject = $mailSubject;
			$this->search = array ( );
			$this->replace = array ( );
		}

		public function addReplacement ( $search, $replace )
		{
			array_push ( $this->search, $search );
			array_push ( $this->replace, $replace );
		}

		public function email ( $from = '', $fromName = '' )
		{
			$from = $from == '' ? SYSTEM_MAIL : $from;
			$fromName = $fromName == '' ? SYSTEM_NAME : $fromName;

			if ( file_exists ( SYSTEM_MAIL_DIR . $this->mailTemplate ) === FALSE )
			{
				throw new MailException ( 'Template "' . SYSTEM_MAIL_DIR . $this->mailTemplate . '" does not exists' );
			}

			$email = file_get_contents ( SYSTEM_MAIL_DIR . $this->mailTemplate );
			$email = str_replace ( $this->search, $this->replace, $email );
			$email = nl2br ( $email );

			$this->PHPMailer->From = $from;
			$this->PHPMailer->FromName = $fromName;
			$this->PHPMailer->Subject = $this->translations->doTranslate ( $this->mailSubject );
			$this->PHPMailer->AltBody = $email;
			$this->PHPMailer->Body = $this->translations->doTranslate ( $this->mergeWithTemplate ( $email ) );

			return $this->PHPMailer->Send ( );
		}

		public function addAttachment ( $path, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
		{
			$this->PHPMailer->AddAttachment ( $path, $name, $encoding, $type );
		}

		private function mergeWithTemplate ( $content )
		{
			if ( file_exists ( SYSTEM_MAIL_DIR . 'HTML_TEMPLATE.txt' ) === FALSE )
			{
				throw new MailException ( 'Template "' . SYSTEM_MAIL_DIR . 'HTML_TEMPLATE.txt' . '" does not exists' );
			}

			$template = file_get_contents ( SYSTEM_MAIL_DIR . 'HTML_TEMPLATE.txt' );
			return str_replace ( '<CONTENT>', $content, $template );
		}

		public function AddAddress ( $address, $name = '' )
		{
			if ( DEBUGMODUS )
			{
				$this->PHPMailer->AddAddress ( SYSTEM_MAIL, $name );
				return;
			}

			$this->PHPMailer->AddAddress ( $address, $name );
		}

		public function AddCC ( $address, $name = '' )
		{
			if ( DEBUGMODUS )
			{
				$this->PHPMailer->AddCC ( SYSTEM_MAIL, $name );
				return;
			}

			$this->PHPMailer->AddCC ( $address, $name );
		}

		public function AddBCC ( $address, $name = '' )
		{
			if ( DEBUGMODUS )
			{
				$this->PHPMailer->AddBCC ( SYSTEM_MAIL, $name );
				return;
			}

			$this->PHPMailer->AddBCC ( $address, $name );
		}

	}

	class MailException extends CustomException
	{
		public function __construct ( $message = NULL, $code = 0 )
		{
			parent::__construct ( $message, $code );
		}

		public static function printException ( $e = NULL, $code = 0 )
		{
			echo '<pre>Caught exception ("' . $e->getMessage ( ) . '")' . "\n" . $e . '</pre>';
		}

	}
