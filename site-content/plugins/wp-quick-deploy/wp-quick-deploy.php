<?php
/*
Plugin Name: WP Quick Deploy
Version: 1.2
Description: Install multiple plugins at once without hassle.
Author: Vladimir Prelovac
Author URI: http://www.prelovac.com/vladimir
Plugin URI: http://www.prelovac.com/vladimir/wordpress-plugins/wp-quick-deploy

Copyright 2010  Vladimir Prelovac
*/

/*
Credits: 

Marko Novakovic http://www.linkedin.com/pub/marko-novakovic/b/b09/727 for contribution to this plugin
*/

if (isset($quick_deployment)) return false;

require_once(dirname(__FILE__) . '/wp-quick-deploy.class.php');

$quick_deployment = new QuickDeployment();