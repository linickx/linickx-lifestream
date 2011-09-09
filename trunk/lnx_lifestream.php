<?php
/*
Plugin Name: LINICKX LifeStream
Plugin URI: http://www.linickx.com/archives/2751/linickx-lifestream-v0-2
Description: A WordPress Plug-in which allows you to lifestream any Feed! 
Version: 0.2.1
Author: Nick Bettison
Author URI: http://www.linickx.com/
*/

	# Only load up the admin code if we need it :-)
	if (is_admin()) {
		include_once(WP_PLUGIN_DIR . "/linickx-lifestream/admin.php");
	}

	$lnx_lifestream_options = get_option('lnx_lifestream_options'); // Plugin Options

	// Only do something if we have options...
	if ($lnx_lifestream_options) {

		// This is the function WP kicks off to update the feeds.
		function lnx_lifestream_update() {

			global $wpdb, $lnx_lifestream_options; // you always need options :)

			if (isset($lnx_lifestream_options['lastupdate'])) { // have re run before?

                	        $lnx_lifestram_lastupdate = $lnx_lifestream_options['lastupdate'];
				$lnx_lifestream_updateinterval = $lnx_lifestream_options['updateinterval'];
	                        $lnx_lifestream_updateinterval = $lnx_lifestream_updateinterval * 60; // Convert our Value into Seconds.

				 // Is it time to update yet?
                	        if (time() - $lnx_lifestram_lastupdate < $lnx_lifestream_updateinterval) {
                        	        return; // no, return then.
                        	}
				// Yes! Update
				include_once(WP_PLUGIN_DIR . "/linickx-lifestream/run.php");

                	} else {
                        	$lnx_lifestram_lastupdate = time(); // 1s run, set the time.
                	}

			// Stick the run time  in the DB
                        $lnx_lifestream_options['lastupdate'] = $lnx_lifestram_lastupdate;
                        update_option('lnx_lifestream_options', $lnx_lifestream_options);
			return;

		}
		
		if(!defined('WP_ADMIN')) { // We Don't want to run in the dasboard.

			if ($lnx_lifestream_options['update'] == "wp")  {
				// Hook our Updates Into WP
				add_action('shutdown', 'lnx_lifestream_update');
			}
		}
	}
?>
