<?php

class StoneUserController extends StoneController
{
	protected static $salt = 'nIR5GQq0m83fWZ3KC3XxHmxaoQrg6j';

	public function __construct()
	{
		parent::__construct();

		$this->model = new StoneUserModel();
	}

	public function getModel()
	{
		return $this->model;
	}

	public static function getCurrentUserId()
	{
		return IsVar::set( $_SESSION, 'id', 0, TRUE );
	}

	public function saveUser( $values )
	{
		if( filter_var( $values[ 'email' ], FILTER_VALIDATE_EMAIL ) )
		{
			$userId = IsVar::set( $values, 'id', 0 );
			if( ( $userId == 0 AND count( $this->model->getUserByEmail( $values[ 'email' ] ) ) == 0 ) OR $userId > 0 )
			{
				if( IsVar::set( $values, 'parent_id', 0 ) == 0 )
				{
					$currentUserId = self::getCurrentUserId();
					if( $currentUserId != $userId )
					{
						$values[ 'parent_id' ] = $currentUserId;
					}
					else
					{
						$values[ 'parent_id' ] = NULL;
					}
				}

				$userId = $this->model->saveUser( $userId, $values );

				if( empty( $values[ 'password' ] ) === FALSE )
				{
					$this->model->saveUserPassword( $userId, self::generateSaltCode( $values[ 'password' ] ) );
				}
	
				return $userId;
			}
		}

		return false;
	}

	public static function isLoggedIn()
	{
		$_this = StoneController::getClass( 'StoneUser' );

		if( isset( $_SESSION[ 'user' ][ 'id' ] ) AND isset( $_SESSION[ 'user' ][ 'hash' ] ) )
		{
			return $_this->loginAttempt( $_SESSION[ 'user' ][ 'id' ], $_SESSION[ 'user' ][ 'hash' ] );
		}
		elseif( isset( $_COOKIE[ 'user' ][ 'id' ] ) AND isset( $_COOKIE[ 'user' ][ 'hash' ] ) )
		{
			return $_this->loginAttempt( $_COOKIE[ 'user' ][ 'id' ], $_COOKIE[ 'user' ][ 'hash' ] );
		}

		return FALSE;
	}

	public function logout()
	{
		setcookie( 'user[id]', '', time() - LOGIN_EXPIRE, '/' );
		setcookie( 'user[hash]', '', time() - LOGIN_EXPIRE, '/' );

		if( ini_get( 'session.use_cookies' ) )
		{
			$params = session_get_cookie_params();
			setcookie( session_name(), '', time() - LOGIN_EXPIRE, $params[ 'path' ], $params[ 'domain' ], $params[ 'secure' ], $params[ 'httponly' ] );
		}

		session_destroy();

		return true;
	}

	public function loginAttempt( $mixed, $password )
	{
		$tempPassword = $password;
		if( filter_var( $mixed, FILTER_VALIDATE_EMAIL ) )
		{
			$user = $this->model->getUserByEmail( $mixed );
			$tempPassword = self::generateSaltCode( $password );
		}
		elseif( is_numeric( $mixed ) )
		{
			$user = $this->model->getUserById( $mixed );
		}
		else
		{
			return FALSE;
		}

		if( count( $user ) > 0 )
		{
			if( isset( $_POST[ 'login' ] ) )
			{
				$this->model->saveLoginLog( $user[ 'id' ], $_SERVER[ 'REMOTE_ADDR' ], self::getCountryAndCityFromIPAddress( $_SERVER[ 'REMOTE_ADDR' ] ) );
			}

			if( isset( $user[ 'verify_code' ] ) && strlen( $user[ 'verify_code' ] ) > 0 AND $user[ 'verify_code' ] == $password )
			{
				$newPassword = self::generateSaltCode( $password );
				$this->model->saveUserPassword( $user[ 'id' ], $newPassword );
				$this->model->saveUserVerifyCode( $user[ 'id' ], '' );

				// Reset settings
				$user[ 'password' ] = $newPassword;
				$user[ 'verify_code' ] = '';
			}
			elseif( $user[ 'password' ] != $tempPassword )
			{
				return FALSE;
			}

			$this->regenerateSessions( $user, IsVar::set( $_POST, 'rememberme', FALSE ) );
			return TRUE;
		}

		return FALSE;
	}

