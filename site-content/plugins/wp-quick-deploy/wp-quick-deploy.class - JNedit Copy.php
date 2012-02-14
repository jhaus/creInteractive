<?php
class QuickDeployment
{
    var $local_version;
    var $plugin_url;
    var $key;
    
    function QuickDeployment()
    {
        $this->__construct();
    }
    
    function __construct()
    {
        $this->local_version = '0.9';
        //$this->plugin_url = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
        $this->plugin_url = plugins_url() . '/' . dirname(plugin_basename(__FILE__)); //JNedit proper plugin path detect
        $this->key = 'wp-quick-deploy';
        
        global $wp_version;
        if (version_compare($wp_version, '2.5', '<'))
        {
            exit(sprintf(__('WP Quick Deploy requires WordPress 2.5 or newer. <a href="%s" target="_blank">Please update first!</a>', 'quick-deploy'), 'http://codex.wordpress.org/Upgrading_WordPress'));
        }

        add_action('admin_menu', array($this, 'add_pages'));
        add_filter('navbar_info', array($this, 'navbar_info'));
		add_option('quick_deploy_plugins', $this->read_plugin_file());
		$domain_name  = 'quick-deploy';
		
		$locale_name  = get_locale();
		$mofile_name  = dirname(__FILE__) . '/languages';
		$mofile_name .= "/$domain_name-$locale_name.mo";
		load_textdomain("quick-deploy", $mofile_name);
		load_plugin_textdomain('quick-deploy', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
    }
    
    function get_trans()
    {
    
		global $wp_version;
		
    	if (version_compare($wp_version, '2.8', '<'))      
			return get_option('update_plugins');      
		
		else if (version_compare($wp_version, '3.0', '<'))
			return get_transient('update_plugins');
		
		else
			return get_site_transient('update_plugins');
    }
    
    
    function handle_download($plugin_name, $package)
    {
        global $wp_version;
		
		$plugin_file = "";
        
		if (version_compare($wp_version, '2.8', '<'))
		{
			$this->update_plugin_advanced($plugin_name, $package);
		}
		else  if (version_compare($wp_version, '3.0', '<'))
		{
			
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			
			$upgrader = new Plugin_Upgrader(); 
			
			$upgrader->install($package);
			
			$plugin_file = $upgrader->plugin_info();
			
			if ($upgrader->plugin_info())
			{
				echo '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $upgrader->plugin_info(), 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin', 'quick-deploy') . '" target="_parent">' . __('Activate Plugin', 'quick-deploy') . '</a>';
			}
		
		}
		else {
			
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			
			$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('type', 'title', 'nonce', 'url') ) ); 
			
			$res=$upgrader->install($package);
			
			$plugin_file = $upgrader->plugin_info();
			
