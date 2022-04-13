<?php
function the_dramatist_custom_login_css() {
    echo '<style type="text/css"> 
		#branding {
    		display: none;
		} 
	</style>';
}
add_action('login_head', 'the_dramatist_custom_login_css');