<?php
/**
 * By: Mabast Ahmad
 * 
 * Rename admin menus added by plugins
 */
function kl_rename_plugin_menus() {
    global $menu;

    // Define your changes here
    $updates = array(
        "LearnDash LMS" => array(
            'name' => 'QL Courses',
        ),
		"BuddyBoss" => array(
			'name' => 'QL Settings',
		),
		"H5P Content" => array(
			'name' => 'QL Videos'
		),
    );

    foreach ( $menu as $k => $props ) {

        // Check for new values
        $new_values = ( isset( $updates[ $props[0] ] ) ) ? $updates[ $props[0] ] : false;
        if ( ! $new_values ) continue;

        // Change menu name
        $menu[$k][0] = $new_values['name'];

        // Optionally change menu icon
        if ( isset( $new_values['icon'] ) )
            $menu[$k][6] = $new_values['icon'];
    }
}
add_action( 'admin_init', 'kl_rename_plugin_menus' );

//end of rename function
