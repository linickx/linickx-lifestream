<?php

// plug us into the admin dashboard...
add_action('admin_init', 'lnx_lifestream_init' );
add_action('admin_menu', 'lnx_lifestream_add_page');

// Admin Warning
add_action('admin_notices', 'lnx_lifestream_cron_warning');


// Init plugin options to white list our options
function lnx_lifestream_init(){
	register_setting( 'lnx_lifestream_options', 'lnx_lifestream_options', 'lnx_lifestream_validate_options' );
	register_setting( 'lnx_lifestream_options', 'lnx_lifestream_urls', 'lnx_lifestream_validate_urls' );
	register_setting( 'lnx_lifestream_options', 'lnx_lifestream_urls_meta', 'lnx_lifestream_validate_urls_meta' );
}

// Add menu page
function lnx_lifestream_add_page() {
	add_options_page('Linickx LifeStream Options', 'LifeStream', 'manage_options', 'lnx_lifestream', 'lnx_lifestream_do_page');
}

// Draw the menu page itself
function lnx_lifestream_do_page() {

	
	if (isset($_GET['debug'])) {

		$nonce=$_REQUEST['_wpnonce'];

		if (wp_verify_nonce($nonce, 'lnx_lifestream')) { // WP Admin Pages use nonces for Security...

			if ($_GET['debug'] == 'showdb') {

				include_once(WP_PLUGIN_DIR . "/linickx-lifestream/admin/showdb.php"); // Show the Feed DB.

			} elseif ($_GET['debug'] == 'showurls') {

				include_once(WP_PLUGIN_DIR . "/linickx-lifestream/admin/showurls.php"); // Show the URLS we have stored

			} elseif ($_GET['debug'] == 'reset') {

                                include_once(WP_PLUGIN_DIR . "/linickx-lifestream/admin/reset.php"); // Factory Reset

			 } elseif ($_GET['debug'] == 'resetsure') {

                                include_once(WP_PLUGIN_DIR . "/linickx-lifestream/admin/resetsure.php"); // Factory Reset - I'm Sure!

			} else {
				echo "Hello World";
			}

		} else {
			Die('Security Check Failed');
		}

	} else {

		include_once(WP_PLUGIN_DIR . "/linickx-lifestream/admin/main.php"); // Our Main Admin Page

	}
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function lnx_lifestream_validate_urls($input) {

	$counter = 0;
	foreach ($input as $item) {

		// I need a proper validation, but this can be a start... no html.
		$input[$counter] =  wp_filter_nohtml_kses($item);
	
		// Delete any empty entries..	
		if ($item == "") {
			unset($input[$counter]);
		}
		$counter++;
	}
	
	return $input;
}

function lnx_lifestream_validate_urls_meta($input) { // Validate Meta

        $counter = 0;
        foreach ($input as $item) {
			// I need a proper validation, but this can be a start... no html.
			$input[$counter]['tags'] =  wp_filter_nohtml_kses($input[$counter]['tags']);

			// Only Numbers for this one...
        	if (!is_numeric($input[$counter]['cat'])) {
                	 unset($input[$counter]['cat']);
        	}

			// Delete any empty entries..
			if ($item['tags'] == "") {
					unset($input[$counter]['tags']);
			}
			if ($item['cat'] == "") {
					unset($input[$counter]['cat']);
			}
			
			$counter++;
        }
        return $input;
}

function lnx_lifestream_validate_options($input) { // Validate options...


	// Only two options allowed here..
	if (!($input['update'] == "wp" || $input['update'] == "cron"))  {
		$input['update'] = "wp";
	}

	// And here...
	if (!($input['feeddb'] == "wp" || $input['feeddb'] == "file"))  {
                $input['feeddb'] = "wp";
        }

	// Only Numbers for this one...
	if (!is_numeric($input['updateinterval'])) {
		$input['updateinterval'] = "5";
	}

	// Whole Numbers...
	$input['updateinterval'] = (int)$input['updateinterval'];

	// Positive, whole numbers :)
	if ($input['updateinterval'] < 1) {
		$input['updateinterval'] = "5";
	}

	// Same Checks...
	if (!is_numeric($input['feeddbsize'])) {
                $input['feeddbsize'] = "5";
        }

        // Whole Numbers...
        $input['feeddbsize'] = (int)$input['feeddbsize'];

        // Positive, whole numbers
        if ($input['feeddbsize'] < 1) {
                $input['feeddbsize'] = "5";
        }	

	// Vailidate our Checkbox value is either 0 or 1
	$input['cronverbose'] = ( $input['cronverbose'] == 1 ? 1 : 0 );


	return $input;
}

# Error Checking.
function lnx_lifestream_cron_warning() {

	$options = get_option('lnx_lifestream_options'); // Generic Plugin Options.

	if ($options['update'] == "cron") { // Cron Needs A config File to Run.
		if (!file_exists(WP_PLUGIN_DIR . "/linickx-lifestream/config.php")) {
			echo "
			<div id='lnx_cron_warning' class='updated fade'><p><strong>".__('LINICKX LifeStream Error.')."</strong> ".sprintf(__('To Use Cron Updating you must create config.php, see FAQ or Config.Sample.php for more details'), "http://wordpress.org/extend/plugins/linickx-lifestream/faq/")."</p></div>
			";
		}
	}
}

?>
