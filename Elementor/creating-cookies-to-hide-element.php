<?php
add_action('init', function() {
    if (!isset($_COOKIE['training_banner_cookie'])) {
        setcookie('training_banner_cookie', 'showbanner', strtotime('+1 day'));
    }
	$cookieValue = $_COOKIE['training_banner_cookie'];
	if (/*condition to find a class in the dome*/)){ ?>
		<style>
			#training-alert{display:none;}
		</style> 
		<?php
		setcookie('training_banner_cookie', 'hidebanner', strtotime('+1 day'));
	}
	if ($cookieValue == "hidebanner"){ ?>
		<style>
			#training-alert{display:none;}
		</style> 
		<?php
	}
});
