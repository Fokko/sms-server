<?php

class UserView extends View
{
	public function requestPasswordForm( $mailSend = NULL )
	{
		$html = '<form class="form" method="post">
			<script type="text/javascript">
			$( function() {
				$( "#email" ).focus();
			} );
			</script>
			<div id="logo">
				<img src="' . HTTP_ROOT . 'images/logo.png">
			</div>
			<p>{{Fill in your e-mail address and you will receive your new password by e-mail}}.</p>' . PHP_EOL;
			if( $mailSend === 2 )
			{
				$html .= '<label class="error">' . PHP_EOL;
				$html .= '{{Your already requested a new password}}.';
				$html .= '</label><p>&nbsp;</p>' . PHP_EOL;
			}
			elseif( $mailSend === 1 )
			{
				$html .= '<label class="error">' . PHP_EOL;
				$html .= '{{Your e-mail address is was not found in our database}}.';
				$html .= '</label><p>&nbsp;</p>' . PHP_EOL;
			}
			elseif( $mailSend === 0 )
			{
				$html .= '<label class="error">' . PHP_EOL;
				$html .= '{{Your password is send to your e-mail}}.';
				$html .= '</label><p>&nbsp;</p>' . PHP_EOL;
			}

			$html.='
			<label>E-mail</label><br />
			<input type="text" id="email" tabindex="1" class="text large required" name="email" value="' . IsVar::set( $_POST, 'email' ) . '" /><br />
			
			<div class="clearfix">&nbsp;</div>

			<p>
				<input name="submit" tabindex="3" type="submit" class="submit" id="btnLogin" value="{{REQUEST PASSWORD}}" />
			</p>
		</form>';
		return $html;
	}

	public function showUsers( $users )
	{
		$html = '<h1>{{Users}}</h1>' . PHP_EOL;
		$currentUser = StoneUserController::getUser();
		$userRoles = UserController::$userRoles;
		array_pop( $userRoles );
		if( in_array( IsVar::set( $currentUser, 'role' ), $userRoles ) )
		{
			$html .= self::showFormDialogLink( 'user', 'action=add', 'action=save', 'action=showTable', '#userList', '<img width="16" height="16" alt="" src="' . HTTP_ROOT . 'images/icons/add.png"/> {{Add user}}' ) . '<br />' . PHP_EOL;
		}

		$html .= '<div id="userList">' . PHP_EOL;
		$html .= 	$this->showUsersTable( $users );
		$html .= '</div>' . PHP_EOL;

		return $html;
	}

	public function showAddresses()
	{
		$html = '';
		
		$html .= '<h1>Addresses</h1>';
		
		return $html;
	}

	public function register( $registered = FALSE )
	{
		$html = '';
	
		if( $registered )
		{
			$html .= '<h1>{{Succesfully registered}}</h1>';
			
			$html .= '<p>{{Your account has succesfully been submitted}}.</p>';	
		}
		else 
		{			
			$html .= '
		<script type="text/javascript">
			$(function() {
				// validate signup form on keyup and submit
				$("#signupForm").validate({
					rules: {
						firstname: "required",
						lastname: "required",
						adres: "required",
						place: "required",
						password: {
							required: true,
							minlength: 5
						},
						confirm_password: {
							required: true,
							minlength: 5,
							equalTo: "#password"
						},
						email: {
							required: true,
							email: true
						}
					},
					messages: {
						firstname: "{{Firstname is required}}.",
						lastname: "{{Lastname is required}}.",
						address: "{{Address is required}}.",
						place: "{{Residence is required}}.",				
						password: {
							required: "{{Password is required}}.",
							minlength: "{{Password needs to be 5 characters or longer}}."
						},
						confirm_password: {
							required: "{{Password verification is required}}.",
							minlength: "{{Password needs to be 5 characters or longer}}.",
							equalTo: "*"
						},
						email: "{{E-mail address is required}}."
					}
				});
			});
		</script>';
		
			$html .= '<form action="' . HTTP_ROOT . 'index.php?module=user&action=register" id="signupForm" method="post">';
		
			$html .= '<h1>{{Register}}</h1>';
			$html .= '<p>{{Register for free and get access to our service}}.</p>';
		
			$html .= '<label for="email">{{E-mail address}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[email]" id="email" /><br /><br />';
		
			$html .= '<label for="password">{{Password}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="password" value="" name="properties[password]" id="password" /><br /><br />';
		
			$html .= '<label for="confirm_password">{{Password verification}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="password" value="" name="properties[confirm_password]" id="confirm_password" /><br /><br />';
		
			$html .= '<label for="firstname">{{Firstname}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[firstname]" id="firstname" /><br /><br />';
		
			$html .= '<label for="lastname">{{Lastname}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[lastname]" id="lastname" /><br /><br />';
		
			$html .= '<label for="address">{{Address}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[address]" id="address" /><br /><br />';
		
			$html .= '<label for="place">{{Residence}} <span class="required">*</span>:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[place]" id="place" /><br /><br />';
		
			$html .= '<label for="phone">{{Phone number}}:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[telefoon]" id="phone" /><br /><br />';
		
			$html .= '<label for="twitter">{{Twitter}}:</label><br />';
			$html .= '<input class="text large" type="text" value="" name="properties[twitter]" id="twitter" /><br />';
		
			$html .= '<div class="clearfix">&nbsp;</div>';
			$html .= '<p><input type="submit" value="{{Register}}" class="submit"></input></p>';
		
			$html .= '</form>';
		}
				
		return $html;
	}
	