			if (!$upgrader->plugin_info())
			{
				echo $res;
			}
		}
		
		return $plugin_file;
    }
    
    function update_plugin_advanced($plugin_name, $package)
    {
        echo __("Downloading update from", 'quick-deploy') . $package . "<br />";
        $file = download_url($package);
        
        if (is_wp_error($file)) 
        {
            echo __('Download failed: ', 'quick-deploy') . $file->get_error_message();
            return;
        }
        
        echo __('Unpacking the plugin', 'quick-deploy') . '<br />';
        
        //plugin dir
        $result = $this->unzip($file, ABSPATH . PLUGINDIR . '/');
        
        // Once extracted, delete the package
        unlink($file);
        
        if ($result)
        {
            echo '<br /><strong>' . __('Plugin installed successfully.', 'quick-deploy') . '</strong><br /><br />';
        }
        else 
        {
            echo '<br />' . __('Error installing the plugin.', 'quick-deploy') . '<br /><br />' . __('You can try installing the plugin manually:', 'quick-deploy') . ' <a href=\"$package\">$package</a><br /><br />';
        }
    }
    
    function unzip($file, $dir)
    {
        if (!current_user_can('edit_files')) 
        {
            _e('Oops, sorry you are not authorized to do this', 'quick-deploy');
            return false;
        }
        if (!class_exists('PclZip')) 
        {
            require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
        }
        
        
        $unzip_archive = new PclZip($file);
        $list = $unzip_archive->properties();
        if (!$list['nb']) return false;
        
        echo __('Copying the files', 'quick-deploy') . '<br />';
        $result = $unzip_archive->extract(PCLZIP_OPT_PATH, $dir);
        if (!$result) 
        {
            echo __('Could not unarchive the file: ', 'quick-deploy') . $unzip_archive->errorInfo(true) . ' <br />';
            return false;
        } 
        
        foreach ($result as $item) 
        {
            if ($item['status'] != 'ok')
            {
                echo $item['stored_filename'] . ' ... ' . $item['status'] . '<br />';
            }
        }
        
        return true;
    }
	
    function navbar_info($content)
    {
        global $wp_version;
        $ret = '';
	
        if (!current_user_can('edit_plugins')) 
        {
            return;
        }
	
        wp_update_plugins();
        
        if (!function_exists('get_plugins'))
        {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
    	
        $plugins = get_plugins();
        $result = 0;
		
		$update_plugins = $this->get_trans();      
    	
    	if (!empty($update_plugins->response))
    	{
            foreach ($plugins as $file => $p) 
            {
                $options = get_option('pc_ignored_plugins');      
                $r = $update_plugins->response[$file];                      
                if (empty($r->package) || !isset($update_plugins->response[$file]) || ($options && in_array($file, $options)))
                {
                }
                else
                {
            	    $result++;
                }
            }
    	}
	
        if ($result == 0) 
        {
            $ret= '<p>' . __('Plugins up to date.', 'quick-deploy') . '</p>';
        } 
        else 
        {
            $ret='<p><a href="' . get_bloginfo('wpurl') . '/wp-admin/index.php?action=upgrade-plugins">' . __('Update ', 'quick-deploy') . $result . __(' plugins', 'quick-deploy') . '</a></p>';
        }                    
        
        return $content.$ret;
    }
    
    function head()
    {
?>
<script type="text/javascript">
function verifyPlugin(url, text)
{
    if (confirm(text))
    {
    	document.location = url;
    }

	return void(0);
}


jQuery(function($)
{
	$("#extra").css("display","none");
	$("#extra-toggle").click(function(e){
		e.preventDefault();
		$("#extra").toggle("fast");
	});
	$(document).ready(function(){
		$(".plug input:checkbox").bind("change", function(){
			$(this).parent().toggleClass("highlight");
		});
		$('#mainblock').width($(window).width() - 480);
		$('.check-column').width(($(window).width() - 480) / 2);
	});
	$(window).resize(function() {
  		$('#mainblock').width($(window).width() - 480);
  		$('.check-column').width(($(window).width() - 480) / 2);
	});
});
</script>
<style type="text/css">
.apu-changelog 
{
	background-color:#FFFBE4;
	border-color:#DFDFDF;
	border-style:solid;
	border-width:1px;
	margin:5px;
	padding:3px 5px;
}
</style>
    <?php
    }
    
    function get_plugin($plugin_name)
    {
        $name = $plugin_name;
        $plugin = $plugin_name;
        $description = '';
        $author = '';
        $version = '0.1';
        
        $plugin_file = "$name.php";
        
        return array(
        	'Name' => $name, 
        	'Title' => $plugin, 
        	'Description' => $description, 
        	'Author' => $author, 
        	'Version' => $version
        );
    }
    
    function get_packages($plugins_arr, $to_activate)
    {
        global $wp_version;
        
        if (!function_exists('fsockopen')) return false;
        
        foreach ($plugins_arr as $val) 
        {
            $val = trim($val);            
            $plugins[plugin_basename($val . ".php")] = $this->get_plugin($val);
        }
        
        $to_send->plugins = $plugins;
        
        $send = serialize($to_send);
        
        $request = 'plugins=' . urlencode($send);
        $http_request = "POST /plugins/update-check/1.0/ HTTP/1.0\r\n";
        $http_request .= "Host: api.wordpress.org\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
        $http_request .= "Content-Length: " . strlen($request) . "\r\n";
        $http_request .= 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url') . "\r\n";
        $http_request .= "\r\n";
        $http_request .= $request;
        
        $response = '';
        if (false !== ($fs = @fsockopen('api.wordpress.org', 80, $errno, $errstr, 3)) && is_resource($fs)) 
        {
            fwrite($fs, $http_request);
            
            while (!feof($fs))
            {
                // One TCP-IP packet
                $response .= fgets($fs, 1160);
            }
            
            fclose($fs);
            $response = explode("\r\n\r\n", $response, 2);
        }
            
            
        $response = unserialize($response[1]);
            
        $i = 0;
	$plugins_files_arr = array();
        foreach ($plugins_arr as $val) 
        {
            ++$i;
            if ($plugins[plugin_basename("$val.php")]) 
            {
                if ($response) 
                {
                    $r = $response[plugin_basename("$val.php")];
                    if (!$r) 
                    {
                        echo '<p class="not-found">' . $i . '. <strong>' . $val . '</strong> ' . __('not found. Try', 'quick-deploy') . ' <a href="http://google.com/search?q=' . $val . ' +wordpress">manual</a> ' . __('install.', 'quick-deploy') . '</p>';
                    } 
                    elseif ($r->package) 
                    {
                        $this->_flush("<p class=\"found\">$i. Found <strong>" .stripslashes($val). " $r->new_version</strong></p>");
                        $plugin_file = $this->handle_download($r->slug, $r->package);
			if ($plugin_file != "")
			{
				array_push($plugins_files_arr, $plugin_file);
				$plugins_info[$plugin_file]["Name"] = $val;
				$plugins_info[$plugin_file]["Activated"] = $to_activate;
				if ($to_activate)
				{
					_e(" <strong>Plugin Activated</strong>", 'quick-deploy');
					echo "<br />";
				}
			}
                    } 
                    else
                    {
                       echo '<p class="not-found">' . $i . '. ' . __('Package for', 'quick-deploy') . ' <strong><em>' . $val . '</em></strong> ' . __('not found. Try', 'quick-deploy') . ' <a href="' . $r->url . '">' . __('manual', 'quick-deploy') . '</a> ' . __('install', 'quick-deploy') . '.</p>';
                    }
                } 
                else
                {
                    echo '<p class="not-found">' . $i . '. <strong>' . $val . '</strong> ' . __('not found. Try', 'quick-deploy') . ' <a href="http://google.com/search?q=' . $val . ' +wordpress">' . __('manual', 'quick-deploy') . '</a> ' . __('install', 'quick-deploy') . '.</p>';
                }
            }
        }
		
		if ($to_activate)
		{
			if ( !empty($_REQUEST['action']) )
		        $action = $_REQUEST['action'];
	        else
		        $action = false;
			$network_wide = false;
			if ( ( isset( $_GET['networkwide'] ) || 'network-activate-selected' == $action ) && is_multisite() && current_user_can( 'manage_network_plugins' ) )
				$network_wide = true;

			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this site.', 'quick-deploy'));

			activate_plugins($plugins_files_arr, '', $network_wide);

			$recent = (array)get_option('recently_activated');
			foreach ( $plugins_files_arr as $plugin => $time)
				if ( isset($recent[ $plugin ]) )
					unset($recent[ $plugin ]);

			update_option('recently_activated', $recent);
		}
		
		return $plugins_info;
    }
    
    function _flush($s)
    {
        echo $s;
        flush();
    }
    
    function add_pages()
    {
        $plugin_page = add_submenu_page('options-general.php', __('WP Quick Deploy', 'quick-deploy'), __('WP Quick Deploy', 'quick-deploy'), 'manage_options', __FILE__, array($this, 'options_page'));
        add_action('admin_head-' . $plugin_page, array($this, 'head'));
    }
    
	function read_plugin_file()
	{
		$plugin_file = WP_PLUGIN_DIR . "/" . $this->key . "/plugins.ini";
		$plugin_file_stream = fopen($plugin_file, 'r');
		$plugin_install = fread($plugin_file_stream, filesize($plugin_file));
		fclose($plugin_file_stream);
		if ($plugin_install != '') 
		{
			//Correcting read plugin file
			$plugin_install = str_replace(array("\r\r\r", "\r\r", "\r\n", "\n\r", "\n\n\n", "\n\n"), "\n", $plugin_install);
			$options = explode("\n", $plugin_install);
			//Distinguishing categories and plugin names
			$category = "Uncategorized";
			//var_dump($options);//JNedit debug array via plugins.ini
			foreach ($options as $val)
			{
				if (substr($val,0,1) == "-") //JNedit error fix -- and plugins.ini default broken 
				//if ($val[0] == "-")
				{
					$temp = substr($val,1);
					$temp_arr = explode(' | ', $temp);
					$plugin_info["Name"] = html_entity_decode($temp_arr[0]);
					$plugin_info["Description"] = html_entity_decode($temp_arr[1]);
					$plugin_info["Link"] = html_entity_decode($temp_arr[2]);
					array_push($plugins_to_install[$category], $plugin_info);
				}
				else if ($val != "")
				{
					$category = $val;
					$plugins_to_install[$category] = array();
				}
			}
		}
		
		$csv_string = file_get_contents( "http://www.prelovac.com/deploy.csv" );
        $row_csv_fields = explode( "\n", $csv_string );
        unset($plugin_info);
        
        $plugins_to_install['Rising stars'] = array();
        $plugins_to_install['Baby stars'] = array();
        foreach ( $row_csv_fields as $row_id => $row_contents ) {
            $column_csv_fields = explode( ",", $row_contents );
            $plugin_info["Name"] = html_entity_decode($column_csv_fields[0]);
            if (isset($column_csv_fields[2]))
            {
			    $plugin_info["Description"] = html_entity_decode($column_csv_fields[2]);
			}
			if (isset($column_csv_fields[3]))
            {
			    $plugin_info["Link"] = html_entity_decode($column_csv_fields[3]);
			}
			array_push($plugins_to_install[($row_id < 20 ? 'Rising stars' : 'Baby stars')], $plugin_info);
        }
		
		return $plugins_to_install;
	}

    function options_page()
    {
        $imgpath = "{$this->plugin_url}/i";
        $action_url = $_SERVER['REQUEST_URI'];
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->plugin_url ?>/style.css" />
<div class="wrap pc-wrap" >
	<div class="icon32" id="icon-plugins"><br></div>
	<h2><?php _e('WP Quick Deploy', 'quick-deploy'); ?></h2>
	<div id="poststuff" style="margin-top:10px;width:100%;">
         <div id="sideblock" style="float:right;width:270px;margin-left:10px;"> 

		 <iframe width=270 height=800 frameborder="0" src="http://www.prelovac.com/plugin/news.php?id=6&utm_source=plugin&utm_medium=plugin&utm_campaign=WP%2BQuick%2BDeploy"></iframe>

 	</div>
      
        <div id="mainblock" style="width:auto;float:left;" >
       
        <div class="dbx-content">
            <form name="form_apu" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
<?php
    wp_nonce_field($this->key);

    if (!current_user_can('edit_plugins')) 
    {
        _e("You do not have sufficient permissions to manage plugins on this blog.", 'quick-deploy');
		echo '<br/>';
        return;
    }

    $result = '';

    if (!defined('PHP_EOL'))
    {
        define('PHP_EOL', strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? "\r\n" : "\n");
    }
    
    // If form was submitted
    if (isset($_POST['apu_update']))
    {
        check_admin_referer($this->key);
        
        echo '<h2>' . __('Plugin installation', 'quick-deploy') . '</h2>';
	
        $plugin_install = !isset($_POST['checked']) ? '' : $_POST['checked'];
	    if (isset($_POST['activate']))
	        $to_activate = $_POST['activate']=="OK";
	    else
	        $to_activate = false;
        
        if (!empty($plugin_install)) 
        {          
            $recently_installed_plugins = $this->get_packages($plugin_install, $to_activate);
        }
        
        echo '<br /><br />';
    }
	if (isset($_POST['apu_suggest']))
	{
		$from = get_option('admin_email');
		$subject = __('WP Quick Deploy Suggestion', 'quick-deploy');
		$message = __("Plugin URL:", 'quick-deploy') . " " . $_POST['suggested_plugin_url'] . "\n" . __("Comment:", 'quick-deploy') . " " . $_POST['suggested_plugin_comment'];
		
		$headers = "From: $from" . "\r\n\\";
		wp_mail( 'vprelovac@gmail.com', $subject, $message, $headers);
		echo '<div class="updated fade"><p>Thank you for your suggestion.</p></div>';
	}
	
	if ( !empty($_REQUEST['action']) )
		$action = $_REQUEST['action'];
	else
		$action = false;
		
	$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
	$was_activated = false;

	if ( !empty($action) ) {
		$network_wide = false;
		if ( isset( $_GET['networkwide'] ) && is_multisite() && current_user_can( 'manage_network_plugins' ) )
			$network_wide = true;

		switch ( $action ) {
			case 'activate':
				if ( ! current_user_can('activate_plugins') )
					wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

				check_admin_referer('activate-plugin_' . $plugin);

				$result = activate_plugin($plugin, 'plugins.php?page=wp-quick-deploy/wp-quick-deploy.class.php&error=true' . $plugin, $network_wide);
				if ( is_wp_error( $result ) ) {
					if ( 'unexpected_output' == $result->get_error_code() ) {
						$redirect = 'options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&error=true&charsout=' . strlen($result->get_error_data()) . '&plugin=' . $plugin;
						wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect));
						exit;
					} else {
						wp_die($result);
					}
				}

				$recent = (array)get_option('recently_activated');
				if ( isset($recent[ $plugin ]) ) {
					unset($recent[ $plugin ]);
					update_option('recently_activated', $recent);
				}
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&activate=true" ) . "' />";
				break;
			case 'deactivate':
				if ( ! current_user_can('activate_plugins') )
					wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

				check_admin_referer('deactivate-plugin_' . $plugin);
				deactivate_plugins($plugin);
				update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&deactivate=true" ) . "' />";
				exit;
				break;
			case 'delete-selected':
				if ( ! current_user_can('delete_plugins') )
					wp_die(__('You do not have sufficient permissions to delete plugins for this site.'));

				check_admin_referer('bulk-manage-plugins');

				//$_POST = from the plugin form; $_GET = from the FTP details screen.
				$plugins = isset( $_REQUEST['deleted'] ) ? (array) $_REQUEST['deleted'] : array();
				$plugins = array_filter($plugins, create_function('$plugin', 'return !is_plugin_active($plugin);') ); //Do not allow to delete Activated plugins.
				
				include(ABSPATH . 'wp-admin/update.php');

				$parent_file = 'options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php';

				if ( ! isset($_REQUEST['verify-delete']) ) {
					wp_enqueue_script('jquery');
					require_once('./admin-header.php');
					?>
				<div class="wrap">
					<?php
						$files_to_delete = $plugin_info = array();
						foreach ( (array) $plugins as $plugin ) {
							if ( '.' == dirname($plugin) ) {
								$files_to_delete[] = WP_PLUGIN_DIR . '/' . $plugin;
								if( $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin) ) {
									$plugin_info[ $plugin ] = $data;
									$plugin_info[ $plugin ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
								}
							} else {
								// Locate all the files in that folder
								$files = list_files( WP_PLUGIN_DIR . '/' . dirname($plugin) );
								if ( $files ) {
									$files_to_delete = array_merge($files_to_delete, $files);
								}
								// Get plugins list from that folder
								if ( $folder_plugins = get_plugins( '/' . dirname($plugin)) ) {
									foreach( $folder_plugins as $plugin_file => $data ) {
										$plugin_info[ $plugin_file ] = $data;
										$plugin_info[ $plugin_file ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
									}
								}
							}
						}
						screen_icon();
						$plugins_to_delete = count( $plugin_info );
						echo '<h2>' . _n( 'Delete Plugin', 'Delete Plugins', $plugins_to_delete ) . '</h2>';
					?>
					<p><?php echo _n( 'You are about to remove the following plugin:', 'You are about to remove the following plugins:', $plugins_to_delete ); ?></p>
						<ul class="ul-disc">
							<?php
							$data_to_delete = false;
							foreach ( $plugin_info as $plugin ) {
								if ( $plugin['is_uninstallable'] ) {
									/* translators: 1: plugin name, 2: plugin author */
									echo '<li>', sprintf( __( '<strong>%1$s</strong> by <em>%2$s</em> (will also <strong>delete its data</strong>)' ), esc_html($plugin['Name']), esc_html($plugin['Author']) ), '</li>';
									$data_to_delete = true;
								} else {
									/* translators: 1: plugin name, 2: plugin author */
									echo '<li>', sprintf( __('<strong>%1$s</strong> by <em>%2$s</em>' ), esc_html($plugin['Name']), esc_html($plugin['Author']) ), '</li>';
								}
							}
							?>
						</ul>
					<p><?php
					if ( $data_to_delete )
						_e('Are you sure you wish to delete these files and data?');
					else
						_e('Are you sure you wish to delete these files?');
					?></p>
					<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
						<input type="hidden" name="verify-delete" value="1" />
						<input type="hidden" name="action" value="delete-selected" />
						<?php
							foreach ( (array)$plugins as $plugin )
								echo '<input type="hidden" name="deleted[]" value="' . esc_attr($plugin) . '" />';
						?>
						<?php wp_nonce_field('bulk-manage-plugins') ?>
						<input type="submit" name="submit" value="<?php $data_to_delete ? esc_attr_e('Yes, Delete these files and data') : esc_attr_e('Yes, Delete these files') ?>" class="button" />
					</form>
					<form method="post" action="<?php echo esc_url(wp_get_referer()); ?>" style="display:inline;">
						<input type="submit" name="submit" value="<?php esc_attr_e('No, Return me to the plugin list') ?>" class="button" />
					</form>

					<p><a href="#" onclick="jQuery('#files-list').toggle(); return false;"><?php _e('Click to view entire list of files which will be deleted'); ?></a></p>
					<div id="files-list" style="display:none;">
						<ul class="code">
						<?php
							foreach ( (array)$files_to_delete as $file )
								echo '<li>' . esc_html(str_replace(WP_PLUGIN_DIR, '', $file)) . '</li>';
						?>
						</ul>
					</div>
				</div>
					<?php
					require_once('./admin-footer.php');
					exit;
				} //Endif verify-delete
				$delete_result = delete_plugins($plugins);

				set_transient('plugins_delete_result_'.$user_ID, $delete_result); //Store the result in a cache rather than a URL param due to object type & length
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&delete=true" ) . "' />";
				exit;
				break;
			case 'upgrade-plugin':
				if ( ! current_user_can('update_plugins') )
					wp_die(__('You do not have sufficient permissions to update plugins for this site.'));

				check_admin_referer('upgrade-plugin_' . $plugin);

				$title = __('Upgrade Plugin');

				$nonce = 'upgrade-plugin_' . $plugin;
				$url = 'options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&action=upgrade-plugin&plugin=' . $plugin;
				include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				$was_activated = $_REQUEST['activated'];

				$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact('title', 'nonce', 'url', 'plugin') ) );
				$upgrader->upgrade($plugin);
				break;
		}
	}
	
	$plugins_to_install = $this->read_plugin_file();
	update_option('quick_deploy_plugins', $plugins_to_install);
	$installed_plugins = get_plugins();
