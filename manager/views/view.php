<?php

	class View
	{
		private static $instance;
		public static function init ( )
		{
			if ( isset ( self::$instance ) === FALSE )
			{
				$c = get_called_class ( );
				self::$instance = new $c;
			}

			return self::$instance;
		}

		public function __call ( $name, $args )
		{
			throw new ManagerException ( 'Undefined ' . __CLASS__ . ' method "' . $name . '()" called' );
			return $this->pageNotFound ( );
		}

		public function pageNotFound ( )
		{
			return '{{Page not found}}.';
		}

		public static function showHtml ( $content = '', $loadTime = 0, $lastLogin = array() )
		{
			$html = '<!DOCTYPE html>
					<html>
					<head>
						<title>Admin</title>
						<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
						<link  href="css/admin.css" rel="stylesheet" type="text/css" />
					</head>
					<body>
						<div id="main">
							<div id="header">
								<a href="' . HTTP_ROOT . '" class="logo"><img src="images/logo.png" alt="" /></a>';
			
			$html .= View::showMenu();
				
			$html .= '	</div>
						<div id="middle">';
					
			$html .= $content;
					
			$html .= '	</div>
						<div id="footer">';
				
			
			$html .= '<p>{{Page loaded in}} ' . $loadTime . ' {{seconds}}' . PHP_EOL;
			if ( count ( $lastLogin ) > 0 )
			{
				$html .= ' | {{Last account activity}}: ' . StoneUserController::ago ( $lastLogin[ 'timestamp' ] ) . ' ' . ($_SERVER[ 'REMOTE_ADDR' ] == $lastLogin[ 'ip_address' ] ? '{{on this computer}}' : '{{from}} ' . $lastLogin[ 'ip_address' ] . ' {{in}} ' . StoneUserController::$countries[ $lastLogin[ 'country_code' ] ] ) . PHP_EOL;
			}
			
			$html .= '	</div>
						</div>
					</body>
					</html>' . PHP_EOL;

			return $html;

		}

		public static function showMenu ( )
		{
			$html = '<ul id="top-navigation">';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=dashboard&action=home" class="' . ((IsVar::set ( $_GET, 'module' ) == 'dashboard' || !isset($_GET['module']) )  == 'dashboard' ? 'active' : '') . '">Dashboard</a></li>';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=phonebook&action=home" class="' . (IsVar::set ( $_GET, 'module' )  == 'phonebook' ? 'active' : '') . '">Telefoonboek</a></li>';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=routing&action=home" class="' . (IsVar::set ( $_GET, 'module' )  == 'routing' ? 'active' : '') . '">Routering</a></li>';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=message&action=home" class="' . (IsVar::set ( $_GET, 'module' )  == 'message' ? 'active' : '') . '">Berichten</a></li>';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=export&action=home" class="' . (IsVar::set ( $_GET, 'module' )  == 'export' ? 'active' : '') . '">Exporteren</a></li>';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=config&action=home" class="' . (IsVar::set ( $_GET, 'module' )  == 'config' ? 'active' : '') . '">Instellingen</a></li>';
			$html .= '	<li><a href="' . HTTP_ROOT . '?module=information&action=home" class="' . (IsVar::set ( $_GET, 'module' )  == 'information' ? 'active' : '') . '">Informatie</a></li>';
			$html .= '</ul>';
		
			return $html;
		}

		public static function showFormDialogLink ( $module, $formUrl, $postUrl, $refreshUrl, $refreshSelector, $title = '', $targetSelector = '#dialog_error', $class = 'button' )
		{
			return '<a href="javascript:void(0);" onclick="showFormDialog( \'' . HTTP_ROOT . 'index.php?module=' . $module . '&ajax=1&' . $formUrl . '\', \'' . HTTP_ROOT . 'index.php?module=' . $module . '&ajax=1&' . $postUrl . '\', \'' . (($refreshUrl == 'refresh') ? $refreshUrl : HTTP_ROOT . 'index.php?module=' . $module . '&ajax=1&' . $refreshUrl) . '\', \'' . $refreshSelector . '\', \'' . htmlentities ( $title ) . '\', \'' . $targetSelector . '\' ); return false;" ' . (strlen ( $class ) > 0 ? 'class="' . $class . '"' : '') . '><span>' . $title . '</span></a>' . PHP_EOL;
		}

		public static function showConfirmDialogLink ( $module, $postUrl, $refreshUrl, $refreshSelector, $title = '{{Are you sure}}?', $text = '{{Are you sure}}?', $class = 'ui-icon ui-icon-closethick' )
		{
			return '<a href="javascript:void(0);" class="' . $class . '" onclick="showConfirmDialog( \'' . HTTP_ROOT . 'index.php?module=' . $module . '&ajax=1&' . $postUrl . '\', \'' . HTTP_ROOT . 'index.php?module=' . $module . '&ajax=1&' . $refreshUrl . '\', \'' . $refreshSelector . '\', \'' . htmlentities ( $title ) . '\', \'' . htmlentities ( $text ) . '\' ); return false;"></a>' . PHP_EOL;
		}

	}
?>