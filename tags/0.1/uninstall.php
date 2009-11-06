<?php

// Uninstall
// Reference: http://jacobsantos.com/2008/general/wordpress-27-plugin-uninstall-methods/

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

// Delete All WordPress Options

delete_option('lnx_lifestream_urls');
delete_option('lnx_lifestream_urls_meta');
delete_option('lnx_lifestream_options');
delete_option('lnx_lifestream_feeddb');

// Delete Files

$savedItemsFilename = WP_CONTENT_DIR . "/lnx_lifestream_feeddb.txt";

if(file_exists($savedItemsFilename)) {
	unlink($savedItemsFilename);
}

// Done, all clean.

?>