	public function showUsersTable( $users = array() )
	{
		$html = '';
		if( is_array( $users ) )
		{
			$html .= '
			<script type="text/javascript">
				$( function() {
					$( "#usersTable" ).tablesorter( {
						headers: {
							0: {
								sorter:	false
							},
							4: {
								sorter:	false
							}
						}
					} );
				} );
			</script>
			' . PHP_EOL;

			$html .= '	<table cellpadding="0" cellspacing="0" id="usersTable" class="tablesorter">' . PHP_EOL;
			$html .= '		<thead>' . PHP_EOL;
			$html .= '			<tr class="tableHeader">' . PHP_EOL;
			$html .= '				<th width="50" class="icons">&nbsp;</th>' . PHP_EOL;
			$html .= '				<th>{{Name}}</th>' . PHP_EOL;
			$html .= '				<th>{{Parent user}}</th>' . PHP_EOL;
			$html .= '				<th>{{Company}}</th>' . PHP_EOL;
			$html .= '				<th>{{E-mail address}}</th>' . PHP_EOL;
			$html .= '			</tr>' . PHP_EOL;
			$html .= '		</thead>' . PHP_EOL;
			$html .= '		<tbody>' . PHP_EOL;

			foreach( $users as $userId => $user )
			{
				$html .= $this->showUserRow( $user );
			}

			$html .= '		</tbody>' . PHP_EOL;
			$html .= '	</table>' . PHP_EOL;
		}
		else
		{
			$html .= '<p>{{There were no users found}}.</p>' . PHP_EOL;
		}

		return $html;
	}

	private function showUserRow( $user )
	{
		$html = '		<tr>' . PHP_EOL;
		$html .= '			<td>' . PHP_EOL;

		if( $user[ 'id' ] == StoneUserController::getCurrentUserId() )
		{
			$html .=				self::showFormDialogLink( 'user', 'action=edit&amp;userId=' . $user[ 'id' ], 'action=save&amp;userId=' . $user[ 'id' ], 'refresh', '#userList', '{{Edit user}}', '#dialog_error', 'ui-icon editIcon clickTarget left' );
		}
		else
		{
			$html .=				self::showFormDialogLink( 'user', 'action=edit&amp;userId=' . $user[ 'id' ], 'action=save&amp;userId=' . $user[ 'id' ], 'action=showTable', '#userList', '{{Edit user}}', '#dialog_error', 'ui-icon editIcon clickTarget left' );
			$html .= 				self::showConfirmDialogLink( 'user', 'action=deleteUser&amp;userId=' . $user[ 'id' ], 'action=showTable', '#userList', '{{Delete user}}', '{{Are you sure you want to delete this user?}}', 'left ui-icon deleteIcon' );
		}

		$html .= '			</td>' . PHP_EOL;
		$html .= '			<td>' . StoneUserController::getUserName( $user[ 'id' ] ) . '</td>' . PHP_EOL;
		$html .= '			<td>' . StoneUserController::getUserName( $user[ 'parent_id' ] ) . '</td>' . PHP_EOL;
		$html .= '			<td>' . IsVar::set( $user, 'company', '', TRUE ) . '</td>' . PHP_EOL;
		$html .= '			<td>' . $user[ 'email' ] . '</td>' . PHP_EOL;
		$html .= '		</tr>' . PHP_EOL;

		if( isset( $user[ 'subusers' ] ) )
		{
			foreach( $user[ 'subusers' ] as $subusers )
			{
				$html .= $this->showUserRow( $subusers );
			}
		}

		return $html;
	}

	public static function showCustomHTML( $content )
	{
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>' . SYSTEM_NAME . '</title>
			<script type="text/javascript" src="' . HTTP_ROOT . 'js/jquery.min.js"></script>
			<script type="text/javascript" src="' . HTTP_ROOT . 'js/jquery.validate.min.js"></script>

			<style type="text/css">
			.error {
				padding: 7px;
				margin: 3px;
				background-color: #FCC;
				border: 1px solid #F00;
				font-family: arial;
				font-size: 10px;
				font-style: normal;
				font-weight: normal;
				font-variant: normal;
				color: #000;
				float: left;
				width: 98%;
				-moz-border-radius:5px;
				-webkit-border-radius: 5px;
			}
			</style>
			<link href="' . HTTP_ROOT . 'css/style.css" rel="stylesheet" type="text/css" />
		</head>
		<body>
			' . $content . '
			 
		</body>
	</html>';
		return $html;
	}

