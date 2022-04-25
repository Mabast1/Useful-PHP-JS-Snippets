<?php 
/**
 * @package   PB Digital Course Plugin
 * @author    Paul Bright <paul@pbdigital.com.au>
 * @copyright  
 * @license   
 * @link      https://pbdigital.com.au
 *
 * Plugin Name:     PB Digital Course Plugin
 * Plugin URI:      https://pbdigital.com.au
 * Description:     Generates roadmap from learndash course
 * Version:         1.2.0
 * Author:          Paul Bright
 * Author URI:      https://pbdigital.com.au
 * Text Domain:     PB Digital Course Plugin
 * License:         {
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:     /languages
 * Requires PHP:    7.0
 * WordPress-Plugin-Boilerplate-Powered: v3.2.0
 */

//Plugin Update 
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://academy.pbdigital.com.au/plugin-updates/roadmap-plugin.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'pbd_course_roadmap'
);
add_action('admin_menu', 'pb_digital_register_submenu');

function pb_digital_register_submenu() {
   add_submenu_page( 'options-general.php', 'PB Digital', 'PB Digital', 'manage_options', 'pb-digital', 'pb_digital_submenu_page');
}
add_action('wp_footer', function(){
   $user_id = get_current_user_id();
   $show_bar = get_option( 'pbd_progress_bar',0);

   if ($show_bar){
      $pbd_rank_type = get_option( 'pbd_rank_type', 'levels' );
      $pbd_points_type = get_option( 'pbd_points_type', 'points');
      $pbd_coins_type = get_option( 'pbd_coins_type', 'coins');
      $pbd_redeem_page = get_option( 'pbd_redeem_page');
      
      $next_level_id = gamipress_get_next_user_rank_id($user_id, $pbd_rank_type);
      $requirements = gamipress_get_rank_requirements($next_level_id);
      $points_needed = get_post_meta($requirements[0]->ID, '_gamipress_points_required', true);
      $current_rank_id =  gamipress_get_user_rank_id ($user_id, $pbd_rank_type);
      $current_rank = get_the_title($current_rank_id);
      $current_points = gamipress_get_user_points($user_id, $pbd_points_type);
      $completion = round($current_points/ $points_needed * 100,0);
      $current_coins = gamipress_get_user_points($user_id,$pbd_coins_type);
      $rank = gamipress_get_rank_type($pbd_rank_type);
      
      if (!$pbd_redeem_page){
         $redeem_screen = 'javascript:void(0)';
      }else {
         $redeem_screen = get_permalink($pbd_redeem_page);
      }
      if (!empty($rank)){
         $rank_img = get_the_post_thumbnail_url($rank['ID'] );
      }
      $coins = gamipress_get_points_type($pbd_coins_type);
      if (!empty($coins)){
         $coins_img = get_the_post_thumbnail_url($coins['ID'] );
      }
      ?>
      <script>
         jQuery('document').ready(function($){
            $('.header-aside').prepend(`<div style="display: flex;align-items: center;margin-right: 35px" class="gamification">
            <div style="width: 50px;height: 50px;display: flex;justify-content: center;align-items: center;border-radius: 50%;background: <?=buddyboss_theme_get_option( 'accent_color' );?>" class="trophy">
               <img src="<?=$rank_img;?>" style="width: 19.36px">
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
         <a style="display: flex;align-items: center; margin-right: 35px" href="<?=$redeem_screen;?>" data-balloon-pos="down" data-balloon="Earn coins to unlock goodies" class="coins">
            <div style="display: flex;justify-content: center;align-items: center;border-radius: 50%;">
               <img src="<?=$coins_img;?>" style="max-width:40px;border:none;">
            </div>
            <div style="font-size: 16px;padding-left: 10px;font-weight: bold;" >
               <?=$current_coins;?>
            </div>
         </a>`);
         })
      </script>
   
   <?php
   }
});

function pb_digital_submenu_page(){
 
   if(function_exists("gamipress_get_rank_type")){
      #gamipress function is available
      ?>
      <div class = "wrap">
         <?php
         if ($_POST){
            check_admin_referer('save-settings', '_wpnonce_save-settings');
            update_option( 'pbd_rank_type',$_POST['rank'] );
            update_option( 'pbd_points_type',$_POST['points'] );
            update_option( 'pbd_coins_type',$_POST['coins'] );
            update_option( 'pbd_redeem_page',$_POST['redeem_page'] );
            update_option( 'pbd_progress_bar',$_POST['progress_bar'] );
            echo '<div class="notice notice-success is-dismissible">
             <p>Settings saved!</p>
            </div>';
         }
         
         ?>
         <h1>PB Digital</h1>
         <form action="" method="post" name="save_settings">
            <?php wp_nonce_field( 'save-settings', '_wpnonce_save-settings' ) ?>
            <table class="form-table" role="presentation">
               <tbody>
                  <tr>
                     <th>Show Progress Bar</th>
                     <td>
                        
                        <select name="progress_bar" class="progress_bar">
                           <option value="1">Enabled</option>
                           <option value="0">Disabled</option>
                        </select>

                     </td>
                  </tr>
                  <tr>
                     <th>Rank Type</th>
                     <td>
                        <?php
                        #Get rank types
                        $rank_types = gamipress_get_rank_types();
                        echo '<select name="rank" class="rank">';
                        if (!empty($rank_types)){
                           foreach ($rank_types as $rank) {
                              $data = get_post($rank['ID']);
                              echo '<option value="'.$data->post_name.'">'.$rank['plural_name'].'</option>';
                           }
                        }else {
                           echo '<option>No suitable rank types found</option>';
                        }
                        echo '</select>';
                        ?>
                     </td>
                  </tr>
                  
                  <tr>
                     <th>Points Type</th>
                     <td>
                        <?php
                     
                        #Get point types
                        $point_types = gamipress_get_points_types();
                        
                        echo '<select name="points" class="points">';
                        
                        if (!empty($point_types)){
                           foreach ($point_types as $points) {
                              $data = get_post($points['ID']);
                              echo '<option value="'.$data->post_name.'">'.$points['plural_name'].'</option>';
                           }
                        }else {
                           echo '<option>No suitable point types found</option>';
                        }
                        echo '</select>';
                        
                        ?>
                     </td>
                  </tr>
                  <tr>
                     <th>Coins Type</th>
                     <td>
                        <?php
                     
                        #Get point types
                        $point_types = gamipress_get_points_types();
                        
                        echo '<select name="coins" class="coins">';
                        
                        if (!empty($point_types)){
                           foreach ($point_types as $points) {
                              $data = get_post($points['ID']);
                              echo '<option value="'.$data->post_name.'">'.$points['plural_name'].'</option>';
                           }
                        }else {
                           echo '<option>No suitable point types found</option>';
                        }
                        echo '</select>';
                        
                        ?>
                     </td>
                  </tr>
                  <tr>
                     <th>Redeem Screen</th>
                     <td>
                        <?php
                     
                        #Get point types
                        $pages = get_posts(['post_type'=>'page', 'numberposts'=> -1,'orderby'=>'title','order'=>'ASC']);
                        
                        
                        echo '<select name="redeem_page" class="redeem_page">';
                        
                        if (!empty($pages)){
                           foreach ($pages as $page) {
                              
                              echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
                           }
                        }else {
                           echo '<option>No pages found</option>';
                        }
                        echo '</select>';
                        
                        ?>
                     </td>
                  </tr>
               </tbody>
            </table>
            <p>
               <input class="button button-primary button-large" type="submit" value="Save Changes" />
            </p>
         </form>
      </div>
      <script>
      jQuery(document).ready(function($){
         $('.progress_bar').val('<?=get_option( 'pbd_progress_bar',0);?>');
         $('.rank').val('<?=get_option( 'pbd_rank_type');?>');
         
         $('.points').val('<?=get_option( 'pbd_points_type');?>');
         $('.coins').val('<?=get_option( 'pbd_coins_type');?>');
         $('.redeem_page').val('<?=get_option( 'pbd_redeem_page');?>');
      });
      </script>
      <?php
   }
}