	public function requestNewPassword( $email )
	{
		if( filter_var( $email, FILTER_VALIDATE_EMAIL ) )
		{
			if( isset( $_SESSION[ 'requestedPassword' ] ) === TRUE AND $_SESSION[ 'requestedPassword' ] == $email )
			{
				return 2;
			}

			$user = $this->model->getUserByEmail( $email );
			if( count( $user ) > 0 )
			{
				$userName = self::getUserName( $user[ 'id' ] );
				$newPassword = self::generateKey( 20 );
				$this->model->saveUserVerifyCode( $user[ 'id' ], $newPassword );

				$mailer = new Mailer( 'requestpassword.txt', '{{Request password}}' );
				$mailer->addReplacement( '<name>', $userName );
				$mailer->addReplacement( '<email>', $email );
				$mailer->addReplacement( '<password>', $newPassword );
				$mailer->AddAddress( $email, $userName );
				$mailer->email();

				$_SESSION[ 'requestedPassword' ] = $email;
				return 0;
			}
		}

		return 1;
	}

	public static function generateKey( $length = 8 )
	{
		$possible = "12346789abcdfghjkmnpqrtvwxyzABCDFGHJKLMNPQRTVWXYZ";
		$maxlength = strlen( $possible );

		if( $length > $maxlength )
		{
			$length = $maxlength;
		}

		$password = '';
		for( $i = 0; $i <= $length; $i++ )
		{
			$char = substr( $possible, mt_rand( 0, $maxlength - 1 ), 1 );
			if( strstr( $password, $char ) === FALSE )
			{
				$password .= $char;
			}
		}

		return $password;
	}

	private function regenerateSessions( $user, $setCookies = FALSE )
	{
		//session_regenerate_id();
		
		$_SESSION['user']	= array(
				'id'	=> $user[ 'id' ],
				'hash'	=> $user[ 'password' ]
			);
		

		if( $setCookies !== FALSE OR ( isset( $_COOKIE[ 'user' ][ 'id' ] ) AND $_COOKIE[ 'user' ][ 'id' ] > 0 AND isset( $_COOKIE[ 'user' ][ 'hash' ] ) AND strlen( $_COOKIE[ 'user' ][ 'hash' ] ) > 0 ) )
		{
			$loginTime = time() + LOGIN_EXPIRE;
			setcookie( 'user[id]', $user[ 'id' ], $loginTime, '/' );
			setcookie( 'user[hash]', $user[ 'password' ], $loginTime, '/' );
		}
	}

	public static function generateSaltCode( $string )
	{
		return md5( self::$salt . $string . self::$salt );
	}

	public function getParentUser( $userId = NULL )
	{
		if( $userId == NULL )
		{
			$userId = self::getCurrentUserId();
		}

		$parentUserId = $this->model->getUserParentId( $userId );

		return $this->getUser( $parentUserId );
	}

