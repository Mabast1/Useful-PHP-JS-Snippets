<?php
add_filter( 'learndash_memberpress_min_courses_count_for_silent_course_enrollment', function( $count ) {
  return 999; // Big number so it won't use background course enrollment
});
?>