if(!function_exists("pbd_courses_path")){

    add_shortcode( 'pbd_courses_path', 'pbd_courses_path' );
    function pbd_courses_path ($args) {

        $user_id = get_current_user_id();
        $course_id = $args['course_id'];
        
        $user_meta = get_user_meta($user_id);
        $course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);

        $lessons = learndash_get_lesson_list($course_id);
        $lessons_completed = 0;
        $has_started = false;
        if (isset($course_progress[$course_id])){
            $has_started = true;
        }
        if (isset($course_progress[$course_id]['lessons']))
        {
            $lessons_completed = count($course_progress[$course_id]['lessons']);
            $topics_completed = count($course_progress[$course_id]['topics']);
        }

        $coursecomplete = $_GET['completed'];

        $completed = $_GET['completed'];
        $incomplete = $_GET['incomplete'];
        $previous_completed = false;
        ob_start();

    

        ?>

        <style type="text/css">
        #courses-timeline {
            position: relative;
            max-width: 1140px;
            width: 100%;
            margin: 0 auto;
            z-index: 1;
        }

        #courses-timeline .timeline-start::after {
            content: "";
            position: absolute;
            width: 14px;
            height: 200px;
            max-height: 0;
            -webkit-transition: max-height 0.15s ease-out;
            transition: max-height 0.15s ease-out;
            background: #02b4b4;
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
            top: 106px;
        }

        @media only screen and (max-width: 830px) {
            #courses-timeline .timeline-start::after {
                left: 50px;
            }
        }

        @media only screen and (max-width: 601px) {
            #courses-timeline .timeline-start::after {
                left: 40px;
                top: 80px;
            }
        }

        #courses-timeline .timeline-start.completed::after,
        #courses-timeline .timeline-start.active::after {
            max-height: 200px;
            -webkit-transition: max-height 0.25s ease-in;
            transition: max-height 0.25s ease-in;
        }

        #courses-timeline .btn-start {
            background: #02b4b4;
            border: 10px solid #c0ecec;
            border-radius: 100px;
            width: 106px;
            height: 106px;
            margin: 0 auto;
            font-weight: 600;
            font-size: 16px;
            line-height: 24px;
            color: #ffffff;
            display: block;
            text-transform: capitalize;
            position: relative;
            z-index: 1;
        }

        @media only screen and (max-width: 830px) {
        #courses-timeline .btn-start {
            margin-left: -2px;
        }
        }

        @media only screen and (max-width: 601px) {
        #courses-timeline .btn-start {
            width: 80px;
            height: 80px;
            margin-left: 0;
            padding: 0;
            border-width: 8px;
        }
        }

        #courses-timeline .timeline-end {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            width: 96px;
            height: 96px;
            background: #ffffff;
            border: 10px solid #f3f3f3;
            margin: 0 auto;
            border-radius: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        @media only screen and (max-width: 830px) {
        #courses-timeline .timeline-end {
            margin: 0;
        }
        }

        @media only screen and (max-width: 601px) {
        #courses-timeline .timeline-end {
            width: 80px;
            height: 80px;
            border-width: 8px;
        }
        }

        #courses-timeline .timeline-end.completed {
            background: #02b4b4;
            border: 10px solid #c0ecec;
        }

        #courses-timeline .timeline-end.completed svg path {
            stroke: #fff;
        }

        #courses-timeline .courses-center-line {
            position: absolute;
            width: 14px;
            height: 100%;
            top: 0;
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
            background: #f3f3f3;
            z-index: -1;
        }

        @media only screen and (max-width: 830px) {
            #courses-timeline .courses-center-line {
                margin-left: 0;
                left: 50px;
            }
        }

        @media only screen and (max-width: 601px) {
            #courses-timeline .courses-center-line {
                left: 40px;
            }
        }

        #courses-timeline .courses-timeline-content {
            padding-top: 200px;
        }

        @media only screen and (max-width: 601px) {
            #courses-timeline .courses-timeline-content {
                padding-top: 156px;
            }
        }

        #courses-timeline .content-course__details {
            display: -ms-grid;
            display: grid;
            -ms-grid-columns: 100px 1fr;
            grid-template-columns: 100px 1fr;
            -webkit-column-gap: 20px;
            column-gap: 20px;
        }

        @media only screen and (max-width: 601px) {
            #courses-timeline .content-course__details {
                -ms-grid-columns: 1fr;
                grid-template-columns: 1fr;
                row-gap: 20px;
                -webkit-column-gap: 0;
                column-gap: 0;
            }
        }

        #courses-timeline .content-course__photo img {
            border-radius: 20px;
        }

        #courses-timeline .content-course__info {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        #courses-timeline .content-course__info h3 {
            font-weight: 600;
            font-size: 20px;
            line-height: 30px;
            color: #4d5e70;
            margin: 0 0 10px;
        }

        #courses-timeline .content-course__info p {
            font-weight: normal;
            font-size: 14px;
            line-height: 24px;
            color: #8a9197;
            margin: 0;
        }

        #courses-timeline .content-course__completed {
            font-weight: normal;
            font-size: 14px;
            line-height: 21px;
            color: #8a9197;
        }

        #courses-timeline .content-course__progressbar {
            max-width: 100%;
            width: 100%;
            height: 6px;
            background: #f3f3f3;
            border-radius: 100px;
            margin-top: 10px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }

        #courses-timeline .content-course__progressbar-inner {
            display: inline-block;
            height: 6px;
            background: #915dbc;
            border-radius: 100px;
            max-width: 100%;
            -webkit-transition: 0.5s all ease;
            transition: 0.5s all ease;
        }

        #courses-timeline .content-course__lesson {
            font-weight: 600;
            font-size: 24px;
            line-height: 36px;
            text-align: center;
            color: #4d5e70;
            background: #ffffff;
            border: 10px solid #f3f3f3;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            position: absolute;
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
            top: 0;
            -webkit-transition: border-color 0.5s ease-out;
            transition: border-color 0.5s ease-out;
        }

        @media only screen and (max-width: 830px) {
            #courses-timeline .content-course__lesson {
                left: 50px;
            }
        }

        @media only screen and (max-width: 601px) {
            #courses-timeline .content-course__lesson {
                border-width: 5px;
                font-size: 18px;
                line-height: 1px;
                width: 60px;
                height: 60px;
                left: 40px;
            }
        }

        .timeline-article {
            width: 100%;
            height: 100%;
            position: relative;
            padding-top: 80px;
        }

        @media only screen and (max-width: 601px) {
            .timeline-article {
                padding-top: 60px;
            }
        }

        .timeline-article.active .content-course__lesson {
            border-color: #c0ecec !important;
        }

        .timeline-article::before {
            content: "";
            position: absolute;
            width: 14px;
            height: 100%;
            max-height: 0;
            -webkit-transition: max-height 0.15s ease-out;
            transition: max-height 0.15s ease-out;
            background: #02b4b4;
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        @media only screen and (max-width: 830px) {
            .timeline-article::before {
                left: 50px;
            }
        }

        @media only screen and (max-width: 601px) {
            .timeline-article::before {
                left: 40px;
            }
        }

        .timeline-article.completed .content-course__lesson {
            border-color: #c0ecec !important;
        }

        .timeline-article.completed .content-course__progressbar-inner {
            width: 100%;
        }

        .timeline-article.completed::before {
            max-height: 98%;
            -webkit-transition: max-height 0.25s ease-in;
            transition: max-height 0.25s ease-in;
        }

        .timeline-article::after {
            content: "";
            display: block;
            clear: both;
        }

        .timeline-article--content {
            max-width: 42%;
            width: 100%;
        }

        @media only screen and (max-width: 830px) {
            .timeline-article--content {
                max-width: 100%;
                width: auto;
                float: none;
                margin-left: 130px;
                min-height: 53px;
            }
        }

        @media only screen and (max-width: 601px) {
            .timeline-article--content {
                margin-left: 100px;
            }
        }

        .timeline-article:nth-child(odd) .timeline-article--content {
            float: right;
        }

        .timeline-article:nth-child(odd) .timeline-article--inner .arrow {
            left: -27px;
        }

        .timeline-article:nth-child(even) .timeline-article--content {
            float: left;
        }

        .timeline-article:nth-child(even) .timeline-article--inner .arrow {
            right: -27px;
        }

        @media only screen and (max-width: 830px) {
            .timeline-article:nth-child(even) .timeline-article--inner .arrow {
                left: -27px;
                right: auto;
            }
        }

        .timeline-article .timeline-author {
            display: block;
            font-weight: 400;
            font-size: 14px;
            line-height: 24px;
            color: #242424;
            text-align: right;
        }

        .timeline-article .arrow {
            position: absolute;
            z-index: -1;
            top: 42px;
        }

        .timeline-article--inner {
            display: -ms-grid;
            display: grid;
            row-gap: 20px;
            position: relative;
            width: auto;
            padding: 20px 20px 23px;
            z-index: 1;
            background: #ffffff;
            -webkit-filter: drop-shadow(0px 0px 30px rgba(0, 0, 0, 0.05));
            filter: drop-shadow(0px 0px 30px rgba(0, 0, 0, 0.05));
            border-radius: 20px;
            top: -110px;
        }

        @media only screen and (max-width: 601px) {
            .timeline-article--inner {
                top: -100px;
            }
        }

        .path-inner .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #fff;
            fill: none;
            -webkit-animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
                    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .path-inner .checkmark {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: none;
            stroke-width: 2;
            stroke: #02b4b4;
            stroke-miterlimit: 10;
            position: absolute;
            -webkit-animation: fill 0.4s ease-in-out 0.4s forwards,
            scale 0.3s ease-in-out 0.9s both;
                    animation: fill 0.4s ease-in-out 0.4s forwards,
            scale 0.3s ease-in-out 0.9s both;
        }

        .path-inner .checkmark__check {
            -webkit-transform-origin: 50% 50%;
                    transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            -webkit-animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
                    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        
        /* CSS Overrides*/
        <?php 
        if(!empty($args["ring-color-active"])){
            ?>
            .timeline-article.active .content-course__lesson {
                border-color: <?=$args["ring-color-active"]?> !important;
            }
            <?php
        }
        if(!empty($args["ring-color-completed"])){
            ?>
            .timeline-article.completed .content-course__lesson {
                border-color: <?=$args["ring-color-completed"]?> !important;
            }
            <?php 
        }

        if(!empty($args["ring-color"])){
            ?>
            #courses-timeline .content-course__lesson {
                border: 10px solid <?=$args["ring-color"]?>;
            }
            <?php 
        }

        if(!empty($args["path-color-completed"])){
            ?>
            .timeline-article::before,
            #courses-timeline .timeline-start::after {
                background: <?=$args["path-color-completed"]?>;
            }
            <?php
        }

        if(!empty($args["path-color"])){
            ?>
            #courses-timeline .courses-center-line{
                background: <?=$args["path-color"]?>;
            }
            <?php
        }

        if(!empty($args["progress-color"])){
            ?>
            #courses-timeline .content-course__progressbar-inner{
                background: <?=$args["progress-color"]?>;
            }
            <?php
        }

        ?>
        #courses-timeline .btn-start {
            <?php if(!empty($args["start-background-color"])){ ?> background: <?=$args["start-background-color"]?>; <?php }?>
            <?php if(!empty($args["start-ring-color"])){ ?>border: 10px solid <?=$args["start-ring-color"]?>; <?php }?>
            <?php if(!empty($args["start-color"])){ ?>color: <?=$args["start-color"]?>; <?php }?>
        }
        #courses-timeline .timeline-end.completed {
            <?php if(!empty($args["completed-background-color"])){ ?> background: <?=$args["completed-background-color"]?>; <?php }?>
            <?php if(!empty($args["completed-ring-color"])){ ?>border: 10px solid <?=$args["completed-ring-color"]?>; <?php }?>
        }

        <?php if(!empty($args["completed-color"])){ ?>
        #courses-timeline .timeline-end.completed svg path {
            stroke: <?=$args["completed-color"]?>;
        }
        <?php }?>

        <?php
        if(!empty($args["checkmak-color"])){
            ?>
            .path-inner .checkmark {
                stroke: <?=$args["checkmak-color"]?>;
            }
        <?php
        }
        /*
        [pbd_courses_path course_id=2671  
        start-ring-color="#c3e2f1" 
        start-color="#000" 
        start-background-color="#3897ff" 
        ring-color="#c3e2f1" 
        ring-color-completed="#3897ff" 
        ring-color-active="#beddff"   
        path-color="#e2f5ff" 
        path-color-completed="#3897ff"  
        progress-color="#006bc6"
        checkmak-color="#006bc6"
        completed-ring-color="#c3e2f1"
        completed-background-color="#3897ff"
        completed-color="#000"
        ]
        */
        ?>
    

        </style>

        <div class="path">
            <div class="path-inner">
                <!-- Vertical Timeline -->
                <section id="courses-timeline">
                    <div class="timeline-start completed
                    <?=($completed) ? 'completed' : '';?>
                    <?=($incomplete) ? 'completed' : '';?>"><button class="btn-start">Start</button></div>
                    <div class="courses-center-line"></div>
                    <div class="courses-timeline-content">
                        <?php 
                            $coursecomplete = false;
                            foreach ($lessons as $k=>$lesson){
                            $completed = false;
                            $lesson_id = $lesson->ID;
                            $topics = learndash_topic_dots($lesson_id, false, 'array');
                            $topics = count($topics);
                            $topics_completed = count($course_progress[$course_id]['topics'][$lesson_id]);

                            if( empty($course_progress[$course_id]['topics'][$lesson_id]) ){                        

                                if ( $course_progress[ $course_id ]['lessons'][ $lesson_id ] ){
                                    
                                    if(empty($topics)){
                                        $topics = 1;
                                        $topics_completed = 1;
                                    }else{
                                        $topics_completed = count($topics);
                                    } 
                                } 
                            }
                            $percent_completed = round($topics_completed / $topics * 100,0);

                            if($percent_completed >= 100 ) $completed = true;
                            
                        ?>
                        <!-- Article -->
                        <div class="timeline-article 
                        <?=($completed) ? 'completed' : '';?>
                        <?=($previous_completed) ? 'active' : '';?>"
                        onclick="window.location = '<?=get_permalink($lesson->ID);?>'"
                        style="cursor:pointer;">
                            
                            <div class="timeline-article--content">
                                <div class="timeline-article--inner">
                                    <svg class="arrow" width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28.284 14L56.57 28.284 28.284 42.57 0 28.284 28.284 14z" fill="#fff"/></svg>
                                    <div class="content-course__details">
                                        <div class="content-course__photo">
                                            <img src="<?=get_the_post_thumbnail_url( $lesson->ID);?>" alt="Yoga Beginner course">
                                        </div>
                                        <div class="content-course__info">
                                            <h3><?=$lesson->post_title;?></h3>
                                            <p><?=get_post_meta($lesson->ID, '_learndash_course_grid_short_description', true);?></p>
                                        </div>
                                    </div>
                                    <div class="content-course__progress">
                                        <div class="content-course__completed">
                                            <?=$topics_completed;?> of <?=$topics;?> lessons completed
                                        </div>
                                        <div class="content-course__progressbar">
                                            <div class="content-course__progressbar-inner" style="width:<?=$percent_completed;?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-course__lesson">
                                <span><?php echo $k + 1;?></span>
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
                            </div>
                        </div>
                        <!-- // Article -->
                        <?php 
                            if ($completed) { $previous_completed = true; } else { $previous_completed = false; }
                        } ?>
                        
                    </div>
                    <div class="timeline-end 
                        <?=($completed) ? 'completed' : '';?>"><svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26.546 10.182c3 0 5.454-3.546 5.454-6.818h-5.454M7.454 10.182c-3 0-5.455-3.546-5.455-6.818h5.455M17 18.5v9.41M23.818 32H10.182m10.909-4.09h-8.182M26.545 8.817V2H7.456v6.818A9.487 9.487 0 0017 18.364a9.487 9.487 0 009.546-9.546z" stroke="#4D5E70" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                
                    <?php 
                    if($completed) $coursecomplete = true;
                    ?>
                </section>
                <!-- // Vertical Timeline -->


                
            

            </div>
        </div>
        
        <?php
        
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