	public static function getCurrentUserIP()
	{
		if( isset( $_SERVER[ 'REMOTE_ADDR' ] ) )
		{
			return $_SERVER[ 'REMOTE_ADDR' ];
		}
		elseif( isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) )
		{
			return $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		}
		elseif( isset( $_SERVER[ 'HTTP_CLIENT_IP' ] ) )
		{
			return $_SERVER[ 'HTTP_CLIENT_IP' ];
		}
		else
		{
			return false;
		}
	}

	public static function getUser( $userId = NULL )
	{
		if( $userId == NULL )
		{
			$userId = self::getCurrentUserId();
		}

		$_this = StoneController::getClass( 'StoneUser' );
		$user = $_this->model->getUserById( $userId );

		return $user;
	}

	public static function getUserLanguage( $userId = NULL )
	{
		if( $userId === NULL )
		{
			$userId = self::getCurrentUserId();
		}

		if( $userId > 0 )
		{
			$user = self::getUser( $userId );
			return IsVar::set( $user, 'language', DEFAULT_LANGUAGE_ID, TRUE );
		}

		return DEFAULT_LANGUAGE_ID;
	}

	public static function getLastLogin( $userId = NULL )
	{
		if( $userId === NULL )
		{
			$userId = self::getCurrentUserId();
		}

		if( $userId > 0 )
		{
			$_this = StoneController::getClass( 'StoneUser' );
			return $_this->model->getLastLoginLogByUserId( $userId );
		}

		return array();
	}

	public static function getUserName( $userId = NULL )
	{
		if( $userId == NULL )
		{
			$userId = self::getCurrentUserId();
		}

		$_this = StoneController::getClass( 'StoneUser' );
		$user = $_this->model->getUserById( $userId );

		if( isset( $user[ 'properties' ] ) )
		{
			$userName = trim( IsVar::set( $user[ 'properties' ], 'firstname' ) . ' ' . IsVar::set( $user[ 'properties' ], 'lastname' ) );
			if( strlen( $userName ) == 0 )
			{
				$userName = IsVar::set( $user[ 'properties' ], 'company' );
			}
		}

		if( isset( $userName ) === FALSE OR strlen( $userName ) == 0 )
		{
			$userName = IsVar::set( $user, 'email' );
		}

		return $userName;
	}

	public static function getUserRole( $userId = NULL )
	{
		if( $userId == NULL )
		{
			$userId = self::getCurrentUserId();
		}

		$_this = StoneController::getClass( 'StoneUser' );
		$user = $_this->model->getUserById( $userId, FALSE );

		return $user[ 'role' ];
	}

	public static function ago( $time = 0 )
	{
		$periods = array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' );
		$lengths = array( '60','60','24','7','4.35','12','10' );

		$now = time();

		$difference	= $now - $time;

		for( $j = 0; $difference >= $lengths[ $j ] AND $j < count( $lengths ) - 1; $j++ )
		{
			$difference /= $lengths[ $j ];
		}

		$difference = round( $difference );

		if( $difference != 1 )
		{
			$periods[ $j ] .= 's';
		}

		return $difference . ' {{' . $periods[ $j ] . '}} {{ago}}';
	}

	public static function getCountryAndCityFromIPAddress( $ipAddress = '' )
	{
		$ipDetail = array();

		$xml = file_get_contents( 'http://api.hostip.info/?ip=' . $ipAddress );

		preg_match( "@<countryAbbrev>(.*?)</countryAbbrev>@si", $xml, $cc_match );

		$countyCode = strtoupper( IsVar::set( $cc_match, 1, '' ) );
		if( isset( self::$countries[ $countyCode ] ) )
		{
			return $countyCode;
		}

		return '';
	}

	public static $countries = array(
		"AF"=>"{{Afghanistan}}",
		"AX"=>"{{Akrotiri}}",
		"AL"=>"{{Albania}}",
		"AG"=>"{{Algeria}}",
		"AQ"=>"{{American Samoa}}",
		"AN"=>"{{Andorra}}",
		"AO"=>"{{Angola}}",
		"AV"=>"{{Anguilla}}",
		"AY"=>"{{Antarctica}}",
		"AC"=>"{{Antigua and Barbuda}}",
		"XQ"=>"{{Arctic Ocean}}",
		"AR"=>"{{Argentina}}",
		"AM"=>"{{Armenia}}",
		"AA"=>"{{Aruba}}",
		"AT"=>"{{Ashmore and Cartier Islands}}",
		"ZH"=>"{{Atlantic Ocean}}",
		"AS"=>"{{Australia}}",
		"AS"=>"{{Austria}}",
		"AJ"=>"{{Azerbaijan }}",
		"BF"=>"{{Bahamas, The}}",
		"BA"=>"{{Bahrain}}",
		"FQ"=>"{{Baker Island}}",
		"BG"=>"{{Bangladesh}}",
		"BB"=>"{{Barbados}}",
		"BS"=>"{{Bassas da India}}",
		"BO"=>"{{Belarus}}",
		"BE"=>"{{Belgium}}",
		"BH"=>"{{Belize}}",
		"BN"=>"{{Benin}}",
		"BP"=>"{{Bermuda}}",
		"BT"=>"{{Bhutan}}",
		"BL"=>"{{Bolivia}}",
		"BK"=>"{{Bosnia and Herzegovina}}",
		"BC"=>"{{Botswana}}",
		"BV"=>"{{Bouvet Island}}",
		"BR"=>"{{Brazil}}",
		"IO"=>"{{British Indian Ocean Territory}}",
		"VI"=>"{{British Virgin Islands}}",
		"BX"=>"{{Brunei}}",
		"BU"=>"{{Bulgaria}}",
		"UV"=>"{{Burkina Faso}}",
		"BM"=>"{{Burma}}",
		"BY"=>"{{Burundi }}",
		"CB"=>"{{Cambodia}}",
		"CM"=>"{{Cameroon}}",
		"CA"=>"{{Canada}}",
		"CV"=>"{{Cape Verde}}",
		"CJ"=>"{{Cayman Islands}}",
		"CT"=>"{{Central African Republic}}",
		"CD"=>"{{Chad}}",
		"CI"=>"{{Chile}}",
		"CH"=>"{{China}}",
		"KT"=>"{{Christmas Island}}",
		"IP"=>"{{Clipperton Island}}",
		"CK"=>"{{Cocos (Keeling) Islands}}",
		"CO"=>"{{Colombia}}",
		"CN"=>"{{Comoros}}",
		"CG"=>"{{Congo, Democratic Republic of the}}",
		"CF"=>"{{Congo, Republic of the}}",
		"CW"=>"{{Cook IslandsC}}",
		"CR"=>"{{Coral Sea Islands}}",
		"CS"=>"{{Costa Rica}}",
		"IV"=>"{{Cote d\'Ivoire}}",
		"HR"=>"{{Croatia}}",
		"CU"=>"{{Cuba}}",
		"CY"=>"{{Cyprus}}",
		"EZ"=>"{{Czech Republic }}",
		"DA"=>"{{Denmark}}",
		"DX"=>"{{Dhekelia}}",
		"DJ"=>"{{Djibouti}}",
		"DO"=>"{{Dominica}}",
		"DR"=>"{{Dominican Republic }}",
		"TT"=>"{{East Timor}}",
		"EC"=>"{{Ecuador}}",
		"EG"=>"{{Egypt}}",
		"ES"=>"{{El Salvador}}",
		"EK"=>"{{Equatorial Guinea}}",
		"ER"=>"{{Eritrea}}",
		"EN"=>"{{Estonia}}",
		"ET"=>"{{Ethiopia}}",
		"EU"=>"{{Europa Island}}",
		"EU1"=>"{{European Union}}",
		"EU2"=>"{{European Union entry follows Taiwan }}",
		"FK"=>"{{Falkland Islands (Islas Malvinas)}}",
		"FO"=>"{{Faroe Islands}}",
		"FJ"=>"{{Fiji}}",
		"FI"=>"{{Finland}}",
		"FR"=>"{{France}}",
		"FG"=>"{{French Guiana}}",
		"FP"=>"{{French Polynesia}}",
		"FS"=>"{{French Southern and Antarctic Lands }}",
		"GB"=>"{{Gabon}}",
		"GA"=>"{{Gambia, The}}",
		"GZ"=>"{{Gaza Strip}}",
		"GG"=>"{{Georgia}}",
		"GM"=>"{{Germany}}",
		"GH"=>"{{Ghana}}",
		"GI"=>"{{Gibraltar}}",
		"GO"=>"{{Glorioso Islands}}",
		"GR"=>"{{Greece}}",
		"GL"=>"{{Greenland}}",
		"GJ"=>"{{Grenada}}",
		"GP"=>"{{Guadeloupe}}",
		"GQ"=>"{{Guam}}",
		"GT"=>"{{Guatemala}}",
		"GK"=>"{{Guernsey}}",
		"GV"=>"{{Guinea}}",
		"PU"=>"{{Guinea-Bissau}}",
		"GY"=>"{{Guyana }}",
		"HA"=>"{{Haiti}}",
		"HM"=>"{{Heard Island and McDonald Islands}}",
		"VT"=>"{{Holy See (Vatican City)}}",
		"HO"=>"{{Honduras}}",
		"HK"=>"{{Hong Kong}}",
		"HQ"=>"{{Howland Island}}",
		"HU"=>"{{Hungary }}",
		"IC"=>"{{Iceland}}",
		"IN"=>"{{India}}",
		"XO"=>"{{Indian Ocean}}",
		"ID"=>"{{Indonesia}}",
		"IR"=>"{{Iran}}",
		"IZ"=>"{{Iraq}}",
		"EI"=>"{{Ireland}}",
		"IM"=>"{{Isle of Man}}",
		"IS"=>"{{Israel}}",
		"IT"=>"{{Italy }}",
		"JM"=>"{{Jamaica}}",
		"JN"=>"{{Jan Mayen}}",
		"JA"=>"{{Japan}}",
		"DQ"=>"{{Jarvis Island}}",
		"JE"=>"{{Jersey}}",
		"JQ"=>"{{Johnston Atoll}}",
		"JO"=>"{{Jordan}}",
		"JU"=>"{{Juan de Nova Island }}",
		"KZ"=>"{{Kazakhstan}}",
		"KE"=>"{{Kenya}}",
		"KQ"=>"{{Kingman Reef}}",
		"KR"=>"{{Kiribati}}",
		"KN"=>"{{Korea, North}}",
		"KS"=>"{{Korea, South}}",
		"KU"=>"{{Kuwait}}",
		"KH"=>"{{Kyrgyzstan }}",
		"LA"=>"{{Laos}}",
		"LG"=>"{{Latvia}}",
		"LE"=>"{{Lebanon}}",
		"LT"=>"{{Lesotho}}",
		"LI"=>"{{Liberia}}",
		"LY"=>"{{Libya}}",
		"LS"=>"{{Liechtenstein}}",
		"LH"=>"{{Lithuania}}",
		"LU"=>"{{Luxembourg }}",
		"MC"=>"{{Macau}}",
		"MK"=>"{{Macedonia}}",
		"MA"=>"{{Madagascar}}",
		"MI"=>"{{Malawi}}",
		"MY"=>"{{Malaysia}}",
		"MV"=>"{{Maldives}}",
		"ML"=>"{{Mali}}",
		"MT"=>"{{Malta}}",
		"RM"=>"{{Marshall Islands}}",
		"MB"=>"{{Martinique}}",
		"MR"=>"{{Mauritania}}",
		"MP"=>"{{Mauritius}}",
		"MF"=>"{{Mayotte}}",
		"MX"=>"{{Mexico}}",
		"FM"=>"{{Micronesia, Federated States of}}",
		"MQ"=>"{{Midway Islands}}",
		"MD"=>"{{Moldova}}",
		"MN"=>"{{Monaco}}",
		"MG"=>"{{Mongolia}}",
		"MJ"=>"{{Montenegro}}",
		"MH"=>"{{Montserrat}}",
		"MO"=>"{{Morocco}}",
		"MZ"=>"{{Mozambique }}",
		"WA"=>"{{Namibia}}",
		"MR"=>"{{Nauru}}",
		"BQ"=>"{{Navassa Island}}",
		"NP"=>"{{Nepal}}",
		"NL"=>"{{Netherlands}}",
		"NT"=>"{{Netherlands Antilles}}",
		"NC"=>"{{New Caledonia}}",
		"NZ"=>"{{New Zealand}}",
		"NU"=>"{{Nicaragua}}",
		"NG"=>"{{Niger}}",
		"NI"=>"{{Nigeria}}",
		"NE"=>"{{Niue}}",
		"NF"=>"{{Norfolk Island}}",
		"CQ"=>"{{Northern Mariana Islands}}",
		"NO"=>"{{Norway}}",
		"MU"=>"{{Oman }}",
		"ZN"=>"{{Pacific Ocean}}",
		"PK"=>"{{Pakistan}}",
		"PS"=>"{{Palau}}",
		"LQ"=>"{{Palmyra Atoll}}",
		"PM"=>"{{Panama}}",
		"PP"=>"{{Papua New Guinea}}",
		"PF"=>"{{Paracel Islands}}",
		"PA"=>"{{Paraguay}}",
		"PE"=>"{{Peru}}",
		"RP"=>"{{Philippines}}",
		"PC"=>"{{Pitcairn Islands}}",
		"PL"=>"{{Poland}}",
		"PO"=>"{{Portugal}}",
		"PQ"=>"{{Puerto Rico }}",
		"QA"=>"{{Qatar }}",
		"RE"=>"{{Reunion}}",
		"RO"=>"{{Romania}}",
		"RS"=>"{{Russia}}",
		"RW"=>"{{Rwanda }}",
		"SH"=>"{{Saint Helena}}",
		"SC"=>"{{Saint Kitts and Nevis}}",
		"ST"=>"{{Saint Lucia}}",
		"SB"=>"{{Saint Pierre and Miquelon}}",
		"VC"=>"{{Saint Vincent and the Grenadines}}",
		"WS"=>"{{Samoa}}",
		"SM"=>"{{San Marino}}",
		"TP"=>"{{Sao Tome and Principe}}",
		"SA"=>"{{Saudi Arabia}}",
		"SG"=>"{{Senegal}}",
		"RB"=>"{{Serbia}}",
		"SE"=>"{{Seychelles}}",
		"SL"=>"{{Sierra Leone}}",
		"SN"=>"{{Singapore}}",
		"LO"=>"{{Slovakia}}",
		"SI"=>"{{Slovenia}}",
		"BP"=>"{{Solomon Islands}}",
		"SO"=>"{{Somalia}}",
		"SF"=>"{{South Africa}}",
		"SX"=>"{{South Georgia and the South Sandwich Islands}}",
		"OO"=>"{{Southern Ocean}}",
		"SP"=>"{{Spain}}",
		"PG"=>"{{Spratly Islands}}",
		"CE"=>"{{Sri Lanka}}",
		"SU"=>"{{Sudan}}",
		"NS"=>"{{Suriname}}",
		"SV"=>"{{Svalbard}}",
		"WZ"=>"{{Swaziland}}",
		"SW"=>"{{Sweden}}",
		"SZ"=>"{{Switzerland}}",
		"SY"=>"{{Syria }}",
		"TW"=>"{{Taiwan}}",
		"TW1"=>"{{Taiwan entry follows Zimbabwe}}",
		"TI"=>"{{Tajikistan}}",
		"TZ"=>"{{Tanzania}}",
		"TH"=>"{{Thailand}}",
		"TO"=>"{{Togo}}",
		"TL"=>"{{Tokelau}}",
		"TN"=>"{{Tonga}}",
		"TD"=>"{{Trinidad and Tobago}}",
		"TE"=>"{{Tromelin Island}}",
		"TS"=>"{{Tunisia}}",
		"TU"=>"{{Turkey}}",
		"TX"=>"{{Turkmenistan}}",
		"TK"=>"{{Turks and Caicos Islands}}",
		"TV"=>"{{Tuvalu }}",
		"UG"=>"{{Uganda}}",
		"UP"=>"{{Ukraine}}",
		"AE"=>"{{United Arab Emirates}}",
		"UK"=>"{{United Kingdom}}",
		"US"=>"{{United States}}",
		"UM"=>"{{United States Pacific Island Wildlife Refuges}}",
		"UY"=>"{{Uruguay}}",
		"UZ"=>"{{Uzbekistan }}",
		"NH"=>"{{Vanuatu}}",
		"VE"=>"{{Venezuela}}",
		"VM"=>"{{Vietnam}}",
		"VQ"=>"{{Virgin Islands }}",
		"WQ"=>"{{Wake Island}}",
		"WF"=>"{{Wallis and Futuna}}",
		"WE"=>"{{West Bank}}",
		"WI"=>"{{Western Sahara}}",
		"YM"=>"{{Yemen}}",
		"ZA"=>"{{Zambia}}",
		"ZI"=>"{{Zimbabwe}}",
		""	=>"{{Unknown}}"
	);
}
?>