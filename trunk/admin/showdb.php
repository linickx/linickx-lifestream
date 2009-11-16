<?php

	/*
		Admin Debug - Show the Feed Database
	*/
?>
	<h3>Your LifeStream Feed DB...</h3>
<?php
		$lnx_lifestream_options = get_option('lnx_lifestream_options'); // Plugin Options

		$lnx_feeddb_location = $lnx_lifestream_options['feeddb']; // Where should our DB live?

		/*
                                load db into array
                */

                if ($lnx_feeddb_location == "wp") { // DB Lives in WordPress

                        if (get_option('lnx_lifestream_feeddb')) {
                                $savedItems = unserialize(get_option('lnx_lifestream_feeddb'));
                        } else {
				echo "Loading WordPress DB Failed";
                        }

                } elseif ($lnx_feeddb_location == "file") { // DB Lives as a file

                        $savedItemsFilename = WP_CONTENT_DIR . "/lnx_lifestream_feeddb.txt";

                        if(file_exists($savedItemsFilename)) {
                                $savedItems = unserialize(file_get_contents($savedItemsFilename));
                        } else {
				echo "lnx_lifestream_feeddb.txt Doesn't exist in" .  WP_CONTENT_DIR;
			}
                } else {
			echo "I don't know where to find your DB!";
                }
?>
	<pre>
<?php
		print_r($savedItems);
?>
	</pre>