function pbd_custom_scripts() {   
    wp_enqueue_script( 'jquery-wait', plugin_dir_url( __FILE__ ) . 'jquery.wait.js' );
    wp_enqueue_script( 'confetti', plugin_dir_url( __FILE__ ) . 'confetti.js' );

    // start curve path
    wp_enqueue_script('highlight-pack-js', plugin_dir_url( __FILE__ ).'/assets/circle-progress/js/highlight.pack.js', '', '1.0.1'.mt_rand());
    wp_enqueue_script('query-circle-js', plugin_dir_url( __FILE__ ).'/assets/circle-progress/js/circle-progress.js', array('jquery-core'), '1.0.1'.mt_rand());
    wp_enqueue_script('circle-progres-js', plugin_dir_url( __FILE__ ).'/assets/js/circle-progress.min.js', '', '1.0,0');

}
add_action('wp_enqueue_scripts', 'pbd_custom_scripts');

if(!function_exists("pbd_courses_path_scripts")){
    function pbd_courses_path_scripts() {
        
        ?>
        
        <script>
            jQuery(document).ready(function ($) {
                 
                $('.btn-start').on('click',function () {
                    $(this).parent().addClass('active');
                    $('.timeline-article:first-child').addClass('active');
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $(".timeline-article:first-child").offset().top - 100
                    }, 1000);
                });

            
                $(window).load( () => {
                    //scroll to trophy
                    <?php if ($completed): ?>
                        $([document.documentElement, document.body]).animate({
                           // scrollTop: $(".timeline-end").offset().top - 100
                        }, 1000);	
                    <?php endif; ?>

                    //scroll to active course
                    <?php if ($incomplete): ?>
                        $([document.documentElement, document.body]).animate({
                            //scrollTop: $(".timeline-article.active").offset().top - 100
                        }, 1000);
                    <?php endif; ?>


                    let time =  500;
                    let delay = 500;

                    $('.timeline-article').each(function () {
                        setTimeout(() => {
                            if ($(this).hasClass('completed')) {
                                $('.content-course__lesson .checkmark',this).show();
                                $('html, body').animate({
                                    //scrollTop: $(this).offset().top - 120
                                }, delay);
                            }else {
                                if ($(this).hasClass('active'))
                                $('html, body').animate({
                                    //scrollTop: $(this).offset().top - 120
                                }, delay);
                            }
                        }, time);
                        time+=delay;
                        
                        
                    });
                });
            

            
            });
        </script>
        <?php
    }
    add_action('wp_footer','pbd_courses_path_scripts');
}