	public function showUserForm( $user = array(), $parentUser = array(), $users = array() )
	{
		$currentUserRole = StoneUserController::getUserRole();

		if( isset( $user[ 'properties' ] ) === FALSE )
		{
			$user[ 'properties' ] = array();
		}

		if( isset( $parentUser[ 'properties' ] ) === FALSE )
		{
			$parentUser[ 'properties' ] = array();
		}

		$html = '	<script type="text/javascript">' . PHP_EOL;
		$html .= '	function showDiv( divID ) {' . PHP_EOL;
		$html .= '		$( ".tabDiv" ).hide();' . PHP_EOL;
		$html .= '		$( "#div" + divID ).show();' . PHP_EOL;
		$html .= '		$( ".tab" ).removeClass( "ui-tabs-selected" );' . PHP_EOL;
		$html .= '		$( "#tab" + divID ).addClass( "ui-tabs-selected" );' . PHP_EOL;
		$html .= '	}' . PHP_EOL;
		$html .= '	$( function() {' . PHP_EOL;
		$html .= '		showDiv( 1 );' . PHP_EOL;
		$html .= '	} );' . PHP_EOL;
		$html .= '	</script>';

		$html .= '	<div>' . PHP_EOL;
		$html .= '		<ul class="ui-tabs-nav">' . PHP_EOL;
		$html .= '			<li id="tab1" class="tab"><a href="javascript:showDiv(1);"><span>{{Personal information}}</span></a></li>' . PHP_EOL;
		$html .= '			<li id="tab2" class="tab"><a href="javascript:showDiv(2);"><span>{{Login}}</span></a></li>' . PHP_EOL;

		$html .= '		</ul>' . PHP_EOL;
		$html .= '	</div>' . PHP_EOL;
		$html .= '	<br />' . PHP_EOL;
		$html .= '	<div id="popup">' . PHP_EOL;
		$html .= '		<form id="userForm" class="form" method="post" enctype="multipart/form-data">' . PHP_EOL;

		if( IsVar::set( $user, 'id', 0 ) > 0 )
		{
			$html .= '			<input type="hidden" name="userId" value="' . IsVar::set( $user, 'id' ) . '">' . PHP_EOL;
		}

		$html .= '			<fieldset id="div1" class="tabDiv">' . PHP_EOL;
		$html .= '				<legend>{{Personal information}}</legend>' . PHP_EOL;
		$html .= '				<dt>{{Company}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="properties[company]" value="' . IsVar::set( $user[ 'properties' ], 'company' ) . '" /></dd>' . PHP_EOL;
		$html .= '				<dt>{{Firstname}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="properties[firstname]" value="' . IsVar::set( $user[ 'properties' ], 'firstname' ) . '" /></dd>' . PHP_EOL;
		$html .= '				<dt>{{Lastname}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="properties[lastname]" value="' . IsVar::set( $user[ 'properties' ], 'lastname' ) . '" /></dd>' . PHP_EOL;
		$html .= '				<dt>{{Phone}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="properties[phone]" value="' . IsVar::set( $user[ 'properties' ], 'phone' ) . '" /></dd>' . PHP_EOL;
		$html .= '				<dt>{{Website}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="properties[website]" value="' . IsVar::set( $user[ 'properties' ], 'website' ) . '" /></dd>' . PHP_EOL;
		$html .= '			</fieldset>' . PHP_EOL;

		$html .= '			<fieldset id="div2" class="tabDiv">' . PHP_EOL;
		$html .= '				<legend>{{Login information}}</legend>' . PHP_EOL;

		if( $currentUserRole == 'administrator' AND IsVar::set( $user, 'id' ) != StoneUserController::getCurrentUserId() )
		{
			$html .= '				<dt>{{Parent user}}:</dt>' . PHP_EOL;
			$html .= '				<dd><select name="parent_id">';
			foreach( $users as $userId => $_user )
			{
				if( IsVar::set( $user, 'id' ) == $userId )
				{
					continue;
				}

				$html .= '<option value="' . $userId . '"' . ( IsVar::set( $user, 'parent_id', 0 ) == $userId ? ' selected="selected"' : '' ) . '>' . StoneUserController::getUserName( $userId ) . '</option>';
			}
			$html .= '				</select></dd>' . PHP_EOL;
		}
		else
		{
			$html .= '				<input type="hidden" name="parent_id" value="' . IsVar::set( $user, 'parent_id', 0 ) . '" />' . PHP_EOL;
		}

		if( IsVar::set( $user, 'id' ) == StoneUserController::getCurrentUserId() )
		{
			$html .= '				<input type="hidden" name="role" value="' . IsVar::set( $user, 'role' ) . '" />' . PHP_EOL;
		}
		else
		{
			$html .= '				<dt>{{User role}}:</dt>' . PHP_EOL;
			$html .= '				<dd><select name="role">' . PHP_EOL;

			$startOptions = $currentUserRole == 'administrator' ? TRUE : FALSE;
			foreach( UserController::$userRoles as $userRole )
			{
				if( $startOptions === TRUE )
				{
					$html .= '<option' . ( IsVar::set( $user, 'role' ) == $userRole ? ' selected="selected"' : '' ) . '>' . $userRole . '</option>';
				}

				if( $startOptions === FALSE AND $userRole == $currentUserRole )
				{
					$startOptions = TRUE;
				}
			}

			$html .= '					' . PHP_EOL;
			$html .= '				</select></dd>' .PHP_EOL;
		}
		$html .= '				<dt>{{E-mail address}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="email" value="' . IsVar::set( $user, 'email' ) . '" /></dd>' .PHP_EOL;
		$html .= '				<dt>{{Password}}:</dt>' . PHP_EOL;
		$html .= '				<dd><input type="text" name="password" /></dd>' . PHP_EOL;
		$html .= '				<dt>{{Interface language}}:</dt>' . PHP_EOL;
		$html .= '				<dd><select name="properties[language]">' . PHP_EOL;

		$languages = TranslationController::getLanguages();
		if( USE_MULTILANGUAGE === TRUE )
		{
			foreach( $languages as $languageId => $language )
			{
				$html .= '	<option value="' . $languageId . '"' . ( ( IsVar::set( $user[ 'properties' ], 'language', 0 ) == $languageId ) ? ' selected="selected"' : '' ) . '>' . $language[ 'name' ] . '</option>' . PHP_EOL;
			}
		}
		else
		{
			$html .= '	<option value="' . DEFAULT_LANGUAGE_ID . '">' . $languages[ DEFAULT_LANGUAGE_ID ][ 'name' ] . '</option>' . PHP_EOL;
		}
		$html .= '				</select></dd>' .PHP_EOL;
		$html .= '			</fieldset>' . PHP_EOL;
		$html .= '			</form>' . PHP_EOL;
		$html .= '		</div>' . PHP_EOL;
		$html .= '		<br style="clear: both;" />' . PHP_EOL;

		return $html;
	}

