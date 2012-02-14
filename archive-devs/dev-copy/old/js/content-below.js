$( document ).ready( function() {

var contents_html = ( '#development_html, #marketing_html, #company_html, #blog_html' );

$( contents_html ).hide();

	$( 'h3.development' ).click( function() {
		window.location.hash = 'development';
		$( 'h3' ).siblings().removeClass( 'active' );
		$( '#development_html' ).slideToggle('slow');
		$( '#development_html' ).siblings( '.content_page' ).hide('slow');
		$(this).toggleClass( 'active' );
	});
	
	$( 'h3.marketing' ).click( function() {
		window.location.hash = 'marketing';
		$( 'h3' ).siblings().removeClass( 'active' );
		$( '#marketing_html' ).slideToggle('slow');
		$( '#marketing_html' ).siblings( '.content_page' ).hide('slow');
		$(this).toggleClass( 'active' );
	});
	$( 'h3.company' ).click( function() {
		window.location.hash = 'company';
		$( 'h3' ).siblings().removeClass( 'active' );
		$( '#company_html' ).slideToggle('slow');
		$( '#company_html' ).siblings( '.content_page' ).hide('slow');
		$(this).toggleClass( 'active' );
	});
	$( 'h3.blog' ).click( function() {
		window.location.hash = 'blog';
		$( 'h3' ).siblings().removeClass( 'active' );
		$( '#blog_html' ).slideToggle('slow');
		$( '#blog_html' ).siblings( '.content_page' ).hide('slow');
		$(this).toggleClass( 'active' );
	});
	
});

/*
	if ( click == true ) {
		$( 'html,body' ).animate( {scrollTop: $( "#nav" ).offset().top - 16 - 40}, 200, "swing" );
	}

if( window.location.hash === 'development' ||
	window.location.hash === 'marketing' ||
	window.location.hash === 'company' ||
	window.location.hash === 'blog' ) {
		loadContent( window.location.( '#menu' ) );
	}

$( 'h3 > ul' ).window();
$( 'h3 > ul' ).bind( 'tabsshow', function( event, ui ) {
    window.location.hash = ui.window.hash;
});

var content_links = ( "#show_development, #show_marketing, #show_company, #show_blog" );
var contents_html = ( "#development_html, #marketing_html, #company_html, #blog_html" );
var myJSONcontent = ( "#development_html": "#show_development", "#marketing_html": "#show_marketing", "#company_html": "#show_company", "#blog_html": "#show_blog" );

$( document ).ready( function() {

$( '.content_page' ).hide();

	$( 'h3.development' ).click( function() {
		$( '.content_page.item-1' ).slideDown(400);
		$( '.content_page.item-1' ).siblings( '.content_page' ).hide();
	});
	
	$( 'h3.marketing' ).click( function() {
		$( '.content_page.item-2' ).slideDown(400);
		$( '.content_page.item-2' ).siblings( '.content_page' ).hide();
	});
	$( 'h3.company' ).click( function() {
		$( '.content_page.item-3' ).slideDown(400);
		$( '.content_page.item-3' ).siblings( '.content_page' ).hide();
	});
	$( 'h3.blog' ).click( function() {
		$( '.content_page.item-4' ).slideDown(400);
		$( '.content_page.item-4' ).siblings( '.content_page' ).hide();
	});
});
*/

/*
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

 onClick.( content_links ) = $( content_html ).addClass( 'hide' );

 onClick = $( content_html ).addClass( 'hide' ).( content_links );
 
$( content_links ).click( function() {
	$( content_html ).removeClass( 'hide' )
});

var content_links = document.getElementsByTagName("nav ul li h3"); // returns a nodeList
var arr = jQuery.makeArray( content_links );
*/
/*
<!--
$( content_html ).hide();

$( document ).delegate( "nav a", click, function( e ) {
	e.preventDefault();
	$( content_html ).hide();
	$( $( this ).attr("h3") ).slideToggle();
} );
-->*/