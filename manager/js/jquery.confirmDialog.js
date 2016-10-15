function showConfirmDialog( postUrl, refreshUrl, refreshSelector, title, text )
{
	function closeFormDialog()
	{
		$( '#confirmDialog' ).dialog( 'close' );
		$( '#confirmDialog' ).dialog( 'destroy' );
	}

	$( '#confirmDialog' ).dialog(
	{
		autoOpen:	false,
		width:		400,
		title:		title,
		modal:		true, 
		buttons:
		{
			'Ok': function()
			{
				$( this ).parent().find( ".ui-dialog-buttonpane" ).html( '<div id="dialogLaden"><p><img src="images/loading.gif" width="16" height="16" alt="" /> Laden...</p></div>' );

				$( '#confirmDialog' ).load( postUrl, {}, function( responseText, textStatus, XMLHttpRequest )
				{
					if( responseText.length > 3 )
					{
						$( '#confirmDialog' ).html( responseText );
					}
					else
					{
						$( refreshSelector ).load( refreshUrl ); 
						closeFormDialog();
					}
				} );
			},
			'Cancel': function()
			{
				closeFormDialog();
			}
		}
	} );

	$( '#confirmDialog' ).html( text );
	$( '#confirmDialog' ).dialog( 'open' );
}