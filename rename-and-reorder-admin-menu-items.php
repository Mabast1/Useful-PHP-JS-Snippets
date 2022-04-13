<?php
/**
 * Author: Mabast
 * Activates the 'menu_order' filter and then hooks into 'menu_order'
 */
add_filter('custom_menu_order', function() { return true; });
add_filter('menu_order', 'my_new_admin_menu_order');
/**
 * Filters WordPress' default menu order
 */
function my_new_admin_menu_order( $menu_order ) {
  // define your new desired menu positions here
  // for example, move 'upload.php' to position #9 and built-in pages to position #1
  $new_positions = array(
      'h5p' => 1,
	  'pmpro-dashboard' => 5,
	  'edit.php?post_type=search-filter-widget' => 6,
	  'edit.php?post_type=acf-field-group'=> 7,
	  'snippets' => 8,
	  'pdf-print.php' => 100,
	  'wp-mail-smtp' => 101,
	  'wp-hide' => 102,
  );
  // helper function to move an element inside an array
  function move_element(&$array, $a, $b) {
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
  }
  // traverse through the new positions and move 
  // the items if found in the original menu_positions
  foreach( $new_positions as $value => $new_index ) {
    if( $current_index = array_search( $value, $menu_order ) ) {
      move_element($menu_order, $current_index, $new_index);
    }
  }
  return $menu_order;
};

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
            'name' => 'Edit Courses',
        ),
		"BuddyBoss" => array(
			'name' => 'SOD Settings',
		),
		"Code Combat" => array(
			'name' => 'CodeCombat'
		),
		"H5P Content" => array(
			'name' => 'SOD Videos'
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