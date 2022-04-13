<?php
add_action('admin_head', 'remove_ul_notice');

function remove_ul_notice() {
  echo '<style>
   .notice.notice-warning {
    	display: none;
	}
  </style>';
}