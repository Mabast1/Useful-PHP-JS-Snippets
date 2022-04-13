<?php
function pfk_ld_user_course_points() {
	$points_p_tag = do_shortcode('[ld_user_course_points]' );
	$points_p_tag = str_replace( 'Earned Course Points:', '', $points_p_tag );
	return $points_p_tag;
}
add_shortcode( 'pfk_ld_user_course_points', 'pfk_ld_user_course_points' );