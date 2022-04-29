<?php

/* 
Disable plugin update notices 
Add the below code to your plugin file 
file and change the plugin_basename to the name 
of your plugin
*/

add_filter( 'http_request_args', 'dm_prevent_update_check', 10, 2 );
function dm_prevent_update_check( $r, $url ) {
    if ( 0 === strpos( $url, 'http://api.wordpress.org/plugins/update-check/' ) ) {
        $my_plugin = plugin_basename( __FILE__ );
        $plugins = unserialize( $r['body']['plugins'] );
        unset( $plugins->plugins[$my_plugin] );
        unset( $plugins->active[array_search( $my_plugin, $plugins->active )] );
        $r['body']['plugins'] = serialize( $plugins );
    }
    return $r;
}

/* 
Disable plugin update notices 
Add the below code to your theme's function.php 
file and change the plugin_basename to the name 
of your plugin
*/

add_filter('site_transient_update_plugins', 'remove_update_notification');
function remove_update_notification($value) {
     unset($value->response[ plugin_basename("ultimate-elementor/ultimate-elementor.php") ]);
     return $value;
} 

/* End of disable plugin update notices */
<?