	public function loginForm( $loginAttempt = FALSE, $registered = FALSE )
	{
		$html = '
			<script type="text/javascript">
			$( function() {
				$( "#user" ).focus();
			} );
			</script>
			<div id="logo" align="center">
				<img src="' . HTTP_ROOT . 'images/logo.png">
			</div>';
			
			$html .= '
					<div style="margin-left: auto; margin-right: auto; width: 420px;">
						<div style="float: left; width: 420px;">
							' . $this->login( $loginAttempt ) . '
						</div>';
						
						/*
			$html .= '	<div style="padding-left: 45px; border-left: 1px dashed #BBBBBB; float: right; width: 460px;">							
							' . $this->register( $registered ) . '
						</div>';*/
		
			$html .= '	<div style="clear: both;"></div>
					</div>';
			
			

		return $html;
	}
	
	private function login( $loginAttempt = false )
	{
		$html = '
				<h1>Login</h1>
				<form class="form" method="post" action="' . HTTP_ROOT . '">';
		if( $loginAttempt === TRUE )
		{
			$html .= '<label class="error">' . PHP_EOL;
			$html .= '{{Login failed}}.';
			$html .= '</label><p>&nbsp;</p>' . PHP_EOL;
		}
			
		$html .='	<p>{{Login with your username and password}}.</p>
					<label>{{E-mail address}} <span class="required">*</span>:</label><br />
					<input type="text" id="user" tabindex="1" class="text large required"  name="user" value="' . IsVar::set( $_POST, 'user' ) . '" /><br />
			
					<div class="clearfix">&nbsp;</div>
					<label>{{Password}} <span class="required">*</span>: <a href="' . HTTP_ROOT . 'index.php?module=user&action=requestpassword" style="text-transform:lowercase; font-size:11px;"">({{Lost password?}})</a></label><br />
					<input type="password" tabindex="2" id="user" class="text large required" name="password" /><br />
			
					<div class="clearfix">&nbsp;</div>
			
					<p>
							<input name="login" tabindex="3" type="submit" class="submit" id="btnLogin" value="{{LOGIN}}" />
							<input type="checkbox" tabindex="4" class="checkbox" id="rememberme" name="rememberme" value="1" />
							<label for="rememberme">{{Remember me}}</label>
							<br /><br />
						</p>
					</form>';
		
		return $html;
	}
}
?>