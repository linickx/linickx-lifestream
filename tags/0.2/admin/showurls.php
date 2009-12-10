<?php

        /*
                Admin Debug - Show the URLS in the DB
        */
?>
<h3>URL(S) / FEED(S) in the Database</h3>

<?php
	$lnx_lifestream_options = get_option('lnx_lifestream_options'); // Plugin Options
	$lnx_lifestram_lastupdate = $lnx_lifestream_options['lastupdate'];
	
	echo "Last Update: <em>$lnx_lifestram_lastupdate</em> = " . date('d - M - Y (H:i)',$lnx_lifestram_lastupdate) . "<hr/>";

	$lnx_lifestream_urls = get_option('lnx_lifestream_urls');

	if ($lnx_lifestream_urls) {

		$lnx_lifestream_urls_meta = get_option('lnx_lifestream_urls_meta'); // Load the Meta DB.

		if (!$lnx_lifestream_urls_meta) {
			echo "No URL Meta in DB";
		}

		$counter = 0;

		foreach($lnx_lifestream_urls as $lnx_lifestream_url) {

			echo  "<p><b><a href=\"" . $lnx_lifestream_url . "\">" . $lnx_lifestream_url . "</a></b><pre>";
			print_r($lnx_lifestream_urls_meta[$counter]);
			echo "</pre></p>";

			$counter++;
		}

	} else {
		echo "No URLs in Database";
	}

?>
