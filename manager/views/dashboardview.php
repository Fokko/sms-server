<?php
class DashboardView extends View
{
	public function home( $received = array(), $send = array() )
	{
		$html =		'<div id="center-column" style="width: 440px; float: left; margin-left: 15px;">';
		$html .=	'	<div class="top-bar">';
		$html .=	'		<h1>Ontvangen berichten</h1>';
		$html .= '	<script type="text/javascript">
						var time = 20;
						window.onload = function()
						{
							timerTick();
						}

						function timerTick()
						{
							if( time-- <= 0 )
							{
								location.reload( true );
							}
							else
							{
								setTimeout( \'timerTick();\', 1000 );
							}
						}
					</script>';
		
		$html .=	'	</div>';									
		
		
		foreach( $received as $messageId => $message )
		{
			if( $message[ 'sender' ] == '' ) 
			{
				$message[ 'sender' ] = 'Mail';
			}
		
			$minutesAgo = $this->formatTime( $message[ 'receivedtime' ] );
			$html .= '	<div><b>' . $message[ 'sender' ] . '</b>:<br />' . $message[ 'msg' ] . '<br />' . $minutesAgo . ' geleden</div><br />';
		}
				
		$html .= 	'</div>';
		$html .=	'<div id="center-column" style="width: 440px; float: left; margin-left: 15px;">';
		
		$html .=	'	<div class="top-bar">';
		$html .=	'		<h1>Verzonden berichten</h1>';
		$html .=	'	</div>';
				
		
		foreach( $send as $messageId => $message )
		{
			$minutesAgo = $this->formatTime( $message[ 'senttime' ] );
			$html .= '	<div><b>' . $message[ 'receiver' ]; 
			
			if($message['status'] != 'delivered')
			{
				$html .= ' <span style="color: red;">' . $message['status'] . '</span>';
			}
			else
			{			
				$html .= ' <span style="color: green;">' . $message['status'] . '</span>';
			}
			
			if($message['person'] != '' )
			{
				$html .= ' (' . $message['person'] . ')';
			}
			
			$html .= '</b>:<br />' . $message[ 'msg' ] . '<br />' . $minutesAgo . ' geleden</div><br />';
		}
				
		$html .= 	'</div>';

		return $html;
	}
	
	private function formatTime ( $time )
	{
		$formattedString = '';
		
		$minutes = round( abs( round( time() - strtotime( $time ) ) ) / 60 );
		
		if( $minutes >= 60 )
		{
			$formattedString .= floor( $minutes / 60 ) . ' uur, ';
		}
		$formattedString .= ( $minutes % 60 ) . ' minuten';
		
		return $formattedString;
	}
}
?>