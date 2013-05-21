jQuery( function( $ ) {
	var $pCactions = $( '#p-cactions' );
	$pCactions.find( 'h5 a' )
		// For accessibility, show the menu when the hidden link in the menu is clicked (bug 24298)
		.click( function( e ) {
			$pCactions.find( '.menu' ).toggleClass( 'menuForceShow' );
			e.preventDefault();
		})
		// When the hidden link has focus, also set a class that will change the arrow icon
		.focus( function() {
			$pCactions.addClass( 'vectorMenuFocus' );
		})
		.blur( function() {
			$pCactions.removeClass( 'vectorMenuFocus' );
		});
	
	// Breadcrumb slider to reveal personal toolbar options
	window.cpOpen = false;
	$("#nav-control-panel a").click(function(e) {
		e.preventDefault();
		if (window.cpOpen == false)
		{
			$("#breadcrumbs").animate({
				marginTop: '-25px'
			}, {
				duration : 400,
				complete: function() {
					window.cpOpen = true;
					$("#nav-control-panel span").addClass("control-button-up");
					$("#nav-control-panel span").removeClass("control-button-down");
				}
			});
		} else {
			$("#breadcrumbs").animate({
				marginTop: '-50px'
			}, {
				duration : 400,
				complete: function() {
					window.cpOpen = false;
					$("#nav-control-panel span").addClass("control-button-down");
					$("#nav-control-panel span").removeClass("control-button-up");
				}
			});
		}
	});
});
