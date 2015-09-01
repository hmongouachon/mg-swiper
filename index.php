<?php
/*
Plugin Name: mg Swiper
Plugin URI: http://8mgdesign.com
Description: A responsive html content swiper / slider with fullscreen background images based on idangerous.swiper. 
Author: Hadrien Mongouachon
Author URI: http://8mgdesign.com
Version: 1.0.1
*/

define('MGS_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('MGS', "mg Swiper");
define ("MGS_VERSION", "1.0");
define('CPT_NAME', "mg Swiper");
define('CPT_SINGLE', "mg Swiper");
define('CPT_TYPE', "mg-swiper");
 
add_theme_support('post-thumbnails', array('mg-swiper'));

if( is_admin() ) {
    add_action( 'admin_init', 'mgs_reg_function' );
    add_action('admin_menu', 'mgs_custom_menu');
}

function mgs_register() { 
    $args = array( 
        'label' => __(CPT_NAME), 
        'singular_label' => __(CPT_SINGLE), 
        'public' => true, 
        'show_ui' => true, 
        'capability_type' => 'post', 
        'hierarchical' => false, 
        'rewrite' => true, 
        'supports' => array('title', 'editor', 'thumbnail') 
       ); 
    register_post_type(CPT_TYPE , $args ); 
} 
add_action('init', 'mgs_register');


function mgs_custom_menu(){
    add_submenu_page( 'edit.php?post_type='.CPT_TYPE, 'Options', 'Options', 'manage_options', 'mgs_options', 'mgs_custom_menu_page' );
    add_action( 'admin_init', 'mgs_reg_function' );
}

//create group of variables
function mgs_reg_function() {
    register_setting( 'mgs-settings-group', 'mgs_loop' );
    register_setting( 'mgs-settings-group', 'mgs_speed' );
    register_setting( 'mgs-settings-group', 'mgs_autoplay' );
    register_setting( 'mgs-settings-group', 'mgs_keyboardControl' );
    register_setting( 'mgs-settings-group', 'mgs_mousewheelControl' );
    register_setting( 'mgs-settings-group', 'mgs_simulateTouch' );
}

//add default value to variables
 function mgs_activate() {
    add_option('mgs_loop');
    add_option('mgs_speed');
    add_option('mgs_autoplay');
    add_option('mgs_keyboardControl');
    add_option('mgs_mousewheelControl');
    add_option('mgs_simulateTouch');
 }

wp_enqueue_script('swiper_js', MGS_PATH.'js/idangerous.swiper.js', array('jquery'));
wp_enqueue_style('swiper_css', MGS_PATH.'css/idangerous.swiper.css');
	

function mgs_script(){ 
	# slider parameters 
 $loop               = get_option('mgs_loop', 'false');
 $speed              = get_option('mgs_speed', '300');
 $autoplay  		 = get_option('mgs_autoplay', '5000');
 $keyboardControl	 = get_option('mgs_keyboardControl', 'false');
 $mousewheelControl	 = get_option('mgs_mousewheelControl', 'false');
 $simulateTouch		 = get_option('mgs_simulateTouch', 'true');



print '<script type="text/javascript" charset="utf-8">
    jQuery(function($){
        var mySwiper = new Swiper(".swiper-container",{
            pagination: ".swiper-pager",
            paginationClickable: true,
            loop : '.$loop.',
            speed : '.$speed.',
            autoplay : '.$autoplay.',
            keyboardControl : '.$keyboardControl.',
            mousewheelControl : '.$mousewheelControl.',
            simulateTouch : '.$simulateTouch.',
            preventLinks : true,
            onSlideChangeEnd : function(swiper){ 
                captionShow()
            },
            onSlideChangeStart : function(swiper){
                captionHide()
            }
        });
        $(".swiper-slide").first().find("figcaption, .title, .content").addClass("active");
        function captionShow() {
            $(".swiper-slide-active .title, .swiper-slide-active .content, .swiper-slide-active figcaption").addClass("active");
        };
        function captionHide() {
            $(".title, .content, figcaption").removeClass("active");
        };
        
        // resize event
        // $(window).resize(function() {
        //    mySwiper.reInit();
        // }); 
    });
</script>';
}
add_action('wp_footer', 'mgs_script');

function mgs_get_slider(){
 
$slider= '<div class="swiper-container">
      <div class="swiper-wrapper">';
    $mgs_query= "post_type=mg-swiper";
    query_posts($mgs_query);
    if (have_posts()) : while (have_posts()) : the_post();
        //$img= get_the_post_thumbnail( $post->ID, 'large' );
        $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        $title = apply_filters( 'the_title', get_the_title() );
        $content = apply_filters( 'the_content', get_the_content() );
       $slider.='<figure class="swiper-slide" style="background-image : url('.$url.')">
       <figcaption>
       <span class="title">'.$title.'</span>
       <span class="content">'.$content.'</span></figure>
       </figcaption>
       ';         
    endwhile; endif; wp_reset_query();
 
    $slider.= '</div>
    <div class="swiper-pager"></div>
    </div>';
     
    return $slider;
}
 
 
/**add the shortcode for the slider- for use in editor**/
function mgs_insert_slider($atts, $content=null){
    $slider= mgs_get_slider();
    return $slider; 

}
 
add_shortcode('mg_swiper', 'mgs_insert_slider');
 
 
/**add template tag- for use in themes**/
 
function mgs_slider(){
    print mgs_get_slider();
}

function mgs_custom_menu_page() {

?>

<div class="wrap">
<h2>mgSwiper</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'mgs-settings-group' ); ?>
    <table class="form-table">

         <tr valign="top">
        <th scope="row">Loop</th>
        <td>
        <label>
        <input type="text" name="mgs_loop" id="mgs_loop" size="10" value="<?php echo get_option ('mgs_loop'); ?>" />
        </label>
        </tr>

        
        <tr valign="top">
        <th scope="row">Speed</th>
        <td>
        <label>
        <input type="text" name="mgs_speed" id="mgs_speed" size="10" value="<?php echo get_option('mgs_speed'); ?>" />
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Autoplay</th>
        <td>
        <label>
        <input type="text" name="mgs_autoplay" id="mgs_autoplay" size="10" value="<?php echo get_option('mgs_autoplay'); ?>" />
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Keyboard Control</th>
        <td>
        <label>
        <input type="text" name="mgs_keyboardControl" id="mgs_keyboardControl" size="10" value="<?php echo get_option('mgs_keyboardControl'); ?>" />
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Mousewheel Control</th>
        <td>
        <label>
        <input type="text" name="mgs_mousewheelControl" id="mgs_mousewheelControl" size="10" value="<?php echo get_option('mgs_mousewheelControl'); ?>" />
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Simulate Touch</th>
        <td>
        <label>
        <input type="text" name="mgs_simulateTouch" id="mgs_simulateTouch" size="10" value="<?php echo get_option('mgs_simulateTouch'); ?>" />
        </label>
        </tr> 
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>

<?php }