<?php
/*
	run.php
	~~~~~~~

	This script is used to check the feeds, and insert the posts.
*/

	if (file_exists("config.php")) { // Do We have a Config File?
		require("config.php");
	}

	// By Default run.php is silent
	$lnx_lifestream_cronverbose = false;

	// Step 1, load WordPress if it's not there already.
	if (!defined('WP_USE_THEMES')) {

		if ( !defined('DB_NAME') ) { // We don't want to hook into the dashboard or other WP Bits.
			
				define('WP_USE_THEMES', false);
			
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $WPDIR . '/wp-blog-header.php' )) {
	    				require($_SERVER['DOCUMENT_ROOT'] . $WPDIR . '/wp-blog-header.php');
				} else {
					die('Can\'t find WordPress. Please copy config.sample.php to config.php and edit $WPDIR');
				}
		
	
				// Step 2, WP isn't there, should we be running?
				$lnx_lifestream_options = get_option('lnx_lifestream_options'); // Plugin Options
			
				if (!($lnx_lifestream_options['update'] == "cron"))  {
					die('Not Configured to run as Cron');
	        		}

				// Should we print anything on output?
				if ($lnx_lifestream_options['cronverbose'] == "1") {
					$lnx_lifestream_cronverbose = true;
				}

		}
	}

	// Now thats done, let's get started.

	# $lnx_lifestream_options is loaded by the plugin.
	$lnx_isrunning = $lnx_lifestream_options['isrunning']; // Is the Script Running ?
	
	if (!$lnx_isrunning) {

		if ($lnx_lifestream_cronverbose) {
			?><h1>LINICKX LifeStream</h1>
<?php
			
		}

		// Load our URLS from the Database...
		$lnx_lifestream_urls = get_option('lnx_lifestream_urls');
		$lnx_lifestream_options['isrunning'] = true; // We Are Running!
		update_option('lnx_lifestream_options', $lnx_lifestream_options);

	} else {
		if ($lnx_lifestream_cronverbose) {
			echo "Run.php is already running, exiting...";
		}
	}

	// All code sit's in here, so that nothing happenz if there are no URLS to get.
	if ($lnx_lifestream_urls) {

		// db holder
		$savedItems = array();

		// max days to check for feed items.
		$numberOfDays = $lnx_lifestream_options['feeddbsize'];

		$numberOfDaysInSeconds = ($numberOfDays*24*60*60);
		$expireDate = time() - $numberOfDaysInSeconds;

		if ($lnx_lifestream_cronverbose) {
			echo "Number of Days to Store in DB:" . $numberOfDays . "<br /> \n";
			echo "Posts with date before " . date('d - M - Y (H:i)',$expireDate) . " will be ignored <br /> \n";

		}

		$lnx_feeddb_location = $lnx_lifestream_options['feeddb']; // Where should our DB live?
		
		/*
				load db into array
		*/

		if ($lnx_feeddb_location == "wp") { // DB Lives in WordPress

			if ($lnx_lifestream_cronverbose) {
				echo "The Feed DB lives in WordPress <br /> \n";
			}	

			if (get_option('lnx_lifestream_feeddb')) {
				$savedItems = unserialize(get_option('lnx_lifestream_feeddb'));

				if ($lnx_lifestream_cronverbose) {
					echo "Found Old DB <br />\n";
				}

			} else {
				$savedItems = array(); // A new DB is created if we don't find it.

				if ($lnx_lifestream_cronverbose) {
					echo "Creating new DB <br /> \n";
				}
			}
		
		} elseif ($lnx_feeddb_location == "file") { // DB Lives as a file

			if ($lnx_lifestream_cronverbose) {
                                echo "The Feed DB is a File <br /> \n";
                        }
		
			$savedItemsFilename = WP_CONTENT_DIR . "/lnx_lifestream_feeddb.txt";
	
			if(file_exists($savedItemsFilename)) {

				if ($lnx_lifestream_cronverbose) {
					echo "Found a file to read <br /> \n";
				}

        			$savedItems = unserialize(file_get_contents($savedItemsFilename));
        			if(!$savedItems) {
                			$savedItems = array();
					
					if ($lnx_lifestream_cronverbose) {
                                        	echo "Creating new DB <br /> \n";
                                	}
        			}
			}
		} else {
			$savedItems = array(); // failsafe, create array.

			if ($lnx_lifestream_cronverbose) {
				echo "Umm, I couldn't find a valid DB option!";
			}
		}

		 
		/*
				Loop through items to find new ones and insert them into db and create post
		*/

		$counter = 0;
		$lnx_lifestream_urls_meta = get_option('lnx_lifestream_urls_meta'); // Load up our Meta DB

		foreach($lnx_lifestream_urls as $lnx_lifestream_url) {

			set_time_limit(20); // Up the Execution Time

			$lnx_lifestream_feed = fetch_feed($lnx_lifestream_url); // go WP-SimplePie, do your thing!

			if ($lnx_lifestream_cronverbose) {
				echo " \n <h2>Feed ID / Counter = " . $counter . "</h2> \n";
				echo 'Fetching Feed: <a href="' . $lnx_lifestream_url . '">' . $lnx_lifestream_url .'</a><br />' . "\n";
			}

			foreach($lnx_lifestream_feed->get_items() as $item)
			{
			 
					// if item is too old dont even look at it
					if($item->get_date('U') < $expireDate) {
						if ($lnx_lifestream_cronverbose) {
							echo $item->get_title() . " Too Old, skipping... <br /> \n";
						}
						continue;
		 			}
		 
					// make id
					$id = md5($item->get_id());
				
					// if item is already in db, skip it
					if(isset($savedItems[$id])) {
						if ($lnx_lifestream_cronverbose) {
                                                        echo $item->get_title() . " Is Already in DB, Skipping... <br /> \n";
						}
								continue;
					}
					
					// found new item, add it to db
					$i = array();

					$i['title'] = $item->get_title();
					$i['title'] = trim($i['title']);

					$i['link'] = $item->get_link();
					$i['link'] = trim($i['link']);

					$i['author'] = '';
					$author = $item->get_author();
					if($author)
					{
						$i['author'] = $author->get_name();
						$i['author'] = trim($i['author']);
					}

					$i['date'] = $item->get_date('U');
					$i['date'] = trim($i['date']);

					$i['content'] = $item->get_content();

					$i_feed = $item->get_feed();

					$i['feed_link'] = $i_feed->get_permalink();
					$i['feed_link'] = trim($i['feed_link']);

					$i['feed_title'] = $i_feed->get_title();
					$i['feed_title'] = trim($i['feed_title']);


					if ($lnx_lifestream_cronverbose) {
                                                        ?>
								<h3>Found a New Item</h3>
								<ul>
							<?php
								echo "<li>Item Title: " . $i['title'] . "</li> \n";
								echo "<li>Item Link: " . $i['link'] . "</li> \n";
								echo "<li>Item Author: " . $i['author'] . "</li> \n";
								echo "<li>Item Date: " . $i['date'] . "</li> \n";
								echo "<li>Feed Link: " . $i['feed_link'] . "</li> \n";
								echo "<li>Feed Title: " . $i['feed_title'] . "</li> \n";
							?>
								</ul>
							<?php
					}
		 
					//Create WP Post
					unset($lnx_post);
					$lnx_post = array();
					$lnx_post['post_title'] = $i['title'];
					$lnx_post['post_status'] = 'publish';
					$lnx_post['post_author'] = 1;

					if (isset($lnx_lifestream_urls_meta[$counter]['cat'])) {
						$lnx_post['post_category'] = array($lnx_lifestream_urls_meta[$counter]['cat']);
					}

					if (isset($lnx_lifestream_urls_meta[$counter]['tags'])) {
						$lnx_post['tags_input'] = $lnx_lifestream_urls_meta[$counter]['tags'];
					}

					$lnx_post['post_date'] = $item->get_date('Y-m-d H:i:s');
					$lnx_post['post_content'] = '<a href="' . $i['link'] . '">' . $i['title'] . '</a>';

					// Insert the post into the database
					 wp_insert_post( $lnx_post );

					if ($lnx_lifestream_cronverbose) {
						?>	<h3>Creating New Post</h3>
							<ul>
						<?php
							echo "<li>Post Title: " . $lnx_post['post_title'] . "</li> \n";
							echo "<li>Post Status: " . $lnx_post['post_status'] . "</li> \n";
							echo "<li>Post Author: " . $lnx_post['post_author'] . "</li> \n";
							echo "<li>Post Category: " . $lnx_post['post_category']  . "</li> \n";
							echo "<li>Post Tags: " . $lnx_post['tags_input'] . "</li> \n";
							echo "<li>Post Date: " . $lnx_post['post_date'] .  "</li> \n";
							echo "<li>Post Content: " . $lnx_post['post_content'] . "</li> \n";
						?>
							</ul>
							<hr />
						<?php
					}

					$savedItems[$id] = $i;
			}
			$counter++;
		}

		/*
			        remove expired items from db
		*/
		$keys = array_keys($savedItems);
		foreach($keys as $key)
			{
        			if($savedItems[$key]['date'] < $expireDate)
        			{
					if ($lnx_lifestream_cronverbose) {
						echo "Deleting " . $savedItems[$key]['title'] . "From DB <br /> \n";
					}
					
                			unset($savedItems[$key]);
        			}
		}

	 
	 
		/*
				sort items in reverse chronological order
		*/
		function customSort($a,$b)
		{
				return $a['date'] <= $b['date'];
		}
		uasort($savedItems,'customSort');
	 
		if ($lnx_lifestream_cronverbose) {
			echo "\n <p>&nbsp;</p> \n \n"; // White space, to make output more readable.
		} 
	 
		/*
				save db
		*/
		if ($lnx_feeddb_location == "wp") { // DB Lives in WordPress

                        if (get_option('lnx_lifestream_feeddb')) {
				update_option('lnx_lifestream_feeddb',serialize($savedItems));

					if ($lnx_lifestream_cronverbose) {
						echo "Updating DB in WP <br /> \n";	
					}
			} else {
				// Create New DB :-)
				add_option('lnx_lifestream_feeddb',serialize($savedItems));

					if ($lnx_lifestream_cronverbose) {
						echo "Creating new DB in WP <br /> \n";
					}
			}
		
                } elseif ($lnx_feeddb_location == "file") { // DB Lives as a file

                        if (!$handle = fopen($savedItemsFilename, 'a')) {
				echo "Cannot open file ($savedItemsFilename)";
			}	

			if(!fwrite($handle,serialize($savedItems))) {
				echo ("<strong>Error: Can't save items to file.</strong> <br /> \n");
			} else {
				if ($lnx_lifestream_cronverbose) {
					echo "Writing DB to File <br /> \n";
				}
			}


                } else {
                        echo ("<strong>Error: Can't save Feed Database.</strong><br /> \n");
                }

		/*
			End Script
		*/
		$lnx_lifestream_options['isrunning'] = false;
                update_option('lnx_lifestream_options', $lnx_lifestream_options);

		if ($lnx_lifestream_cronverbose) {
			echo "<strong>Done.</strong>";
		}

	} else {
		if ($lnx_lifestream_cronverbose) {
			echo "No URLS/FEEDS Found in Database. \n";
		}
	}

?>