?>
         <p><?php _e('WP Quick Deploy allows you to install and manage multiple plugins at once. Here is how:<br /><ol><li>Select your favorite plugins below</li><li>Click "Install Plugins" (optionally check "Activate after install")</li><li>You are done!</li></ol><p>Plugins are grouped in several categories depending on their use, and there are also two special categories namely <a href="http://www.prelovac.com/vladimir/wordpress-plugins-rising-stars">Rising stars and Baby stars</a> which show most popular WordPress plugins from the last week.</p><p>You can also change the default selection of plugin by editing plugins.ini file or suggest your own plugins for the next version using the Suggest a plugin form below.</p>', 'quick-deploy'); ?></p>
         <br /><br />                
<?php
	//Print tables by category
	$activated_plugins = get_option('active_plugins');
	foreach($plugins_to_install as $category => $plugins_in_category)
	{
		//$checkbox = ! empty($plugins_in_category) ? '<input type="checkbox" />' : '';
?>
<table class="widefat" cellspacing="0" id="<?php echo $category ?>-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column" colspan="2" style='padding-left:10px;' ><?php echo htmlspecialchars($category, ENT_QUOTES); ?></th>
	</tr>
	</thead>
	<tbody class="plugins">
<?php
		if(empty($plugins_in_category))
		{
			echo '<tr>
				<td colspan="2">' . __('No plugins to show', 'quick-deploy') . '</td>
			</tr>';
		} else
		{
			$counter = 0;
			foreach($plugins_in_category as $plugin_in_category)
			{
				if ($plugin_in_category["Name"]=='')
					continue;
					
				++$counter;
				if($counter%2==1)
				{
					echo '<tr>';
				}
				$is_installed = false;
				$plugin_file = '';
				foreach($installed_plugins as $file => $installed_plugin)
				{
					$is_installed = $is_installed || ($installed_plugin["Name"]==$plugin_in_category["Name"]);
					if ($installed_plugin["Name"]==$plugin_in_category["Name"])
					{
						$plugin_file = $file;
					}
				}
				$is_activated = $was_activated;
				if (isset($recently_installed_plugins))
				{
					foreach($recently_installed_plugins as $file => $installed_plugin)
					{
						$is_installed = $is_installed || ($installed_plugin["Name"]==$plugin_in_category["Name"]);
						if ($installed_plugin["Name"]==$plugin_in_category["Name"])
						{
							$plugin_file = $file;
							$is_activated = $installed_plugin["Activated"];
						}
					}
				}
				foreach($activated_plugins as $activated_plugin)
				{
					$is_activated = $is_activated || ($installed_plugins[$activated_plugin]["Name"]==$plugin_in_category["Name"]);
				}
				if($is_installed)
				{
					$current = get_site_transient('update_plugins');
					$is_for_update = isset($current->response[$plugin_file]);
					
					$title = htmlspecialchars($plugin_in_category["Description"], ENT_QUOTES);
					if($is_activated)
					{
						$checkbox = "<img src='" . $imgpath . "/green-tick.png' height='12px' width='12x' />";
						$caption = "<strong><span title='$title'>" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "</span></strong> &nbsp";
						if (isset($plugin_in_category["Link"])) {					
							$caption.='&nbsp' . '<a href="'.$plugin_in_category["Link"].'" target="_blank" title="' . __('View this plugin', 'quick-deploy') . '" class="edit">' . __('View', 'quick-deploy').'</a>&nbsp|&nbsp';
						}
						$caption .= '<a href="' . wp_nonce_url('options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&action=deactivate&amp;plugin=' . $plugin_file, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin', 'quick-deploy') . '">' . __('Deactivate', 'quick-deploy') . '</a>';
						if (($is_for_update && current_user_can('update_plugins') && !(empty($current->response[$plugin_file]->package))))
						{
							$caption .= "&nbsp|&nbsp<a href='" . wp_nonce_url("options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&action=upgrade-plugin&plugin=" . $plugin_file . "&activated=" . $is_activated, 'upgrade-plugin_' . $plugin_file) . "' title='" . __('Update this plugin', 'quick-deploy') . "'>" . __("Update", 'quick-deploy') . "</a>";
						}
					}
					else {
						$checkbox = "<img src='" . $imgpath . "/yellow-tick.png' height='12px' width='12px' />";
						$caption = "<span title='$title'>" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "</span> &nbsp";
						if (isset($plugin_in_category["Link"])) {					
							$caption.='&nbsp' . '<a href="'.$plugin_in_category["Link"].'" target="_blank" title="' . __('View this plugin', 'quick-deploy') . '" class="edit">' . __('View', 'quick-deploy').'</a>&nbsp|&nbsp';
						}
						$caption .= '<a href="' . wp_nonce_url('options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&action=activate&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin', 'quick-deploy') . '" class="edit">' . __('Activate', 'quick-deploy') . '</a>';
						if (($is_for_update && current_user_can('update_plugins') && !(empty($current->response[$plugin_file]->package))))
						{
							$caption .= "&nbsp|&nbsp<a href='" . wp_nonce_url("options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&action=upgrade-plugin&plugin=" . $plugin_file, 'upgrade-plugin_' . $plugin_file) . "' title='" . __('Update this plugin', 'quick-deploy') . "'>" . __("Update", 'quick-deploy') . "</a>";
						}
						$caption .= "&nbsp|&nbsp" . '<a href="' . wp_nonce_url('options-general.php?page=wp-quick-deploy/wp-quick-deploy.class.php&action=delete-selected&amp;deleted[]=' . $plugin_file, 'bulk-manage-plugins') . '" title="' . __('Delete this plugin', 'quick-deploy') . '" class="delete">' . __('Delete', 'quick-deploy');
					}
				} else {
					$title = htmlspecialchars($plugin_in_category["Description"], ENT_QUOTES);
					$checkbox = "<input type='checkbox' name='checked[]' style='cursor:pointer;' value='" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "' id='" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "' />";
					//$caption = "<label title='$title' style='' for='" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "'>" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "</label>";
					$caption = "<span title='$title'>" . htmlspecialchars($plugin_in_category["Name"], ENT_QUOTES) . "</span> &nbsp";
					if (isset($plugin_in_category["Link"])) {
					//$caption .= ' <a href="'.$plugin_in_category["Link"].'" target="_blank"><img title="Open in new window" src="' . $imgpath . '/link.png" /></a>';
					$caption.='&nbsp' . '<a href="'.$plugin_in_category["Link"].'" target="_blank" title="' . __('View this plugin', 'quick-deploy') . '" class="edit">' . __('View', 'quick-deploy').'</a>';
				}
				}
				
				echo "<td scope='row' class='check-column' style='padding-top:3px;padding-bottom:3px;'><div class='plug' style='padding-top: 4px;'>$checkbox $caption</div></td>";
				if($counter % 2 == 0)
				{
					echo '</tr>';
				}
			}
			if($counter % 2 == 1)
			{
				echo '</tr>';
			}
		}
?>
	</tbody>
</table>
<br />
<?php
	}
	global $wp_version;
	if (!(version_compare($wp_version, '2.8', '<')))
	{
?>
					<p>
						<input class="button-primary" type="submit" name="apu_update" value="<?php _e('Install plugins', 'quick-deploy'); ?> &raquo;" class="button-primary"/> <br /><br />
						<input type="checkbox" id="activate" name="activate" value="OK" style='cursor:pointer;' /> <label for="activate"><?php _e('Activate after install', 'quick-deploy'); ?></label>
					</p>
<?php
	}
?>					
				</form>
    		</div>
			<h2><?php _e("Suggest a plugin", 'quick-deploy'); ?></h2>
			<p><?php _e("We try to list only the top WordPress plugins and those requested by many users.<br/>Please tell us what you want to see in the future.", 'quick-deploy'); ?></p>
			<form name="form_sug" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<table>
					<tr>
						<td>
							<?php _e("Plugin URL (from WordPress.org):", 'quick-deploy'); ?>
						</td>
						<td>
							<input type="text" style="width:400px;" name="suggested_plugin_url" id="suggested_plugin_url" />
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top;">
							<?php _e("Comment:", 'quick-deploy'); ?>
						</td>
						<td>
							<textarea style="width:400px;" name="suggested_plugin_comment" id="suggested_plugin_comment" cols="40" rows="6"></textarea>
						</td>
					</tr>
				</table>
				<input type="submit" class="button-primary" name="apu_suggest" value="<?php _e("Submit", 'quick-deploy'); ?>" />
			</form>
    	</div>
    </div>
</div>
<div class='clear'></div>
<h5 class="author"><?php _e('Another fine WordPress plugin by', 'quick-deploy'); ?> <a href="http://www.prelovac.com/vladimir/">Vladimir Prelovac</a></h5>

<?php
    }
}
?>