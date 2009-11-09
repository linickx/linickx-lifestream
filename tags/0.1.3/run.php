<?php
/*
	run.php
	~~~~~~~

	This script is used to check the feeds, and insert the posts.
*/

	if (file_exists("config.php")) { // Do We have a Config File?
		require("config.php");
	}

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

		}
	}

	// Now thats done, let's get started.

	# $lnx_lifestream_options is loaded by the plugin.
	$lnx_isrunning = $lnx_lifestream_options['isrunning']; // Is the Script Running ?
	
	if (!$lnx_isrunning) {

		// Load our URLS from the Database...
		$lnx_lifestream_urls = get_option('lnx_lifestream_urls');
		$lnx_lifestream_options['isrunning'] = true; // We Are Running!
		update_option('lnx_lifestream_options', $lnx_lifestream_options);

	}

	// All code sit's in here, so that nothing happenz if there are no URLS to get.
	if ($lnx_lifestream_urls) {

		// db holder
		$savedItems = array();

		// max days to check for feed items.
		$numberOfDays = $lnx_lifestream_options['feeddbsize'];

		$numberOfDaysInSeconds = ($numberOfDays*24*60*60);
		$expireDate = time() - $numberOfDaysInSeconds;

		$lnx_feeddb_location = $lnx_lifestream_options['feeddb']; // Where should our DB live?
		
		/*
				load db into array
		*/

		if ($lnx_feeddb_location == "wp") { // DB Lives in WordPress

			if (get_option('lnx_lifestream_feeddb')) {
				$savedItems = unserialize(get_option('lnx_lifestream_feeddb'));
			} else {
				$savedItems = array(); // A new DB is created if we don't find it.
			}
		
		} elseif ($lnx_feeddb_location == "file") { // DB Lives as a file
		
			$savedItemsFilename = WP_CONTENT_DIR . "/lnx_lifestream_feeddb.txt";
	
			if(file_exists($savedItemsFilename)) {
        			$savedItems = unserialize(file_get_contents($savedItemsFilename));
        			if(!$savedItems) {
                			$savedItems = array();
        			}
			}
		} else {
			$savedItems = array(); // failsafe, create array.
		}

		 
		/*
				Loop through items to find new ones and insert them into db and create post
		*/

		$counter = 0;
		$lnx_lifestream_urls_meta = get_option('lnx_lifestream_urls_meta'); // Load up our Meta DB

		foreach($lnx_lifestream_urls as $lnx_lifestream_url) {

			set_time_limit(20); // Up the Execution Time

			$lnx_lifestream_feed = fetch_feed($lnx_lifestream_url); // go WP-SimplePie, do your thing!

			foreach($lnx_lifestream_feed->get_items() as $item)
			{
			 
					// if item is too old dont even look at it
					if($item->get_date('U') < $expireDate)
						continue;
		 
		 
					// make id
					$id = md5($item->get_id());
				
					// if item is already in db, skip it
					if(isset($savedItems[$id]))
					{
								continue;
					}
					// found new item, add it to db
					$i = array();
					$i['title'] = $item->get_title();
					$i['link'] = $item->get_link();
					$i['author'] = '';
					$author = $item->get_author();
					if($author)
					{
						$i['author'] = $author->get_name();
					}
					$i['date'] = $item->get_date('U');
					$i['content'] = $item->get_content();
					$lnx_lifestream_feed = $item->get_feed();
					$i['feed_link'] = $lnx_lifestream_feed->get_permalink();
					$i['feed_title'] = $lnx_lifestream_feed->get_title();
		 
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
	 
	 
	 
		/*
				save db
		*/
		if ($lnx_feeddb_location == "wp") { // DB Lives in WordPress

                        if (get_option('lnx_lifestream_feeddb'))
						{
								update_option('lnx_lifestream_feeddb',serialize($savedItems));
						} else {
								// Create New DB :-)
								add_option('lnx_lifestream_feeddb',serialize($savedItems));
						}
		
                } elseif ($lnx_feeddb_location == "file") { // DB Lives as a file

                        if (!$handle = fopen($savedItemsFilename, 'a')) {
							echo "Cannot open file ($savedItemsFilename)";
						}	

						if(!fwrite($handle,serialize($savedItems)))
						{
								echo ("<strong>Error: Can't save items to file.</strong><br>");
						}


                } else {
                        echo ("<strong>Error: Can't save Feed Database.</strong><br>");
                }

		/*
			End Script
		*/
		$lnx_lifestream_options['isrunning'] = false;
                update_option('lnx_lifestream_options', $lnx_lifestream_options);
	}
?>
