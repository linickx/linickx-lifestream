<?php

        /*
                Admin Debug - R U Sure you want to reset?
        */
?>
<h2>Facory Reset</h2>
<p>The following, will clean out WordPress of anything LINICKX Lifestream releated.</p>
<p><strong>Are you sure you want to do this?</strong></p>
<?php
echo "<a href='" . wp_nonce_url( add_query_arg('debug', 'resetsure'), 'lnx_lifestream' ) . "'>" . __( 'Yes I\'m Sure', 'lnx_lifestream' ) . "</a>";
?>