add_shortcode("pbd_course_roadmap_curve", function($attr){
    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $autoscroll = true;
    if($attr["autoscroll"]=="false") $autoscroll = false;

    ob_start();
    ?>
    <style type="text/css">
    .page-template-pt-pathdev {
        margin:100px auto;
    }
    .pathway-course-path{
        display:block
    }
    .pathway-courses--item{
        z-index:999
    }
    .pathway-course-path.left .vertical-line
    {
        background: #CED3D4;
        width: 412px;
        height: 23px;
        position: absolute;
        left: -135px;
    }
    .right-vertical-line {
        background: #CED3D4;
        width: 420px;
        height: 27px;
        position: absolute;
        right: -205px;
        top: 3px;;
    }
    .path-container{
        position:relative
    }
    .path-container-0 .right-vertical-line {
        width: 250px;
        top: 0px;
    }

    /* new path */
    .path {
        max-width: 900px;
        margin: 0 auto;
        padding: 100px 50px 200px;
        position: relative;
        width:calc(100% - 40px);
    }
    @media (max-width:601px) {
        .path {
            zoom:.6;
            padding-left:20px;
            padding-right:20px;
        }
    }
    .path-svg {
        display:block;
        position:relative;
    }
    .path-svg svg {
        display:block;
        width: auto;
        transform:translateX(-50%);
        position:relative;
    }
    .path-svg.completed .path-btn,
    .path-svg.active .path-btn{
        border-color:#fff;
        box-shadow: 0px 6px 0px #9ECFEE, 0px 10px 8px rgba(46, 66, 90, 0.15);
    }
    .path-svg svg .line {
        transition: 0.5s all ease;
    }
    .path-svg svg .stroke {
        transition: 0.5s all ease;
        stroke-dasharray: 0;
        stroke-dashoffset: -1000;
    }
    .path-svg.completed svg .line,
    .path-svg svg.active .line{
        stroke: #efefef;
        stroke-dashoffset: -500;
    }
    .path-svg.completed svg .stroke,
    .path-svg svg.active .stroke {
        stroke-dasharray: 3, 6;
        stroke: #3da0dd;
    }

    .starting-path svg{
        max-width: 236px;
        display: block;
        left:50%;
    }

    .path-right svg{
        position:relative;
        max-width: 283px;
        left:calc(50% + 138px);
        width: 100%;
        transform: translateX(-50%);
        margin-top: -20px;
        pointer-events:none;
    }

    .path-left svg{
        max-width:282px;
        left: calc(50% - 139px);
        margin-top: -20px;
    }
    .path-middle svg{
        max-width: 236px;
        left: 50%;
        margin-top: -20.1px;
        display: block;
    }
    button.btn-start {
        position: absolute;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #FFC262 0%, #FF9B40 100%);
        border: 8px solid #fff;
        box-shadow: 0px 6px 0px #ffbc5c, 0px 10px 8px rgb(46 66 90 / 15%);
        left: calc(50% - 150px);
        transform: translateX(-50%);
        z-index: 10;
        margin-top: -52px;
    }
    button.path-btn {
        cursor:pointer;
        width: 120px;
        height: 120px;
        position: absolute;
        background: 0;
        border: 8px solid #DDF49D;
        padding: 0;
        background: #FFFFFF;
        transition:.5s all ease;
        box-shadow: 0px 6px 0px #B9E676, 0px 10px 8px rgba(46, 66, 90, 0.15);
        z-index:10;
    }
    button.path-btn img {
        border-radius:100%;
    }
    .path-lesson {
        width: 200px;
        position: absolute;
        text-align: center;
        top: 80px;
    }
    .path-lesson h3 {
        font-weight: 600;
        font-size: 20px;
        line-height: 30px;
        text-align: center;
        color: #333;
        margin:0;
    }
    button.path-btn img {
        object-fit:cover;
    }
    .path-right button.path-btn {
        left: calc(50% + 50px);
        top: -52px;
    }
    .path-right .path-lesson {
        left: calc(50% + 10px);
    }
    .path-left button.path-btn{
        left: calc(50% - 59px);
        top: -50px;
    }
    .path-left .path-lesson {
        left: calc(50% - 100px);
    }
    .path-middle .path-btn {
        left: calc(50% - 200px);
        top: -50px;
    }
    .path-middle .path-lesson {
        left:calc(50% - 240px);
    }

    .path-right.path-finish button.path-btn {
        left: calc(50% + 50px);
        top: -70px;
    }

    .path-left.path-finish button.path-btn {
        top:-70px;
    }
    
    .path-middle.path-finish button.path-btn {
        top:-70px;
    }
    
    .path-finish .path-lesson{
        top:63px;
    }

    
    .path-svg .courseprogress{
    position: absolute;
    bottom: -8px;
    right: -8px;
    border-radius: 100px;
    width: 44px;
    height: 44px;
    visibility: hidden;
    }

    @media only screen and (max-width: 601px) {
        .path-svg .courseprogress{
            bottom: -18px;
            right: -18px;
        }
    }

    .path-svg .courseprogress span{
        width: 36px;
        height: 36px;
        position: absolute;
        top: 50%;
        left: 50%;
        background: #487dc0;
        border-radius: 100%;
        -webkit-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
            -ms-flex-align: center;
                align-items: center;
        -webkit-box-pack: center;
            -ms-flex-pack: center;
                justify-content: center;
        font-family: "Poppins";
        font-weight: 500;
        font-size: 12px;
        line-height: 18px;
        text-align: center;
        color: #ffffff;
    }

    .path-svg .courseprogress .checkmark{
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: none;
        stroke-width: 2;
        stroke: #fff;
        stroke-miterlimit: 10;
        -webkit-animation: fill 0.1s ease-in-out 0.1s forwards,
        scale 0.2s ease-in-out 0.5s both;
                animation: fill 0.1s ease-in-out 0.1s forwards,
        scale 0.2s ease-in-out 0.5s both;
    }

    .path-svg .courseprogress .checkmark__circle{
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #24d8a2;
        fill: none;
        -webkit-animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
                animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .path-svg .courseprogress .checkmark__check{
        -webkit-transform-origin: 50% 50%;
                transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        -webkit-animation: stroke 0.2s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
                animation: stroke 0.2s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
    }
    .path-svg.completed .courseprogress,
    .path-svg.active .courseprogress {
        visibility: visible;
    }

    .path-svg .courseprogress .checkmark__check{
        -webkit-transform-origin: 50% 50%;
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        -webkit-animation: stroke 0.2s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
        animation: stroke 0.2s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
    }
    .path-svg.completed .courseprogress .checkmark{
        display:block;
        left:0px;
        top:10px;
    }
    /* Styling overrides*/
    <?php 
    if(!empty($attr["path_active_background"])){
    ?>
    .path-svg.completed svg .line, .path-svg svg.active .line {
        stroke: <?=$attr["path_active_background"]?>;
    }
    <?php 
    }
    if(!empty($attr["path_stroke_color"])){
        ?>
        .path-svg.completed svg .stroke, 
        .path-svg svg.active .stroke {
            stroke: <?=$attr["path_stroke_color"]?>;
        }
        <?php
    }
    if(!empty($attr["path_background"])){
        $default_path_color = $attr["path_background"];
    }else{
        $default_path_color = "#DDF49D";
    }

    if(!empty($attr["start_button_color"])){
        ?>
        button.btn-start {
            background: linear-gradient(135deg, <?=$attr["start_button_color"]?> 0%, <?=$attr["start_button_color"]?> 100%);
            box-shadow: 0px 6px 0px <?=$attr["start_button_color"]?>, 0px 10px 8px rgb(46 66 90 / 15%);
        }
        <?php
    }

    if(!empty($attr["ring_default_color"])){
        ?>
        .path-svg .path-btn {
            border-color: <?=$attr["ring_default_color"]?>;
        }
        <?php
    }
    if(!empty($attr["ring_default_shadow_color"])){
        ?>
        .path-svg .path-btn {
            box-shadow: 0px 6px 0px <?=$attr["ring_default_shadow_color"]?>, 0px 10px 8px rgb(46 66 90 / 15%);
        }
        <?php
    }

    if(!empty($attr["ring_completed_color"])){
        ?>
        .path-svg.completed .path-btn {
            border-color: <?=$attr["ring_completed_color"]?>;
        }
        <?php
    }
    if(!empty($attr["ring_completed_shadow_color"])){
        ?>
        .path-svg.completed .path-btn {
            box-shadow: 0px 6px 0px <?=$attr["ring_completed_shadow_color"]?>, 0px 10px 8px rgb(46 66 90 / 15%);
        }
        <?php
    }

    if(!empty($attr["ring_active_color"])){
        ?>
        .path-svg.active .path-btn {
            border-color: <?=$attr["ring_active_color"]?>;
        }
        <?php
    }
    if(!empty($attr["ring_active_shadow_color"])){
        ?>
        .path-svg.active .path-btn {
            box-shadow: 0px 6px 0px <?=$attr["ring_active_shadow_color"]?>, 0px 10px 8px rgb(46 66 90 / 15%);
        }
        <?php
    }
    
    if(!empty($attr["title_color"])){
        ?>
        .path-lesson h3 {
            color: <?=$attr["title_color"]?>;
        }
        <?php
    }

    if(!empty($attr["finish_button_shadow_color"])){
        ?>
        .path-svg.path-finish.active .path-btn {
            box-shadow: 0px 6px 0px <?=$attr["finish_button_shadow_color"]?>, 0px 10px 8px rgb(46 66 90 / 15%);
        }
        <?php
    }
    if(!empty($attr["finish_button_background_color"])){
        ?>
        .path-svg.path-finish.active .path-btn {
            background:<?=$attr["finish_button_background_color"]?>
        }
        <?php
    }

    if(!empty($attr["progress_completed_background"])){
        ?>
        .path-svg .courseprogress .checkmark__circle {
            stroke: <?=$attr["progress_completed_background"]?>;
            fill: <?=$attr["progress_completed_background"]?>;
        }
        <?php
    }

    if(!empty($attr["progress_default_background"])){
        ?>
        .path-svg .courseprogress span {
            background: <?=$attr["progress_default_background"]?>;
        }
        <?php
    }

    if(!empty($attr["progress_text_color"])){
        ?>
        .path-svg .courseprogress span {
            color: <?=$attr["progress_text_color"]?>;
        }
        .path-svg .courseprogress .checkmark__check{
            stroke:<?=$attr["progress_text_color"]?>;
        }
        <?php
    }
    ?>
    /* end styling overrides*/

    </style>
    <div class="page-template-pt-pathdev">
        <link rel="stylesheet" href="<?=plugin_dir_url( __FILE__ )?>assets/css/style.css"/>
        <div class="path">
            <!-- starting path -->
            <div class="path-svg starting-path" data-iscomplete="1" data-progress="1">
                <button class="btn-start"><?=($attr["start_button_text"]) ? $attr["start_button_text"]:"Start"?></button>
                <svg class="" viewBox="0 0 236 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M234 10H2" stroke="<?=$default_path_color?>" stroke-width="20"/>
                    <path class="stroke" d="M234 10H2" stroke="<?=$default_path_color?>" stroke-width="3" stroke-linecap="round" stroke-dasharray="3 8"/>
                </svg>
            </div>
            <!-- end starting path -->
            <?php 
            $classSvg[0]["class"] = "path-right";
            $classSvg[0]["svg"] = '
                <svg class="" viewBox="0 0 283 328" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M119 10c85.052 0 154 68.948 154 154s-68.948 154-154 154H2" stroke="'.$default_path_color.'" stroke-width="20"/>
                    <path class="stroke" d="M119 10c85.052 0 154 68.948 154 154s-68.948 154-154 154H2" stroke="'.$default_path_color.'" stroke-width="3" stroke-linecap="round" stroke-dasharray="3 8"/>
                </svg>
            ';
            
            $classSvg[1]["class"] = "path-left";
            $classSvg[1]["svg"] = '
                <svg class="" viewBox="0 0 282 328" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M164 318C78.9482 318 10 249.052 10 164C10 78.9482 78.9481 10 164 10H280" stroke="'.$default_path_color.'" stroke-width="20"/>
                    <path class="stroke" d="M164 318C78.9482 318 10 249.052 10 164C10 78.9482 78.9481 10 164 10H280" stroke="'.$default_path_color.'" stroke-width="3" stroke-linecap="round" stroke-dasharray="3 8"/>
                </svg>
            ';

            $classSvg[2]["class"] = "path-middle";
            $classSvg[2]["svg"] = '
                <svg class="" viewBox="0 0 236 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M234 10H2" stroke="'.$default_path_color.'" stroke-width="20"/>
                    <path class="stroke" d="M234 10H2" stroke="'.$default_path_color.'" stroke-width="3" stroke-linecap="round" stroke-dasharray="3 8"/>
                </svg>
            ';

            
            $steps_count = 6;
            $show_path = true;
            $left = 35;
            $path_is_right = true;
            $course_id = $attr["course_id"];

            $reverse_array = true;
            //look into getting the course id dynamically
            $course_lessons = learndash_get_lesson_list($course_id);
            $course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);

            $j = 0;
            $path_count = 0;
            $path_completed_count = 0;
            foreach($course_lessons as $lesson){
                $class = $classSvg[$j]["class"];
                $svg = $classSvg[$j]["svg"];
                $lesson_id = $lesson->ID;

                $lesson_id = $lesson->ID;
                $topics = learndash_topic_dots($lesson_id, false, 'array');
                $topics = count($topics);
                $topics_completed = count($course_progress[$course_id]['topics'][$lesson_id]);

                if( empty($course_progress[$course_id]['topics'][$lesson_id]) ){                        

                    if ( $course_progress[ $course_id ]['lessons'][ $lesson_id ] ){
                        
                        if(empty($topics)){
                            $topics = 1;
                            $topics_completed = 1;
                        }else{
                            $topics_completed = count($topics);
                        } 
                    } 
                }
                $percent_completed = round($topics_completed / $topics * 100,0);

                $iscomplete = 0;
                
                if($percent_completed >= 100){
                    $iscomplete = 1;
                    $path_completed_count++;
                }
                $path_count++;
                ?>
                <div 
                    data-progress="<?=($percent_completed > 0) ? $percent_completed / 100:0?>"
                    data-href="<?=get_permalink($lesson_id)?>" 
                    data-iscomplete="<?=$iscomplete?>" 
                    data-topicscount="<?=$topics?>" 
                    data-percent="<?=$percent_completed?>" 
                    data-topicscompleted="<?=$topics_completed?>" 
                    data-j="<?=$j?>" class="path-svg <?=$class?> ">
                    <button class="path-btn">
                        <img src="<?=get_the_post_thumbnail_url($lesson_id)?>" alt="">

                        <div class="courseprogress">
                            <?php   
                                $topics = learndash_topic_dots($lesson_id, false, 'array');
                                $topics = count($topics);
                                $topics_completed = count($course_progress[$course_id]['topics'][$lesson_id]);
                                $percent_completed = round($topics_completed / $topics * 100,0);
                            if($iscomplete){
                                ?>
								<span>
                                <svg class="checkmark" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="checkmark__circle" cx="14" cy="14" r="14" fill="#000000"></circle><path class="checkmark__check" d="M20.817 10l-8 8-3.637-3.636" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </span>
                                <?php
                            }else{
                                ?>
                                <span><?=$percent_completed;?>%</span>
                                <?php 
                            }
                            ?>
                        </div>
                        
                    </button>
                    <div class="path-lesson">
                        <h3><?=$lesson->post_title?></h3>
                    </div>
                    <?=$svg?>
                </div>
                <?php 
                $j++; 
                if($j>2) $j = 0;
            }

            ?>
            <div data-pathcount="<?=$path_count?>" 
                data-path-completed-count="<?=$path_completed_count?>" 
                class="path-svg <?=$classSvg[$j]["class"]?> path-finish" 
                data-iscomplete="<?=$iscomplete?>" >
                <button class="path-btn">
                    <?php 
                    if(!empty($attr["finish_image_url"])){
                        ?>
                        <img src="<?=$attr["finish_image_url"]?>" alt="" data-fromattr="1" />
                        <?php
                    }else{
                        ?>
                        <img src="<?=plugin_dir_url( __FILE__ )?>assets/img/finish.png" />
                        <?php
                    }
                    ?>
                    
                </button>
                <div class="path-lesson">
                    <h3>
                    <?=($attr["finish_text"]) ? $attr["finish_text"]:"Finish"?>
                    </h3>
                    
                </div>
            </div>

        </div>
        
                        
    </div>
            
    <script>
    jQuery(document).ready(function ($) {
        $('.btn-start').on('click', function () {
            $(this).next().addClass('active');
            $(this).parent().addClass('completed').next().wait(500).addClass('active');

            setTimeout(() => {                        
                $('html, body').animate({
                    scrollTop: $(".path-svg.active").offset().top - 160
                }, 500);
            }, 500);
        });
        $(document).on("click", ".path-svg.active,.path-svg.completed", e => {
            console.log(e.currentTarget);
            if ( $(e.currentTarget).hasClass('starting-path') ){
               return false;
            } else {
               href = $(e.currentTarget).attr("data-href");
               window.location.href=href;
            }
            
        })

        <?php 
        $time = 500;
        $delay = 1500;
        if(!empty($attr["time"])) $time = $attr["time"];
        if(!empty($attr["delay"])) $delay = $attr["delay"];
        ?>

        let time =  <?=$time?>;//500;
        let delay = <?=$delay?>;//1500;
        let lessons_completed = 7;
        let number = 0;

        <?php if ($coursecomplete): ?>
			let progressVal = 1;
		<?php else: ?>
			let progressVal = 0;
		<?php endif; ?>

        let progressFill = "#000";
		let progressSize = 44;

		if (progressVal = 1) {
			progressFill = "#FFFFFF";
		}


        jQuery('.path-svg').each(function(e) {
                            
            setTimeout(() => {
                if(jQuery(this).attr("data-iscomplete")==1){
                    progressVal = $(this).attr("data-progress");
                    console.log("progressVal",progressVal)
                    $('.courseprogress', e).circleProgress({
                        value: progressVal,
                        startAngle: -1.55,
                        size: 44,
                        emptyFill: '#E7EDF4',
                        fill: progressFill,
                        thickness:5
                    });
                    
                    jQuery(this).removeClass('active')
                        .addClass('completed')
                        .next()
                        .addClass('active');

                    if ($(this).hasClass('path-finish') 
                        && ( 
                            $(this).attr("data-path-completed-count") >= $(this).attr('data-pathcount')
                        ) ){
                        //confetti.start();
                        setTimeout(() => {
                            //confetti.stop();
                        }, 5000);

                    }else {
                        <?php 
                        if($autoscroll){
                        ?>
                        $('html, body').animate({
                            scrollTop: $(this).next().offset().top - 160
                        }, delay);
                        <?php 
                        }
                        ?>
                    }
                    number++;
                }
            }, time);
            time+=delay;


        });
    
    });
    </script>
    <?php

    $out = ob_get_contents();
    ob_end_clean();
    return $out;
});

