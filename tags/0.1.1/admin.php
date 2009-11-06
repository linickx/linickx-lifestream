<?php

// plug us into the admin dashboard...
add_action('admin_init', 'lnx_lifestream_init' );
add_action('admin_menu', 'lnx_lifestream_add_page');

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
	?>
	<div class="wrap">
		<h2>Linickx LifeStream Options</h2>
		<h3>Lifestream Feeds (URLS)</h3>
		<form method="post" action="options.php">
			<?php 	settings_fields('lnx_lifestream_options'); 
				#delete_option('lnx_lifestream_urls');
				#delete_option('lnx_lifestream_urls_meta');
				$lnx_lifestream_urls = get_option('lnx_lifestream_urls'); // Feed URLs
				$lnx_lifestream_urls_meta = get_option('lnx_lifestream_urls_meta'); // Feed URL Meta
				
				if (!$lnx_lifestream_urls) { //Check if our URLs is in the DB, if not, display defaults..
					$lnx_lifestream_urls = array();
					$lnx_lifestream_urls[0] = "http://www.linickx.com/feed";
				}
				$categories=  get_categories('hide_empty=0'); // Post Categories.
			?>
			<table class="form-table">
			<?php
				$counter = 0;
				foreach ($lnx_lifestream_urls as $lnx_lifestream_url) {
			?>
				<tr valign="top"><th scope="row">Feed URL <?php echo $counter;?></th>
					<td><input type="text" name="lnx_lifestream_urls[<?php echo $counter;?>]" value="<?php echo $lnx_lifestream_url; ?>" /></td>
			<?php
			/*	Thanks to this thread, for solving my issues here :-)
				http://www.phpbuilder.com/board/showthread.php?t=10368885
			*/
			?>
					<td>Category: <select name="lnx_lifestream_urls_meta[<?php echo $counter;?>][cat]" ><option value=""><?php echo attribute_escape(__('Select Category')); ?></option> 
			<?php 
			/*
				Lifted from: http://codex.wordpress.org/Function_Reference/get_categories
			*/
					foreach ($categories as $cat) {
						$option = '<option value="'. $cat->cat_ID . '"';
							if ($lnx_lifestream_urls_meta[$counter]['cat'] == $cat->cat_ID) {
								$option .= ' selected ';
							}
						$option .= '>';
						$option .= $cat->cat_name;
						$option .= ' ('.$cat->category_count.')';
						$option .= '</option>';
						echo $option;
					}
			?>
					</select></td>
					<td>Tags:<input type="text" name="lnx_lifestream_urls_meta[<?php echo $counter;?>][tags]" value="<?php echo $lnx_lifestream_urls_meta[$counter]['tags']; ?>" /> </td>
				</tr>
			<?php
				$counter++;
				}
			?>
				<tr valign="top"><th scope="row">Feed URL <?php echo $counter;?></th>
					<td><input type="text" name="lnx_lifestream_urls[<?php echo $counter;?>]" value="" /> <br /><span class="description">Type in the URLs of the Feeds you want to lifestream</span></td>
					 <td>Category: <select name="lnx_lifestream_urls_meta[<?php echo $counter;?>][cat]" ><option value=""><?php echo attribute_escape(__('Select Category')); ?></option>
                        <?php
                                        foreach ($categories as $cat) {
                                                $option = '<option value="'. $cat->cat_ID . '"';
                                                $option .= '>';
                                                $option .= $cat->cat_name;
                                                $option .= ' ('.$cat->category_count.')';
                                                $option .= '</option>';
                                                echo $option;
                                        }
                        ?>
				</select> <br /><span class="description">What Category should be used for posts?</span></td>
                                        <td>Tags:<input type="text" name="lnx_lifestream_urls_meta[<?php echo $counter;?>][tags]" value="" /> <br /><span class="description">What Tags should be used for posts?</span></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save URLs') ?>" />
			</p>

		<h3>General Options</h3>
			<?php
				#delete_option('lnx_lifestream_options');
				$options = get_option('lnx_lifestream_options'); // Generic Plugin Options.
				
				if (!$options) { // Check for options, add Default Values.
					$options = array();
					$options['update'] = "wp"; // By Default WordPress will run things.
					$options['updateinterval'] = "5"; // By Defaul, Update every 5 Mins.
					$options['feeddbsize'] = "10"; // By Default, only 10 Days of Feed in the DB.
					$options['feeddb'] = "wp"; // By Default WordPress will store the DB
				}

				/*
					Set up some Variables to Handle Radio Buttons...
				*/
				if ( $options['update'] == "wp" ) {
					$update_wp_status = 'checked';
				} elseif ( $options['update'] == "cron" ) {
					$update_cron_status = 'checked';
				} else {
					die("Fail: No WP/Cron radio selected!");
				}

				if ( $options['feeddb'] == "wp" ) {
                                        $feeddb_wp_status = 'checked';
                                } elseif ( $options['feeddb'] == "file" ) {
                                        $feeddb_file_status = 'checked';
                                } else {
                                        die("Fail: No WP/File radio selected!");
                                }
			
			?>
			<table class="form-table">
				<tr valign="top"><th scope="row" style="width:400px">Update Method <br /> <span class="description">Who's in charge of updating your feeds? </span></th>
                                        <td><input type="radio" name="lnx_lifestream_options[update]" value="wp" <?php echo $update_wp_status;?>>WordPress Automatic <input type="radio" name="lnx_lifestream_options[update]" value="cron" <?php echo $update_cron_status;?>>Cron </td>
                                </tr>
			<?php 
				 if ( $options['update'] == "wp" ) {
			?>
				 <tr valign="top"><th scope="row">Update Interval <br />  <span class="description">How often should we check your feeds? (In Minutes)</span></th>
                                        <td><input type="text" name="lnx_lifestream_options[updateinterval]" value="<?php echo $options['updateinterval'];?>" > </span></td>
                                </tr>	
			<?php
				}
			?>
				<tr valign="top"><th scope="row">Database Size / Expiry <br /><span class="description">How many days should we store in the Database?</span></th>
                                        <td><input type="text" name="lnx_lifestream_options[feeddbsize]" value="<?php echo $options['feeddbsize'];?>"></td>
                                </tr>
				<tr valign="top"><th scope="row">Database Location <br /><span class="description">Where should the Feed Database be stored?</span></th>
                                        <td><input type="radio" name="lnx_lifestream_options[feeddb]" value="wp" <?php echo $feeddb_wp_status;?>>WordPress Database <input type="radio" name="lnx_lifestream_options[feeddb]" value="file" <?php echo $feeddb_file_status;?>>File </td>
                                </tr>
			 </table>
                        <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Options') ?>" />
                        </p>
                </form>

		<?php
	        # Let's tell users about plug-in news!
	        $lnx_lifestreamNEWSfeed = fetch_feed('http://www.linickx.com/archives/tag/linickx-lifestream/feed');
		?>
		        <h3>LINICKX LifeSteam Plug-in News</h3>
		        <ul>
		<?php
		        foreach ($lnx_lifestreamNEWSfeed->get_items() as $item){
                	        printf('<li><a href="%s">%s</a></li>',$item->get_permalink(), $item->get_title());
        		}
		?>
        		</ul>
        		<p><small><a href="http://www.linickx.com/archives/tag/linickx-lifestream/feed">Subcribe to this feed</a></small></p>

	</div>
	<?php	
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

	return $input;
}
?>
