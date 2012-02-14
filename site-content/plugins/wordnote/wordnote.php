<?php
/*
Plugin Name: WordNote
Plugin URI: http://codemonkey.nu/wordnote/
Description: Simple way to make notes on the WordPress dashboard.
Version: 1.1
Author: Marco Hyyryläinen
Author URI: http://codemonkey.nu/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3689633
*/
		
// Create the function to output the contents of our Dashboard Widget
function wordnote_dashboard_widget_function() {
	
	// Define path to textfile.txt
	$path_to_textfile = WP_PLUGIN_DIR . '/wordnote/textfile.txt';
	
	// Create textfile.txt if it doesn´t exist
	if (file_exists($path_to_textfile)) { } else {        
    	$open_file = fopen($path_to_textfile, "w");
		$write = fwrite($open_file, "Welcome to WordNote! \n/Marco - CodeMonkey.nu");
		fclose($open_file);
	}   
	
	// If ?edit=wordnote is called
	if ($_GET['edit'] == "wordnote") {
		
		// Save to textfile.txt
		$open_file = fopen($path_to_textfile, "w");
		$write = fwrite($open_file, stripslashes($_POST['note']));
		fclose($open_file);

		// Hack to redirect to /wp-admin/index.php
		echo "<script language=\"javascript\" type=\"text/JavaScript\">window.location=\"index.php\"</script>\n";
		
	// If not, display form	
	} else {
		
		// Load textfile.txt
		$text = file_get_contents($path_to_textfile);

		// Display form
		echo "<form name=\"form\" method=\"post\" action=\"?edit=wordnote\">\n";
		echo "<textarea name=\"note\" style=\"width: 100%; height: 100px\">".$text."</textarea><br />\n";
		echo "<input type=\"submit\" name=\"save\" value=\"Save\" class=\"button\" style=\"float: right;\" /><br />\n";
		echo "</form>\n";
		
		// Unset $text (textfile.txt)
		unset($text);
	}
} 

// Create the function use in the action hook
function wordnote_add_dashboard_widgets() {
	wp_add_dashboard_widget('wordnote_dashboard_widget', 'WordNote', 'wordnote_dashboard_widget_function');	
} 

// Hoook into the 'wp_dashboard_setup' action to register our other functions
add_action('wp_dashboard_setup', 'wordnote_add_dashboard_widgets' );
?>