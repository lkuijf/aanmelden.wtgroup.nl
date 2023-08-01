<?php
chdir('../../../../');
require( 'wp-load.php' ); // Load WordPress.

\WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-pages' );
