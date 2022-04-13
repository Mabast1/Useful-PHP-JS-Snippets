/* custom male/female user dashboard changes */
<?php
function user_avatar_based_on_gender(){
global $wpdb, $table_prefix;
$user_ID = get_current_user_id();
$value = $wpdb->get_var("SELECT value FROM `wp_bp_xprofile_data` WHERE user_id = '$user_ID' AND field_id = 4");

if ($value == "his_Male"){ ?> 
	<style>
		#femaleuser{
			display:none;
		}
	</style> 
<?php } else{ ?> 
	<style>
		#maleuser{
			display:none;
		}
		#femaleuser{
			display:block;
		}
	</style> 
<?php }
}
add_shortcode( 'user_gender', 'user_avatar_based_on_gender' );

/* end custom male/female user dashboard changes */