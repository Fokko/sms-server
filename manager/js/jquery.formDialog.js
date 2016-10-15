/**
 *	formUrl: the url for loading the form
 *	postUrl: the url for submitting the form
 *	refreshUrl: the url for refreshing the table
 *	refreshSelector: the element that will contain the new table
 *	title: the title of the dialog
 *	targetSelector: the element that will contain the result/error
 */
function showFormDialog( formUrl, postUrl, refreshUrl, refreshSelector, title, targetSelector )
{
	function closeFormDialog()
	{
		$( "#form_dialog" ).dialog( "destroy" );
		$( "#dialog_form" ).html( "" );
		$( "#dialog_error" ).html( "" );
	}

	// Validate input
 	if ( targetSelector == null )
	{
 		targetSelector = "#dialog_error";
	}

	$( function() 
	{
		$.ajax( 
		{
			type:		"GET",
			cache:		false,
			dataType:	"html",
			url:		formUrl,
			success:	function( data )
			{
				$( "#dialog_error" ).html( "" );
				$( "#dialog_form" ).html( data );
				$( "#form_dialog" ).dialog( "open" );
			}
		} );

		$( "#form_dialog" ).dialog(
		{
			width: 600,
			title: title,
			modal: true, 
			autoOpen: false, 
			buttons: 
			{
				"Ok": function()
				{
					$( this ).parent().find( ".ui-dialog-buttonpane" ).html( '<div id="dialogLaden"><p><img src="images/loading.gif" width="16" height="16" alt="" /> Laden...</p></div>' );

					$( "#dialog_form form" ).ajaxSubmit(
					{
						target:		targetSelector,
						url:		postUrl,
						success:	function( responseText, statusText ) 
						{
							if( refreshUrl == 'refresh' )
							{
								window.location.reload();
							}
							else
							{
								$.ajax({ url: refreshUrl, success: function( data )
									{
										if( refreshSelector == "folderImage" )
										{
											window.location = data;
										}
										else if( parseInt( refreshSelector ) > 0 )
										{
											$( "span#" + refreshSelector ).remove();
											$( "#folderContainer div.productenContainer" ).append( data );

											/* 
											 * De folder container draggable maken
											 * De folder container resizable maken
											 */
											re_activateDraggable( "folder", true );
											re_activateResizable( "folder", true );

											var $newTarget = $( "#folderContainer span#" + refreshSelector );

											/* 
											 * Alle producten (assets) binnen de folder container controleren op afbeeldingen
											 * Deze afbeeldingen draggable maken als dit nodig is
											 */
											$( "#folderContainer span" ).each( function()
											{
												if( $( this ).find( "ul > li" ).length )
												{
													re_activateDraggable( $( this ), true );
													re_activateResizable( $( this ), true );
												}
											} );
										}
										else
										{
											$( refreshSelector ).html( data );
										}

										if ( responseText == "" )
										{
											closeFormDialog();
										}
									}
								});
							}
						}
					} );
				},
				"Cancel": function()
				{
					closeFormDialog();
				}
			}
		} );
	} );
}