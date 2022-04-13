<?php
function wpmu_role_based_style() {

	if ( current_user_can( 'free_trial_user')|| current_user_can('standard_subscription') || current_user_can('premium_subscription') || current_user_can('subscriber') ) {
			?>
			<style>
			#menu-item-4863 {display:none;}
			</style>
			<?php
		}
}
// for front-end; comment out if you don't want to hide on front-end
add_action( 'wp_footer', 'wpmu_role_based_style', 99 );
?>