function my_courses( $attributes ) {
    extract(shortcode_atts(array(
      'btn_color' => 'red',
   ), $attributes));
    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);
    $in_progress= false;
    if (!empty($course_progress)){
        foreach ($course_progress as $k=>$v){
            if (learndash_course_status($k) === "In Progress"){
                $in_progress = true;
            }
        }
    }
    if ($in_progress):
        return '<div class="courses">'.
        do_shortcode('[ld_course_list mycourses="true" progress_bar="true" status="in_progress"]').
        '</div>';
    else:
        return '
        <div class="my_courses">
            <div class="no_courses">
                <h3>No Course!</h3>
                <div>Choose a course from the Course Library<br> or use the button below to start</div>
                <a class="btn" href="/courses" style=" background: #02B4B4;
                box-shadow: 0px 4px 20px rgba(84, 104, 255, 0.1);
                border-radius: 10px;">+</a>
            </div>
        </div>';
    endif;
}
add_shortcode('pbd_my_courses', 'my_courses');

function my_courses_title( $attributes ) {
    extract(shortcode_atts(array(
      'btn_color' => 'red',
   ), $attributes));
    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);
    if (!empty($course_progress)):
        return 'Continue Learning';
    else:
        return 'My Courses';
    endif;
}
add_shortcode('pbd_my_courses_title', 'my_courses_title');



