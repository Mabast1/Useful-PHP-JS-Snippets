<?php
// Autocomplete function hookup
add_action("wp", "custom_learndash_automatically_mark_complete");
//function for Mark Complete Button Removal
function custom_learndash_automatically_mark_complete() { 
  global $post, $current_user; 
  $excluded_courses = array(1234, 5674, 6785);

  $course_id = learndash_get_course_id(); 
  if(empty($course_id) || in_array($course_id, $excluded_courses)) 
    return;

  if( !empty($current_user->ID) && !empty($post->post_type) && $post->post_type == "sfwd-lessons") { 
    learndash_process_mark_complete($current_user->ID, $post->ID); 
  }

  if( !empty($current_user->ID) && !empty($post->post_type) && $post->post_type == "sfwd-topic") { 
    learndash_process_mark_complete($current_user->ID, $post->ID); 
  }
}