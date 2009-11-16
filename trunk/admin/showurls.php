<?php

        /*
                Admin Debug - Show the URLS in the DB
        */
?>
<h3>URL(S) / FEED(S) in the Database</h3>

<?php

	$lnx_lifestream_urls = get_option('lnx_lifestream_urls');

	if ($lnx_lifestream_urls) {

		$lnx_lifestream_urls_meta = get_option('lnx_lifestream_urls_meta'); // Load the Meta DB.

		if (!$lnx_lifestream_urls_meta) {
			echo "No URL Meta in DB";
		}

		$counter = 0;

		foreach($lnx_lifestream_urls as $lnx_lifestream_url) {

			echo  $lnx_lifestream_url;
			print_r($lnx_lifestream_urls_meta[$counter]);
			echo "<hr />";

			$counter++;
		}

	} else {
		echo "No URLs in Database";
	}

?>