function count_badges() {
    $achievements = gamipress_get_achievements(["post_type"=>"badges", 'orderby' => 'data', 'order' => 'asc']);
    $user_achievements = gamipress_get_user_earned_achievement_ids();
    $count = 0;
    foreach ($achievements as $achievement)
    {
        if ( in_array($achievement->ID, $user_achievements )) 
        {
            $count++;
        }
    }
    return $count;
}
add_shortcode('pbd_count_badges', 'count_badges');


// Add Shortcode
function pbd_restrict_content( $atts , $content = null ) {

    // Attributes
    $atts = shortcode_atts(
        array(
            'rank_id' => get_the_ID(),
        ),
        $atts
    );
    $user_id = get_current_user_id();
    //return $atts['rank_id'];
    //return $content;
    
    $achievement = gamipress_get_user_achievements(array('user_id'=>$user_id,'achievement_id'=>$atts['rank_id']));
    if (!empty($achievement)){
        return $content;
    }
    

}


add_shortcode( 'pbd_restrict_content', 'pbd_restrict_content' );
function pbd_courses_completed() {
    $courses = 0;
    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);
    $defaults = array(
                'post_type' => 'sfwd-courses',
                'fields'    => 'ids',
                'nopaging'  => true,
            );

    $course_query_args = wp_parse_args( $course_query_args, $defaults );
    $course_query      = new WP_Query( $course_query_args );
    if ( ( isset( $course_query->posts ) ) && ( ! empty( $course_query->posts ) ) ) {
        $course_ids = $course_query->posts;
    }
    
    if (!empty ($course_ids)){
        foreach ($course_ids as $course_id){
            $courses = $courses + learndash_course_completed( $user_id ,$course_id );
        }
    }
    return ($courses);

} 
add_shortcode( 'pbd_courses_completed', 'pbd_courses_completed' );


?>