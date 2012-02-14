$( document ).ready( function() {

$( '.content_page' ).hide(); 

$( 'h3.linked' ).click( function() {
$( '.content_page' ).show(); 

/*
	$( this ).next( '.content_page' ).slideToggle( 'fast' )
	.siblings( 'div:visible' ).slideUp( 'fast' );
*/
});

});
/*
	//Hide (Collapse) the toggle containers on load
	$( ".content_page" ).hide(); 

	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	$( "h3.linked" ).click( function() {
		$(this).toggleClass("active").next().slideToggle("slow");
		//return false; Prevent the browser jump to the link anchor
	});
	
	
//Set default open/close settings
$('.hide').hide(); //Hide/close all containers
$('.acc_trigger:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container

//On Click
$('.acc_trigger').click(function(){
	if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
		$('.acc_trigger').removeClass('active').next().slideUp(); //Remove all "active" state and slide up the immediate next container
		$(this).toggleClass('active').next().slideDown(); //Add "active" state to clicked trigger and slide down the immediate next container
	}
	return false; //Prevent the browser jump to the link anchor
});	
*/



var content_links = ( "#show_development, #show_marketing, #show_company, #show_blog" );
var contents_html = ( "#development_html, #marketing_html, #company_html, #blog_html" );

var myJSONcontent = ( "#development_html": "#show_development", "#marketing_html": "#show_marketing", "#company_html": "#show_company", "#blog_html": "#show_blog" );

function set_content_info( content_link, content_html )
{
this.content_link = content_link;
this.content_html = content_html;
}

var content_arr = new Array();
content_arr[0] = new set_content_info( '#show_development', '#development_html' );
content_arr[1] = new set_content_info( '#show_marketing', '#marketing_html' );
content_arr[2] = new set_content_info( '#show_company', '#company_html' );
content_arr[3] = new set_content_info( '#show_blog', '#blog_html' );

for ( var x=0; x < 4; x++ ) {
$( content_arr[x]['content_link'] ).click( function() {
	$( content_arr[x]['content_html'] ).removeClass( 'hide' )
});
}
