<?php

/*
	The Main Admin Page
*/

?>
	<div class="wrap">
		<h2>Linickx LifeStream Options</h2>
		<h3>Lifestream Feeds (URLS)</h3>
		<form method="post" action="options.php">
			<?php 	
				settings_fields('lnx_lifestream_options'); 
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
				 if ( $options['update'] == "wp" ) { // WP Update Interval Option
			?>
				 <tr valign="top"><th scope="row">Update Interval <br />  <span class="description">How often should we check your feeds? (In Minutes)</span></th>
                                        <td><input type="text" name="lnx_lifestream_options[updateinterval]" value="<?php echo $options['updateinterval'];?>" > </span></td>
                                </tr>	
			<?php
				}

				if ( $options['update'] == "cron" ) { // Cron Verbose Option
			?>
				<tr valign="top"><th scope="row">Verbose? <br />  <span class="description">Do you want <a href="<?php echo WP_PLUGIN_URL . "/linickx-lifestream/run.php";?>">run.php</a> to make a noise?</span></th>
                                        <td><input type="checkbox" name="lnx_lifestream_options[cronverbose]" value="1" <?php checked('1', $options['cronverbose']); ?>> </span></td>
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
			/*
				User Debug Options...
			*/
		?>
			<h3>Debug...</h3>
			<p>
		<?php

			echo "<a href='" . wp_nonce_url( add_query_arg('debug', 'showdb'), 'lnx_lifestream' ) . "'>" . __( 'Show the feed Database', 'lnx_lifestream' ) . "</a> - ";
			echo "<a href='" . wp_nonce_url( add_query_arg('debug', 'showurls'), 'lnx_lifestream' ) . "'>" . __( 'Show the URLS/FEEDS', 'lnx_lifestream' ) . "</a> - ";
			echo "<a href='" . wp_nonce_url( add_query_arg('debug', 'reset'), 'lnx_lifestream' ) . "'>" . __( 'Factory Reset', 'lnx_lifestream' ) . "</a>";

		?>
			</p>
		<hr />
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

/*
	End
*/

?>	

