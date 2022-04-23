<?php
/**
 * Hide BuddyPress Members Directory from everyone except site admin.
 */
function buddydev_hide_members_directory_for_all_except_admin() {
    if ( bp_is_members_directory() && ! is_super_admin() ) {
        bp_do_404();
        load_template( get_404_template() );
        exit( 0 );
    }
}
 
add_action( 'bp_template_redirect', 'buddydev_hide_members_directory_for_all_except_admin' );