<?php
$show_search        = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_header_search' );
$show_messages      = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_messages' ) && is_user_logged_in();
$show_notifications = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_notifications' ) && is_user_logged_in();
$show_shopping_cart = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_shopping_cart' );
$header_style       = buddyboss_theme_get_option( 'buddyboss_header' );
$profile_dropdown   = buddyboss_theme_get_option( 'profile_dropdown' );

// Gamification variable declarations 
$user_id = get_current_user_id();
$next_level_id = gamipress_get_next_user_rank_id($user_id, 'levels');
$requirements = gamipress_get_rank_requirements($next_level_id);
$points_needed = get_post_meta($requirements[0]->ID, '_gamipress_points_required', true);
$current_rank_id =  gamipress_get_user_rank_id ($user_id, 'levels');
$current_rank = get_the_title($current_rank_id);
$current_points = gamipress_get_user_points($user_id,'points');
$completion = round($current_points/ $points_needed * 100,0);
$current_coins = gamipress_get_user_points($user_id,'coins');
// End of Gamification variable declarations 

?>

<div id="header-aside" class="header-aside <?php echo esc_attr( $profile_dropdown ); ?>">
	<div class="header-aside-inner">

	<!-- Gamification header stats -->
	<div style="display: flex;align-items: center;margin-right: 10px" class="gamification">
				<div style="width: 50px;height: 50px;display: flex;justify-content: center;align-items: center;border-radius: 50%;background: <?=buddyboss_theme_get_option( 'accent_color' );?>" class="trophy">
					<img src="<?=get_stylesheet_directory_uri();?>/assets/img/trophy.png" style="width: 19.36px">
				</div>
				<div style="display: flex;flex-direction: column;min-width: 200px;padding-left: 15px;">
					<div style="display:flex; flex-direction: row;justify-content: space-between;align-items: center">
						<div style="font-size: 15px;">
							Level <?php echo $current_rank;?>
						</div>
						<div style="font-size: 12px">
							<span id="points"><?php echo $current_points;?></span> / <?php echo $points_needed;?>
						</div>
					</div>
					<div style="padding-top: 5px">
						<div style="width:100%;height: 8px;background-color: #EBF0F4;border-radius: 10px ">
							<div class="progress_bar" style="width:<?=$completion?>%;height: 8px;border-radius: 10px; background: <?=buddyboss_theme_get_option( 'accent_color' );?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<a style="display: flex;align-items: center; margin-right: 10px" href="<?php echo bp_loggedin_user_domain() . '/goodies/';?>" data-balloon-pos="down" data-balloon="Earn coins to unlock goodies" class="coins">
				<div style="display: flex;justify-content: center;align-items: center;border-radius: 50%;">
					<img src="<?=get_stylesheet_directory_uri();?>/assets/img/coin.png">
				</div>
				<div style="font-size: 16px;padding-left: 10px;font-weight: bold;" >
					<?=$current_coins;?>
				</div>
			</a>
	<!-- End of Gamification header stats -->
		
		<?php
		if ( is_user_logged_in() ) :
			if (
				(
					class_exists( 'SFWD_LMS' ) &&
					buddyboss_is_learndash_inner()
				) ||
				(
					class_exists( 'LifterLMS' ) &&
					buddypanel_is_lifterlms_inner()
				)
			) :
				?>
				<a href="#" id="bb-toggle-theme">
					<span class="sfwd-dark-mode" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Dark Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-rl bb-icon-moon"></i></span>
					<span class="sfwd-light-mode" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Light Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-sun"></i></span>
				</a>
				<a href="#" class="header-maximize-link course-toggle-view" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Maximize', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
				<a href="#" class="header-minimize-link course-toggle-view" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Minimize', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-merge"></i></a>
					
				<?php
			else :
				if ( $show_search && '4' !== $header_style ) :
					?>
					<a href="#" class="header-search-link" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Search', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-search"></i></a>
					<span class="bb-separator"></span>
					<?php
				endif;

				if ( $show_messages && function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) ) :
					get_template_part( 'template-parts/messages-dropdown' );
				endif;

				if ( $show_notifications && function_exists( 'bp_is_active' ) && bp_is_active( 'notifications' ) ) :
					get_template_part( 'template-parts/notification-dropdown' );
				endif;

				if ( $show_shopping_cart && class_exists( 'WooCommerce' ) ) :
					get_template_part( 'template-parts/cart-dropdown' );
				endif;
			endif;
		endif;

		if ( 'off' !== $profile_dropdown ) {
			if ( is_user_logged_in() ) :
				?>
				<div class="user-wrap user-wrap-container menu-item-has-children">
					<?php
					$current_user = wp_get_current_user();
					$user_link    = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $current_user->ID ) : get_author_posts_url( $current_user->ID );
					$display_name = function_exists( 'bp_core_get_user_displayname' ) ? bp_core_get_user_displayname( $current_user->ID ) : $current_user->display_name;
					?>

					<a class="user-link" href="<?php echo esc_url( $user_link ); ?>">
						<?php
						if ( 'name_and_avatar' === $profile_dropdown ) {
							?>
							<span class="user-name"><?php echo esc_html( $display_name ); ?></span><i class="bb-icon-l bb-icon-angle-down"></i>
							<?php
						}
						echo get_avatar( get_current_user_id(), 100 );
						?>
					</a>

					<div class="sub-menu">
						<div class="wrapper">
							<ul class="sub-menu-inner">
								<li>
									<a class="user-link" href="<?php echo esc_url( $user_link ); ?>">
										<?php echo get_avatar( get_current_user_id(), 100 ); ?>
										<span>
											<span class="user-name"><?php echo esc_html( $display_name ); ?></span>
											<?php if ( function_exists( 'bp_is_active' ) && function_exists( 'bp_activity_get_user_mentionname' ) ) : ?>
												<span class="user-mention"><?php echo '@' . esc_html( bp_activity_get_user_mentionname( $current_user->ID ) ); ?></span>
											<?php else : ?>
												<span class="user-mention"><?php echo '@' . esc_html( $current_user->user_login ); ?></span>
											<?php endif; ?>
										</span>
									</a>
								</li>
								<?php
								if ( function_exists( 'bp_is_active' ) ) {
									$header_menu = wp_nav_menu(
										array(
											'theme_location' => 'header-my-account',
											'echo'        => false,
											'fallback_cb' => '__return_false',
										)
									);
									if ( ! empty( $header_menu ) ) {
										wp_nav_menu(
											array(
												'theme_location' => 'header-my-account',
												'menu_id' => 'header-my-account-menu',
												'container' => false,
												'fallback_cb' => '',
												'walker'  => new BuddyBoss_SubMenuWrap(),
												'menu_class' => 'bb-my-account-menu',
											)
										);
									} else {
										do_action( THEME_HOOK_PREFIX . 'header_user_menu_items' );
									}
								} else {
									do_action( THEME_HOOK_PREFIX . 'header_user_menu_items' );
								}
								?>
							</ul>
						</div>
					</div>
				</div>
				<?php
			endif;
		}

		if ( ! is_user_logged_in() ) :
			?>

			<?php if ( $show_search && '4' !== $header_style ) : ?>
				<a href="#" class="header-search-link" data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Search', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-search"></i></a>
				<span class="search-separator bb-separator"></span>
				<?php
			endif;

			if ( $show_shopping_cart && class_exists( 'WooCommerce' ) ) :
				get_template_part( 'template-parts/cart-dropdown' );
			endif;
			if ( 'off' !== $profile_dropdown ) {
				?>
				<div class="bb-header-buttons">
					<a href="<?php echo esc_url( wp_login_url() ); ?>" class="button small outline signin-button link"><?php esc_html_e( 'Sign in', 'buddyboss-theme' ); ?></a>

					<?php if ( get_option( 'users_can_register' ) ) : ?>
						<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="button small signup"><?php esc_html_e( 'Sign up', 'buddyboss-theme' ); ?></a>
					<?php endif; ?>
				</div>
				<?php
			}

			endif;

			if (
				'3' === $header_style ||
				(
					class_exists( 'SFWD_LMS' ) &&
					buddyboss_is_learndash_inner()
				) ||
				(
					class_exists( 'LifterLMS' ) &&
					buddypanel_is_lifterlms_inner()
				)
			) :
			    echo buddypanel_position_right();
			endif;
		?>

	</div><!-- .header-aside-inner -->
</div><!-- #header-aside -->
