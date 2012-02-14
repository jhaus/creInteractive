<?php

// Using hooks is absolutey the smartest, most bulletproof way to implement things like plugins,
// custom design elements, and ads. You can add your hook calls below, and they should take the 
// following form:
// add_action('thesis_hook_name', 'function_name');
// The function you name above will run at the location of the specified hook. The example
// hook below demonstrates how you can insert Thesis' default recent posts widget above
// the content in Sidebar 1:
// add_action('thesis_hook_before_sidebar_1', 'thesis_widget_recent_posts');

// Delete this line, including the dashes to the left, and add your hooks in its place.

/* NO CODE BELOW */
remove_action('thesis_hook_footer', 'thesis_attribution');


/**
 * function custom_bookmark_links() - outputs an HTML list of bookmarking links
 * NOTE: This only works when called from inside the WordPress loop!
 * SECOND NOTE: This is really just a sample function to show you how to use custom functions!
 *
 * @since 1.0
 * @global object $post
*/

function custom_bookmark_links() {
	global $post;
?>
<ul class="bookmark_links">
	<li><a rel="nofollow" href="http://delicious.com/save?url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>" onclick="window.open('http://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>', 'delicious', 'toolbar=no,width=550,height=550'); return false;" title="Bookmark this post on del.icio.us">Bookmark this article on Delicious</a></li>
</ul>
<?php
}

/*
// Password Protected Page Message
function custom_password_form($form) {
  $subs = array(
	'#<p>This post is password protected. To view it please enter your password below:</p>#' => '<p>Enter your password for access to reports.</p>',
    '#<input(.*?)type="submit"(.*?) />#' => '<input$1type="submit"$2 name="Submit" value="Submit" class="form_submit" />'
  );

  echo preg_replace(array_keys($subs), array_values($subs), $form);
}
//via http://wordpress.org/support/topic/293795?replies=8
add_filter('the_password_form', 'custom_password_form');
*/

function cre_password_form() {
	global $post;
	//$content = remove_filter( 'the_content', $content );
	$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
	$output = '<form class="protected-post-form" action="' . get_option('siteurl') . '/wp-pass.php" method="post">
	' . __( "Enter your password for document access:" ) . '
	<p><label for="' . $label . '"></label><input name="post_password" id="' . $label . '" type="password" size="20" /><input type="submit" name="Submit" class="form_submit" value="' . esc_attr__( "Submit" ) . '" /></p>
	</form>
	';
	return $output;
}
add_filter( 'the_password_form', 'cre_password_form' );

/*
if ( post_password_required($post) ) {
	global $post;
	$content = remove_filter( 'the_content', $content, 999 );
	return $content;
}

function byebyebreak() {
global $post;
	if ( post_password_required($post) ) {
		$content = str_replace( '&lt;br&gt;', '', $content );
		return $content;
	}
	else {
		return $content;
	}
}
add_filter( 'the_content', 'byebyebreak', 20 );
*/