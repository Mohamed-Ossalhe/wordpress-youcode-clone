<?php
/*
Plugin Name: YellowPencil
Plugin URI: https://yellowpencil.waspthemes.com
Description: The most powerful visual CSS editor for WordPress. Customize any theme and page visually without coding.
Version: 7.5.8
Author: WaspThemes
Author URI: https://yellowpencil.waspthemes.com
*/


/* ---------------------------------------------------- */
/* Basic
/* ---------------------------------------------------- */
if (!defined('ABSPATH')) {
    die('-1');
}


/* ---------------------------------------------------- */
/* Check if lite version or not.
/* ---------------------------------------------------- */
if (!strstr(__FILE__, "yellow-pencil-visual-theme-customizer")) {
    $pro_dir = __FILE__;
    $lite_dir = str_replace("waspthemes-yellow-pencil", "yellow-pencil-visual-theme-customizer", __FILE__);
    define("YP_PRO_DIRECTORY", TRUE);
}else{
    $lite_dir = __FILE__;
    $pro_dir = str_replace("yellow-pencil-visual-theme-customizer", "waspthemes-yellow-pencil", __FILE__);
}

if (file_exists($pro_dir) && file_exists($lite_dir)) {

    // Be sure deactivate_plugins function is exists
    if (!function_exists("deactivate_plugins")) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    // deactivate lite version.
    deactivate_plugins(plugin_basename($lite_dir));

}

// Generate Base Editor URL.
function wyp_get_uri() {

    if (current_user_can("edit_theme_options") == true) {

        return admin_url('admin.php?page=yellow-pencil-editor');

    } elseif (defined('YP_DEMO_MODE')) {

        return add_query_arg(array(
            'yellow_pencil' => 'true'
        ), get_home_url() . '/');

    }

}


/* ---------------------------------------------------- */
/* Define
/* ---------------------------------------------------- */
define('YP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('YP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Define
define('YP_VERSION', "7.5.8");

// Admin Settings Page
include(YP_PLUGIN_DIR . 'admin/settings.php');
include(YP_PLUGIN_DIR . 'admin/lib/subscription-cancellation.php');



/* ---------------------------------------------------- */
/* Register YellowPencil Panel
/* ---------------------------------------------------- */
function wyp_yellow_penci_bar() {

    $yellow_pencil_uri = wyp_get_uri();

    // Get protocol
    $protocol = is_ssl() ? 'https' : 'http';

    // Href
    $hrefA = $_GET['href'];

    // Update protocol.
    if(strstr($hrefA,'://') == true){
        $hrefNew = explode("://",$hrefA);
        $hrefNew = $protocol.'://'.$hrefNew[1];
    }elseif(strstr($hrefA,'://') == false){
        $hrefNew = $protocol.'://'.$hrefA;
    }

    // Page Link
    $hrefNew = esc_url($hrefNew);

    // YP Rand, Not must
    $hrefNew = add_query_arg(array('wyp_rand' => rand(136900, 963100)), $hrefNew);

    // Creating LiveLink. Intval filter disabled on wyp_page_id because yp using alpa for some page types.
    $liveLink = add_query_arg(array('wyp_live_preview' => 'true', 'wyp_page_id' => $_GET['wyp_page_id'], 'wyp_page_type' => trim(wp_strip_all_tags($_GET['wyp_page_type'])), 'wyp_rand' => rand(136900, 963100)), $hrefNew);

    // if isset out, set wyp_out to live preview
    if(isset($_GET['wyp_out'])){
        $liveLink = add_query_arg(array('wyp_out' => 'true'), $liveLink);
    }

    $liveLink = str_replace("&#038;", "&amp;", $liveLink);
    $liveLink = str_replace("&#38;", "&amp;", $liveLink);

    echo "<div class='ed-pnl wyp-disable-cancel'>
        <div class='ed-pnl-top'>

            <a href='".$hrefNew."' class='wf-close-btn-link' tabindex='-1'><span data-toggle='tooltip' data-placement='left' title='Close Editor' class='wyp-close-btn'></span></a>

            <a class='wyp-button wyp-save-btn wyp-disabled'>Saved</a>

            <a data-toggle='tooltipTopBottom' data-placement='bottom' title='Review Changes <span class=\"wyp-s-shortcut\">(C)</span>' class='wyp-button-manage'></a>

            <a target='_blank' data-href='".$liveLink."' data-toggle='tooltipTopBottom' data-placement='bottom' title='Live Preview' class='wyp-button-live'></a>

            <div class='wyp-clear'></div>

        </div>";

        echo "<div id='property-responsive-menu'></div>";

        echo "<div class='ed-pnl-inner'>";

        echo "<div class='wyp-csng-sec'>
                <div class='wyp-customizing-inner'>
                ".wyp_customizing_options()."
                <div class='wyp-clear'></div>
                <div id='wyp-crnt-el'>No element selected</div>
                </div>
        </div>";

        // Options
        include( YP_PLUGIN_DIR . 'editor/lib/properties.php' );

        echo "<div class='wyp-panel-no-selection'><div class='starter-notice'><div class='wyp-hand'></div><div class='wyp-hand-after'></div>Select any element on the page to start making changes.</div></div>";

        echo "<div class='ed-pnl-footer'>
            <h3><a tabindex='-1' target='_blank' href='https://yellowpencil.waspthemes.com/documentation/'>Docs</a> / <a tabindex='-1' target='_blank' href='https://yellowpencil.waspthemes.com/changelog/'>V ".YP_VERSION."</a></h3>
            <span class='yicon icon-admin-collapse wyp-panel-hide' data-toggle='tooltip' data-placement='left' title='Hide Panels <span class=\"wyp-shortcut-char\">(H)</span>'></span>
        </div>";

        echo "</div>"; // Editor panel

    echo "</div>";

}


// Check if it is demo mode
function wyp_check_demo_mode() {

    // Demo mode avaiable for just non-logout users.
    if (defined('WT_DEMO_MODE') && is_user_logged_in() == false) {
        define('YP_DEMO_MODE', TRUE);
    }

}
add_action("init", "wyp_check_demo_mode");



/* ---------------------------------------------------- */
/* Add animation ajax callback
/* ---------------------------------------------------- */
function wyp_add_animation() {

    if (current_user_can("edit_theme_options") == true && check_admin_referer("wyp_editor_nonce")) {

        $css  = wp_strip_all_tags($_POST['wyp_anim_data']);
        $name = wp_strip_all_tags($_POST['wyp_anim_name']);

        if (!update_option("yp_anim_" . $name, $css)) {
            add_option("yp_anim_" . $name, $css);
        }

    }

    wp_die();

}

add_action('wp_ajax_wyp_add_animation', 'wyp_add_animation');



/* ---------------------------------------------------- */
/* Download from unsplash and upload to wp
/* ---------------------------------------------------- */
function wyp_unsplash_api(){

    // Let
    if(current_user_can("upload_files") && check_admin_referer("wyp_editor_nonce")){

        global $wpdb;
        $attachments = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_title = '".esc_sql($_POST["wyp_id"])."' AND post_type = 'attachment' ", OBJECT);

        if($attachments){
            $attachment_url = $attachments[0]->guid;
            die($attachment_url);
        }

        // The URL
        $url = $_POST["wyp_link"]."&.jpg";

        // TMP
        $tmp = download_url($url, 60);

        // Error Check
        if(is_wp_error($tmp)){
            die("There is a problem with downloading the image from the remote server. please increase the file upload size limit and try again.");
        }

        // Array
        $file_array = array();

        // Name
        $file_array['name'] = $_POST["wyp_id"].".jpg";
        $file_array['tmp_name'] = $tmp;

        // Error Check
        if ( is_wp_error( $tmp ) ) {
            wp_delete_file($file_array['tmp_name']);
            $file_array['tmp_name'] = '';
            die("This image file seems to be invalid.");
        }

        // do the validation and storage stuff
        $id = media_handle_sideload($file_array, 0, $_POST["wyp_id"]);

        // If error storing permanently, unlink
        if (is_wp_error($id)){
            wp_delete_file($file_array['tmp_name']);
            die("There is a problem with uploading the image to WordPress. please increase the file upload size limit and try again.");
        }

        // Print Result
        die(wp_get_attachment_url($id));

    }

}

add_action('wp_ajax_wyp_unsplash_api', 'wyp_unsplash_api');


/* ---------------------------------------------------- */
/* Saving live option with ajax
/* ---------------------------------------------------- */
function wyp_save_live_option() {

    // If User can edit theme options
    if (current_user_can("edit_theme_options") == true && check_admin_referer("wyp_editor_nonce")) {

        // Get Value
        $name = wp_strip_all_tags($_POST['wyp_option_name']);
        $value = wp_strip_all_tags($_POST['wyp_option_value']);

        // Update Option with yp_op_ prefix
        if (!update_option("yp_op_" . $name, $value)) {
            add_option("yp_op_" . $name, $value);
        }

    }

    // Die
    wp_die();

}

add_action('wp_ajax_wyp_live_save_option', 'wyp_save_live_option');


/* ---------------------------------------------------- */
/* Reading live options
/* ---------------------------------------------------- */
function wyp_get_live_option($name){

    // IF Pro and hides preimum options, show all
    if($name == "hide_premium_options" && defined("WTFV") == true){
        return "false";
    }

    // Database Option
    $option = get_option("yp_op_".$name);

    // If no option on database, read defaults
    if($option === null || $option === false){

        // YP Defaults
        $defaultOption = array(
            'fixed_right_panel' => false,
            'fixed_left_bar' => true,
            'hide_premium_options' => false,
            'show_css_selector' => false,
            'smart_responsive_technology' => true,
            'smart_important_tag' => true,
            'append_auto_comments' => true
        );

        // Get after filters
        $data = apply_filters( 'yp_'.$name, $defaultOption[$name]);

        if($data){
            return "true";
        }else{
            return "false";
        }

    }else{
        return $option;
    }

}


/* ---------------------------------------------------- */
/* Saving selector comments
/* ---------------------------------------------------- */
function wyp_save_comments_option() {

    // Get nonce
    $nonce = $_REQUEST["_wpnonce"];

    // If User can edit theme options
    if (current_user_can("edit_theme_options") == true && wp_verify_nonce($nonce, "wyp_editor_nonce")) {

        // Get Value
        $value = wp_strip_all_tags($_POST['wyp_selector_comments']);

        // IS Valid
        json_decode(wp_unslash($value));

        if(json_last_error() === JSON_ERROR_NONE || empty($value)){

            // Update Option with yp_op_ prefix
            if (!update_option("yp_selector_comments", $value)) {
                add_option("yp_selector_comments", $value);
            }

        }else{

            wp_die("json_error");

        }

    }else{

        if(current_user_can("edit_theme_options") == true){
            wp_die("nonce_error");
        }else{
            wp_die("authorized_error");
        }

    }

    // Die
    wp_die();

}

add_action('wp_ajax_wyp_save_comments_option', 'wyp_save_comments_option');


/* ---------------------------------------------------- */
/* Ajax check plugin license
/* ---------------------------------------------------- */
function wyp_check_license() {

    if (current_user_can("edit_theme_options") == true && check_admin_referer("wyp_editor_nonce")) {

        $key = get_option("yp_purchase_code");
        if($key === null || $key === false){
            die("0");
        }else{
            die("1");
        }

    }

}
add_action('wp_ajax_wyp_check_license', 'wyp_check_license');



/* ---------------------------------------------------- */
/* Delete Stylesheets
/* ---------------------------------------------------- */
function wyp_delete_customization($type, $value) {

    if(current_user_can("edit_theme_options")){

        // Specific single types
        if($value == "lostpassword" || $value == "register" || $value == "login" || $value == "home"){
            $type = "TYPE";
        }

        // delete global data.
        if($type == "GLOBAL"){
            delete_option('wt_css');
            delete_option('wt_styles');
        }

        // delete Post type.
        if($type == "TYPE"){
            $reset_type = trim(wp_strip_all_tags($value));

            delete_option('wt_'.$value.'_css');
            delete_option('wt_'.$value.'_styles');
        }

        // delete by id.
        if($type == "ID"){
            delete_post_meta(intval($value),'_wt_css');
            delete_post_meta(intval($value),'_wt_styles');
        }

    }

}


/* ---------------------------------------------------- */
/* Delete Stylesheets, animations with ajax
/* ---------------------------------------------------- */
function wyp_delete_stylesheet_live() {

    if(current_user_can("edit_theme_options") == true && check_admin_referer('wyp_live_styles_delete_nonce')){

        // delete global data.
        if(isset($_POST['wyp_reset_global'])){
            delete_option('wt_css');
            delete_option('wt_styles');
        }

        // delete anim
        if(isset($_POST['wyp_delete_animate']) && empty($_POST['wyp_delete_animate']) == false){
            delete_option(trim(wp_strip_all_tags(($_POST['wyp_delete_animate']))));
        }

        // delete Post type.
        if(isset($_POST['wyp_reset_type']) && empty($_POST['wyp_reset_type']) == false){
            $reset_type = trim( wp_strip_all_tags( $_POST['wyp_reset_type'] ) );
            delete_option('wt_'.$reset_type.'_css');
            delete_option('wt_'.$reset_type.'_styles');
        }

        // delete by id.
        if(isset($_POST['wyp_reset_id']) && empty($_POST['wyp_reset_id']) == false){
            delete_post_meta(intval($_POST['wyp_reset_id']),'_wt_css');
            delete_post_meta(intval($_POST['wyp_reset_id']),'_wt_styles');
        }

        // delete options
        if(isset($_POST['wyp_reset_options'])){

            // Delete selector comment
            delete_option('yp_selector_comments');

            // Settings page
            delete_option('yp-output-option');
            delete_option('yp-draft-mode');
            delete_option('yp-default-global');

            // Delete all other options starts with yp_op_
            global $wpdb;
            $prefix = "yp_op_";
            $options = $wpdb->get_results($wpdb->prepare("SELECT option_name,option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $prefix . '%'), ARRAY_A);

            if (!empty($options)) {
                foreach ($options as $v) {
                    delete_option($v['option_name']);
                }
            }

        }

        // Get All CSS data as ready-to-use
        $output = wyp_get_export_css("create");

        // Update custom.css file
        wyp_create_custom_css($output);

    }

    wp_die();

}

add_action('wp_ajax_wyp_delete_stylesheet_live', 'wyp_delete_stylesheet_live');


/* ---------------------------------------------------- */
/* GET UPDATE API
/* ---------------------------------------------------- */
$update_dir = YP_PLUGIN_DIR.'/admin/lib/update-support.php';
if(defined('YP_PRO_DIRECTORY') && file_exists($update_dir)){
    require_once(YP_PLUGIN_DIR.'/admin/lib/update-support.php');
}


/* ---------------------------------------------------- */
/* Delete some options when uninstall
/* ---------------------------------------------------- */
if (function_exists('register_uninstall_hook')){
    register_uninstall_hook(__FILE__, 'uninstall_yellow_pencil');
}


/* ---------------------------------------------------- */
/* delete some options on uninstall
/* ---------------------------------------------------- */
function uninstall_yellow_pencil() {
    delete_option('yp_purchase_code');
}


/* ---------------------------------------------------- */
/* Add a customize link in wp plugins page
/* ---------------------------------------------------- */
function wyp_plugin_action_links($links) {
    $links[] = '<a href="' . admin_url('themes.php?page=yellow-pencil') . '">Customize</a>';
    $links[] = '<a href="' . admin_url('themes.php?page=yellow-pencil-changes') . '">Manage Customizations</a>';
    if(!defined("WTFV")){
        $links[] = '<a href="' . admin_url('admin.php?page=yellow-pencil-license') . '">Go Pro</a>';
    }
    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wyp_plugin_action_links' );







/* ---------------------------------------------------- */
/* Update default custom css directory for 7.2.2
/* ---------------------------------------------------- */
function wyp_new_custom_css_directory(){

    $newCustom = get_option('yp_new_custom_CSS_dir');

    // one time only.
    if($newCustom === false){

        // by this point, the $wp_filesystem global should be working, so let's use it to create a file
        global $wp_filesystem;

        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        // Delete all old custom-x.css files in plugin directory because anymore creating in uploads folder.
        $files = glob(YP_PLUGIN_DIR . 'custom-*.css');

        // then delete old revisions before create new.
        foreach($files as $file){
            $wp_filesystem->delete($file);
        }

        // Update custom css
        wyp_update_custom_css_callback();

        // Add a option to define the changed.
        add_option("yp_new_custom_CSS_dir", "1");

    }

}

add_action("init", "wyp_new_custom_css_directory");



/* ---------------------------------------------------- */
/* Get ID of rule
/* ---------------------------------------------------- */
function wyp_css_id($css){

    // No webkit
    $css = str_replace("-webkit-", "", $css);

    // Update transfrom parts
    if(strrpos($css, "-transform") !== false && $css != 'text-transform'){
        $css = 'transform';
    }

    // Update filter parts
    if(strrpos($css, "-filter") !== false){
        $css = 'filter';
    }

    // Update filter parts
    if(strrpos($css, "box-shadow-") !== false){
        $css = 'box-shadow';
    }

    // Return
    return $css;

}


/* ---------------------------------------------------- */
/* Get Font Families
/* ---------------------------------------------------- */
function wyp_load_fonts() {
    $css = wyp_get_css(true);
    wyp_get_font_families($css, null);
}


/* ---------------------------------------------------- */
/* Update database for 7.0.0                            */
/* ---------------------------------------------------- */
function wyp_database_update(){

    $databaseUpdated = get_option('yp_700_db_updateX');

    // one time only.
    if($databaseUpdated === false){

        global $wpdb;

        // Find data in options
        $options = $wpdb->get_results("SELECT option_name,option_value FROM {$wpdb->options} WHERE option_name REGEXP '^wt_.*_?styles$'", ARRAY_A);

        // if array not empty
        if (!empty($options)) {

            // loop
            foreach ($options as $option) {

                // convert old data to new data
                $newData = wyp_convert_new_data($option['option_value']);

                // update
                update_option($option['option_name'], $newData);

            }

        }

        // Find data in metaOptions
        $metaOptions = $wpdb->get_results("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wt_styles'", ARRAY_A);

        // if array not empty
        if (!empty($metaOptions)) {

            // loop
            foreach ($metaOptions as $option) {

                // convert old data to new data
                $newData = wyp_convert_new_data($option['meta_value']);

                // update
                update_post_meta($option['post_id'], $option['meta_key'], $newData);

            }

        }

        // add option for not convert again.
        add_option("yp_700_db_updateX", "1");

    }

}

add_action("init", "wyp_database_update");


/* ---------------------------------------------------- */
/* Convert old data to new data 7.0.0                   */
/* ---------------------------------------------------- */
function wyp_convert_new_data($data){

    // empty
    if(empty($data)){
        return $data;
    }

    // if not have a style tag
    if(strrpos($data, "<style ") === false){
        return $data;
    }

    // converting with preg_match_all regex
    $re = '/<style(.*?)<\/style>/';
    preg_match_all($re, $data, $matches, PREG_SET_ORDER, 0);

    // no matches, then don't make anything.
    if(empty($matches)){
        return $data;
    }

    // keep all as array
    $result = array();

    // loop matches
    foreach ($matches as $value) {

        $style = $value[0];

        preg_match('/data\-rule\=\"(.*?)\"/', $style, $rule);
        preg_match('/data\-style\=\"(.*?)\"/', $style, $selector);
        preg_match('/data\-size\-mode\=\"(.*?)\"/', $style, $msize);
        preg_match('/>(.*?)<\/style>/', $style, $content);

        // push
        array_push($result, "/* [rule=".$rule[1]."] [selector=".$selector[1]."] [msize=".$msize[1]."] */\n ".$content[1]);

    }

    // join array and return;
    return join("", $result);

}


/* ---------------------------------------------------- */
/* Getting font Families By CSS OUTPUT
/* ---------------------------------------------------- */
// Type null = 'wp_enqueue_style'
// Type import = 'import'
// Type = wp_enqueue_style OR return @import CSS
function wyp_get_font_families($css, $type) {

    $protocol = is_ssl() ? 'https' : 'http';

    preg_match_all('/\{(.*?)font-family:(.*?);(.*?)\}/', $css, $r);

    foreach ($r['2'] as &$k) {
        $k = wyp_font_name($k);
    }

    $importArray = array();

    $in = -1;
    foreach (array_unique($r['2']) as $family) {

        $in++;

        // Skip if not include
        if(!isset($_GET["yellow_pencil_frame"])){
            if (strstr($r["0"][$in], "--google-webfont")){
                continue;
            }
        }

        $id = str_replace("+", "-", strtolower($family));

        $id = str_replace("\\", "", $id);

        if ($id == 'arial' || $id == 'helvetica' || $id == 'georgia' || $id == 'serif' || $id == 'helvetica-neue' || $id == 'times-new-roman' || $id == 'times' || $id == 'sans-serif' || $id == 'arial-black' || $id == 'gadget' || $id == 'impact' || $id == 'charcoal' || $id == 'tahoma' || $id == 'geneva' || $id == 'verdana' || $id == 'inherit') {
            return false;
        }

        if ($id == '' || $id == ' ') {
            return false;
        }

        // Getting fonts from google api.
        if ($type == null) {
            wp_enqueue_style($id, $protocol . '://fonts.googleapis.com/css2?family=' . $family . ':ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap');
        } else {
            array_push($importArray, $protocol . '://fonts.googleapis.com/css2?family=' . $family . ':ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap');
        }

    }

    if ($type != null) {
        return $importArray;
    }

}



/* ---------------------------------------------------- */
/* Finding Font Names From CSS data
/* ---------------------------------------------------- */
function wyp_font_name($a) {

    $a = str_replace(array(

        "font-family:",
        '"',
        "'",
        " ",
        "+!important",
        "!important"

    ), array(

        "",
        "",
        "",
        "+",
        "",
        ""

    ), $a);

    if (strstr($a, ",")) {
        $array = explode(",", $a);
        return wp_unslash(trim($array[0], "+")); // /roboto/ problem
    } else {
        return wp_unslash(trim($a, "+"));
    }

}



/* ---------------------------------------------------- */
/* Admin Javascript
/* ---------------------------------------------------- */
function wyp_adminj_javascript(){ ?>
    <script type="text/javascript" >
    jQuery(document).ready(function($){

        <?php

            // Default customization type
            $wyp_default_global = get_option("yp-default-global");

            // Open in global customization
            if($wyp_default_global == "1"){
                echo "window.default_global_customization_type = true;";
            }else{
                echo "default_global_customization_type = false;";
            }

        ?>


    });
    </script><?php
}

// Admin script
add_action( 'admin_footer', 'wyp_adminj_javascript' );



/* ---------------------------------------------------- */
/* Checking current user can or not
/* ---------------------------------------------------- */
function wyp_check_let() {

    // If Demo Mode
    if (defined('YP_DEMO_MODE') == true && isset($_GET['yellow_pencil_frame']) == true) {
        return true;
    }

    // If user can.
    if (current_user_can("edit_theme_options") == true) {
        return true;
    } else {
        return false;
    }

}



/* ---------------------------------------------------- */
/* Checking current user can or not (FOR FRAME)
/* ---------------------------------------------------- */
function wyp_check_let_frame() {

    // If Demo Mode
    if (defined('YP_DEMO_MODE') == true && isset($_GET['yellow_pencil_frame']) == true) {
        return true;
    }

    // Be sure, user can.
    if (current_user_can("edit_theme_options") == true && isset($_GET['yellow_pencil_frame']) == true) {
        return true;
    } else {
        return false;
    }

}



/* ---------------------------------------------------- */
/* Getting Last Post Title
/* ---------------------------------------------------- */
function wyp_getting_last_post_title() {
    $last = wp_get_recent_posts(array(
        "numberposts" => 1,
        "post_status" => "publish"
    ));

    if (isset($last['0']['ID'])) {
        $last_id = $last['0']['ID'];
    } else {
        return false;
    }

    $title = get_the_title($last_id);

    if (strstr($title, " ")) {
        $words = explode(" ", $title);
        return $words[0];
    } else {
        return $title;
    }

}



/* ---------------------------------------------------- */
/* Clean protocol from URL
/* ---------------------------------------------------- */
function wyp_urlencode($v) {
    $u = urldecode($v);
    $y = explode("://", $u);

    if(isset($y[1])){
        return urlencode($y[1]);
    }
    
    return urlencode($u);
}


/* ---------------------------------------------------- */
/* Register Admin Script
/* ---------------------------------------------------- */
function wyp_deactivation_function($hook) {

    // clean options
    delete_option("yp_700_db_updateX");
    delete_option("yp_purchase_code");

}

register_deactivation_hook(__FILE__, 'wyp_deactivation_function');


/* ---------------------------------------------------- */
/* Register Admin Script
/* ---------------------------------------------------- */
function wyp_enqueue_admin_pages($hook) {

  if(current_user_can("edit_theme_options")){

    // Post pages.
    if ('post.php' == $hook || 'post-new.php' == $hook) {
        wp_enqueue_script('yellow-pencil-admin', plugins_url('admin/js/admin-general.js?wypver='.YP_VERSION.'', __FILE__), 'jquery', '1.0', TRUE);
    }

    // Yellow Pencil WordPress Admin Page. // loading ace editor
    if ('toplevel_page_yellow-pencil-changes' == $hook || "yellowpencil_page_yellow-pencil-animations" == $hook || "yellowpencil_page_yellow-pencil-settings" == $hook || "yellowpencil_page_yellow-pencil-license" == $hook) {

        // Ace Editor
        wp_enqueue_script('wyp-admin-page-ace', plugins_url('editor/js/ace/ace.js?wypver='.YP_VERSION.'', __FILE__), 'jquery', '1.0', TRUE);

        // Ace Editor
        wp_enqueue_script('wyp-admin-page-ace2', plugins_url('editor/js/ace/ext-language_tools.js?wypver='.YP_VERSION.'', __FILE__), 'jquery', '1.0', TRUE);

        // General Scripts
        wp_enqueue_script('wyp-admin-page', plugins_url('admin/js/admin-page.js?wypver='.YP_VERSION.'', __FILE__), 'jquery', '1.0', TRUE);

    }

    // Admin CSS
    wp_enqueue_style('yellow-pencil-admin', plugins_url('admin/css/admin.css?wypver='.YP_VERSION.'', __FILE__));

  }

}

add_action('admin_enqueue_scripts', 'wyp_enqueue_admin_pages');



/* ---------------------------------------------------- */
/* Adding Link To Admin Appearance Menu
/* ---------------------------------------------------- */
function wyp_menu() {
    add_theme_page('YellowPencil Editor', 'YellowPencil Editor', 'edit_theme_options', 'yellow-pencil', 'wyp_menu_function', 999);
}


/*
/* ---------------------------------------------------- */
/* Appearance page Loading And Location
/* ---------------------------------------------------- */
function wyp_menu_function() {

}

add_action('admin_menu', 'wyp_menu');


/* ---------------------------------------------------- */
/* Appearance page Loading And Location
/* ---------------------------------------------------- */
function wyp_admin_headr() {

    if(!isset($_GET['page'])){
        return false;
    }

    if($_GET['page'] != 'yellow-pencil'){
        return false;
    }

    // Home URL
    $yellow_pencil_uri = wyp_get_uri();

    // Basic
    if(get_option("show_on_front") == "page"){
        $frontpage_id = get_option('page_on_front');
    }else{
        $frontpage_id = 0;
    }

    if($frontpage_id == 0 || $frontpage_id == null){
        $page_id = "home";
        $wyp_page_type = "home";
        $wyp_mode = "single";
    }else{
        $page_id = $frontpage_id;
        $wyp_page_type = get_post_type($frontpage_id);
        $wyp_mode = "single";
    }

    // Dev filter for auto popup.
    $auto_popup = apply_filters( 'yp_auto_load_popup', TRUE);

    // get option value
    $global_option = get_option("yp-default-global");

    // use global as default
    if($wyp_mode == "single" && $global_option == "1"){
        $wyp_mode = "global";
    }

    // Redirect Link
    if($auto_popup){
        $href = add_query_arg(array('href' => wyp_urlencode(esc_url(get_home_url().'/')), 'wyp_page_id' => $page_id, 'wyp_page_type' => $wyp_page_type, 'wyp_mode' => $wyp_mode, 'wyp_load_popup' => "1"), $yellow_pencil_uri);
    }else{
        $href = add_query_arg(array('href' => wyp_urlencode(esc_url(get_home_url().'/')), 'wyp_page_id' => $page_id, 'wyp_page_type' => $wyp_page_type, 'wyp_mode' => $wyp_mode), $yellow_pencil_uri);
    }

    // Redirect
    wp_safe_redirect($href);
    exit;

}

add_action('admin_init', 'wyp_admin_headr');



/* ---------------------------------------------------- */
/* Sub string after 18chars
/* ---------------------------------------------------- */
function wyp_get_short_title($title, $limit) {

    $title = wp_strip_all_tags($title);

    if ($title == '') {
        $title = 'Untitled';
    }

    if (strlen($title) > $limit) {
        return mb_substr($title, 0, $limit, 'UTF-8') . '..';
    } else {
        return $title;
    }

}



// Update custom.css when reading settings change
// Because this need to update body.blog etc prefixes after change.
add_action( 'update_option_page_on_front', 'wyp_update_custom_css_callback', 10, 2 );
add_action( 'update_option_page_for_posts', 'wyp_update_custom_css_callback', 10, 2 );
add_action( 'update_option_show_on_front', 'wyp_update_custom_css_callback', 10, 2 );

function wyp_update_custom_css_callback(){

    // Get All CSS data as ready-to-use
    $output = wyp_get_export_css("create");

    // Update custom.css file
    wyp_create_custom_css($output);

}


/* ---------------------------------------------------- */
/* Helper tool to print login styles
/* ---------------------------------------------------- */
/* CSS codes, animation-events.js, animate.css, custom-anims */
function wyp_login_styles($r){

    $onlyCSS = "";

    // Login
    if($GLOBALS['pagenow'] === 'wp-login.php' && empty($_REQUEST['action'])){
        $onlyCSS .= get_option("wt_login_css");
    }

    // Register
    if($GLOBALS['pagenow'] === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'register'){
        $onlyCSS .= get_option("wt_register_css");
    }

    // Lost Password
    if($GLOBALS['pagenow'] === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword'){
        $onlyCSS .= get_option("wt_lostpassword_css");
    }

    // Get animations
    $all_options = wp_load_alloptions();foreach ($all_options as $name => $value) {if (stristr($name, 'yp_anim')) {$onlyCSS .= $value . str_replace("keyframes", "-webkit-keyframes", $value);}}

    // No print
    if(strlen($onlyCSS) == 0){
        return false;
    }

    // Delete CSS Comments
    $onlyCSS = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!","", $onlyCSS);

    // Delete HTML tags from pure CSS
    $onlyCSS = wp_strip_all_tags($onlyCSS);

    // Return mode
    if($r){
        return $onlyCSS;
    }

    // Return
    $return = '<style id="yellow-pencil">';
    $return .= "\r\n/*\r\n\tThe following CSS codes are created by the YellowPencil plugin.\r\n\thttps://yellowpencil.waspthemes.com/\r\n*/\r\n";

    // process
    $onlyCSS = wp_unslash(wyp_auto_prefix($onlyCSS));

    // min and add
    $return .= str_replace(array(
        "\n",
        "\r",
        "\t"
    ), '', $onlyCSS);

    // Close style
    $return .= "\n" . '</style>';

    // Print
    echo $return;

    // Animate library.
    if (strstr($onlyCSS, "animation-name:")) {
        wp_enqueue_style('yellow-pencil-animate', plugins_url('public/css/animate.css?wypver='.YP_VERSION.'', __FILE__));
    }

    // Check if there any animation
    if (strstr($onlyCSS, "animation-name:") == true || strstr($onlyCSS, "animation-duration:") == true || strstr($onlyCSS, "animation-delay:") == true) {

        // Load library and jQuery
        wp_enqueue_script('yellow-pencil-library', plugins_url('public/js/animation-events.js?wypver='.YP_VERSION.'', __FILE__));

    }

}

if(isset($_GET["yellow_pencil_frame"]) == false && isset($_GET['wyp_live_preview']) == false){
    add_action('login_head', 'wyp_login_styles', 999999999);
}



/* ---------------------------------------------------- */
/* Getting CSS Codes
/* ---------------------------------------------------- */
/*
yp get css(false) : echo output CSS
yp get css(true) : return just CSS Codes.
*/
function wyp_get_css($r = false) {

    $onlyCSS         = '';
    $get_type_option = '';
    $get_post_meta   = '';

    global $wp_query;
    if (isset($wp_query->queried_object->ID)) {
        $id = @$wp_query->queried_object->ID;
    } else {
        $id = null;
    }

    if (class_exists('WooCommerce')) {
        if (is_shop()) {
            $id = wc_get_page_id('shop');
        }
    }

    $get_option = get_option("wt_css");

    // get post type
    $postType = get_post_type($id);

    // using "shop" type for shop page of woocommerce
    if (class_exists('WooCommerce')) {
        if (is_shop()) {
            $postType = "shop";
        }
    }

    if ($id != null) {
        $get_post_meta = get_post_meta($id, '_wt_css', true);
    }

    if($postType != null){
        $get_type_option = get_option("wt_" . $postType . "_css");
    }

    if ($get_option == 'false') {
        $get_option = false;
    }

    if ($get_type_option == 'false') {
        $get_type_option = false;
    }

    if ($get_post_meta == 'false') {
        $get_post_meta = false;
    }

    if (empty($get_option) == false) {
        $onlyCSS .= $get_option;
    }

    // Load type and id only on singular pages
    if(is_singular()){

        // dont load type on front and home page
        if(is_front_page() == false && is_home() == false){

            if (empty($get_type_option) == false) {
                $onlyCSS .= $get_type_option;
            }

        }

        if (empty($get_post_meta) == false) {
            $onlyCSS .= $get_post_meta;
        }

    }

    // special for shop page of woocommerce
    if (class_exists('WooCommerce')) {

        if (is_shop()) {

            if (empty($get_type_option) == false) {
                $onlyCSS .= $get_type_option;
            }

            if (empty($get_post_meta) == false) {
                $onlyCSS .= $get_post_meta;
            }

        }

    }

    if (is_author()) {
        $onlyCSS .= get_option("wt_author_css");
    } elseif (is_tag()) {
        $onlyCSS .= get_option("wt_tag_css");
    } elseif (is_category()) {
        $onlyCSS .= get_option("wt_category_css");
    } elseif (is_404()) {
        $onlyCSS .= get_option("wt_404_css");
    } elseif (is_search()) {
        $onlyCSS .= get_option("wt_search_css");
    } elseif (is_archive()) {
        $onlyCSS .= get_option("wt_archive_css");
    }

    // home.
    if (is_front_page() && is_home()) {
        $onlyCSS .= get_option("wt_home_css");
    }

    // blog
    if(get_option("show_on_front") == "page"){
        $page_for_posts = get_option('page_for_posts');
    }else{
        $page_for_posts = 0;
    }

    // Don't load type on front and posts page
    if(is_home() && $page_for_posts != 0){
        $get_post_meta   = get_post_meta($page_for_posts, '_wt_css', true);
        $onlyCSS .= $get_post_meta;
    }

    // Get animations
    $all_options = wp_load_alloptions();foreach ($all_options as $name => $value) {if (stristr($name, 'yp_anim')) {$onlyCSS .= $value . str_replace("keyframes", "-webkit-keyframes", $value);}}

    // Delete CSS Comments
    $onlyCSS = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!","", $onlyCSS);

    // Delete HTML tags from pure CSS
    $onlyCSS = wp_strip_all_tags($onlyCSS);

    if ($onlyCSS != '' && $r == false) {

        $return = '<style id="yellow-pencil">';
        $return .= "\r\n/*\r\n\tThe following CSS codes are created by the YellowPencil plugin.\r\n\thttps://yellowpencil.waspthemes.com/\r\n*/\r\n";

        // process
        $onlyCSS = wp_unslash(wyp_auto_prefix($onlyCSS));

        // min and add
        $return .= str_replace(array(
            "\n",
            "\r",
            "\t"
        ), '', $onlyCSS);

        $return .= "\n" . '</style>';

        echo $return;

    }

    if ($r == true) {
        return $onlyCSS;
    }

}


// If is dynamic inline.
if (get_option('yp-output-option') != 'external') {

    // Adding all CSS codes to Website
    if (isset($_GET['yellow_pencil_frame']) == false && isset($_GET['wyp_live_preview']) == false) {

        // Add action if not draft mode
        if(get_option('yp-draft-mode') != '1'){
            add_action('wp_head', 'wyp_get_css', 999999999);
        }

    }

}



/* ---------------------------------------------------- */
/* Getting Live Preview CSS
/* ---------------------------------------------------- */
function wyp_get_live_css() {

    // Get recent generated CSS codes.
    $css = get_option('yp_live_view_css_data');

    $all_options = wp_load_alloptions();foreach ($all_options as $name => $value) {if (stristr($name, 'yp_anim')) {$css .= $value . str_replace("keyframes", "-webkit-keyframes", $value);}}

    if (empty($css)) {
        return $css;
    }

    return wp_unslash(wyp_auto_prefix($css));

}


/* ---------------------------------------------------- */
/* Used for find page details by URL
/* ---------------------------------------------------- */
function show_page_details() {

    // only allowed users can see it
    if(current_user_can("edit_theme_options") || defined('YP_DEMO_MODE')){

        // getting informations
        $data = wyp_get_page_ids();
        $page_id = $data[0];
        $page_type = $data[1];
        $edit_mode = $data[2];

        // This adding all informations to head of the page,
        // the plugin will get these information with javascript functions
        // for open the target page in the editor
        echo "<script id='wyp_page_details'>".$page_id."|".$page_type."|".$edit_mode."</script>";

    }

}

// Hook only if get wyp_get_details
if(isset($_POST['wyp_get_details'])){
    add_action('wp_head', 'show_page_details', 999999999);
}



/* ---------------------------------------------------- */
/* Getting fonts for live preview
/* ---------------------------------------------------- */
function wyp_load_fonts_for_live() {
    $css = wyp_get_live_css();
    wyp_get_font_families($css, null);
}

/* ---------------------------------------------------- */
/* Getting fonts for admin live
/* ---------------------------------------------------- */
function wyp_load_fonts_for_admin_live() {
    $css = wyp_get_live_css();
    wyp_get_font_families($css, null);
}

/* ---------------------------------------------------- */
/* Getting fonts for admin
/* ---------------------------------------------------- */
function wyp_load_fonts_for_admin() {
    $css = wyp_login_styles(true);
    wyp_get_font_families($css, null);
}



/* ---------------------------------------------------- */
/* Generating Live Preview data
/* ---------------------------------------------------- */
function wyp_get_live_preview() {

    $css = wyp_get_live_css();

    if($GLOBALS['pagenow'] === 'wp-login.php'){

      // Animate library.
      if (strstr($css, "animation-name:")) {
          wp_enqueue_style('yellow-pencil-animate', plugins_url('public/css/animate.css?wypver='.YP_VERSION.'', __FILE__));
      }

      // Check if there any animation
      if (strstr($css, "animation-name:") == true || strstr($css, "animation-duration:") == true || strstr($css, "animation-delay:") == true) {

          // Load library and jQuery
          wp_enqueue_script('yellow-pencil-library', plugins_url('public/js/animation-events.js?wypver='.YP_VERSION.'', __FILE__));

      }

    }

    if (empty($css) == false) {

        $css = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!","", $css);

        // min
        $css = str_replace(array(
            "\n",
            "\r",
            "\t"
        ), '', $css);

        $css = '<style id="wyp-live-preview">' . $css . '</style>';

        if ($css != '<style id="wyp-live-preview"></style>') {
            echo $css;
        }

    }

}



/* ---------------------------------------------------- */
/* Adding generated live preview CSS data To WP HEAD
/* ---------------------------------------------------- */
if (isset($_GET['wyp_live_preview']) == true) {

    add_action('wp_head', 'wyp_get_live_preview', 999999999);
    add_action('login_head', 'wyp_get_live_preview', 999999999);
    add_action('init', 'wyp_out_mode', 999999999);

}


/* ---------------------------------------------------- */
/* Adding Prefix To Some CSS Rules
/* ---------------------------------------------------- */
function wyp_auto_prefix($css) {

    // last 4 version of browsers
    // 06.03.2021

    // clean ms and webkit if available
    $css = preg_replace('@\t(-webkit-|-ms-)(.*?):(.*?);@si', "", $css);

    // Webkit prefixes
    $webkit = array(
        "column-count",
        "backdrop-filter",
        "transform",
        "box-shadow",
        "filter",
        "animation-fill-mode",
        "animation-timing-function",
        "transition-property",
        "transition-duration",
        "transition-timing-function",
        "flex-direction",
        "justify-content",
        "align-items",
        "column-gap",
        "row-gap",
        "animation-duration",
        "animation-delay",
        "animation-name",
        "backface-visibility",
        "transform-origin",
        "animation-iteration-count",
        "transition-delay",
        "perspective",
        "flex",
        "box-sizing",
        "flex-grow",
        "background-clip"
    );

    // Ms prefixes
    $ms = array(
        "transform",
        "flex-direction",
        "justify-content",
        "align-items",
        "align-content",
        "flex-wrap",
        "grid-template-columns",
        "grid-template-rows",
        "transform-origin",
        "align-self",
        "flex",
        "flex-basis",
        "flex-shrink",
        "flex-grow"
    );

    // Webkit
    foreach ($webkit as $prefix) {

        if($prefix == "justify-content"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-webkit-box-pack:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "align-items"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-webkit-box-align:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex-direction"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-webkit-box-orient:$1;\r\t-webkit-box-direction:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex" || $prefix == "flex-grow"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-webkit-box-flex:$1;\r\t" . $prefix . ":$1;", $css);
        }else{
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-webkit-" . $prefix . ":$1;\r\t" . $prefix . ":$1;", $css);
        }

    }

    // MS
    foreach ($ms as $prefix) {

        if($prefix == "justify-content"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-pack:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "align-items"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-align:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "align-content"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-line-pack:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex-wrap"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-wrap:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "grid-template-columns"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-grid-columns:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "grid-template-rows"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-grid-rows:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "align-self"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-item-align:$1;\r\t-ms-grid-row-align:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex-basis"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-preferred-size:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex-shrink"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-negative:$1;\r\t" . $prefix . ":$1;", $css);
        }else if($prefix == "flex-grow"){
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-flex-positive:$1;\r\t" . $prefix . ":$1;", $css);
        }else{
            $css = preg_replace('@(?<!-)' . $prefix . ':([^\{\;]+);@i', "-ms-" . $prefix . ":$1;\r\t" . $prefix . ":$1;", $css);
        }

    }

    // Display: flex
    $css = preg_replace('@display(\s+)?:(\s+)?flex(\s+)?(\!important)?;@i', "display:-webkit-box$3$4;\r\tdisplay:-webkit-flex$3$4;\r\tdisplay:-ms-flexbox$3$4;\r\tdisplay:flex$3$4;", $css);

    // Display: grid
    $css = preg_replace('@display(\s+)?:(\s+)?grid(\s+)?(\!important)?;@i', "display:-ms-grid$3$4;\r\tdisplay:grid$3$4;", $css);

    // position: sticky
    $css = preg_replace('@position(\s+)?:(\s+)?sticky(\s+)?(\!important)?;@i', "display:-webkit-sticky$3$4;\r\tdisplay:sticky$3$4;", $css);

    // Load Gradient one time only.
    if(!function_exists("wyp_linear_gradient_support")){

        // Linear gradient prefix support
        function wyp_linear_gradient_support(array $match){

            // only gradient content
            $gradientOriginal = $match[4];
            $gradient = $gradientOriginal;

            // get first part
            preg_match('/linear-gradient\(([^,]+)/i', "linear-gradient(".$gradientOriginal, $matches);

            // direction available
            if(isset($matches[1])){

                $direction = strtolower(trim($matches[1]));

                // is valid
                if(preg_match('/(deg|top|left|right|bottom)/i', $direction)){

                    // Is deg
                    if(preg_match('/deg/i', $direction)){

                        // get deg
                        $deg = preg_replace("/[^0-9.]/", "", $direction);

                        // reverse direction for o and webkit
                        if($deg == "0"){
                            $deg = "bottom";
                        }elseif($deg == "90"){
                            $deg = "left";
                        }elseif($deg == "180"){
                            $deg = "top";
                        }elseif($deg == "270"){
                            $deg = "right";
                        }elseif($deg == "360"){
                            $deg = "bottom";
                        }else if($deg < 90){
                            $deg = 90 - $deg."deg";
                        }else if($deg > 90){
                            $deg = 360 - ($deg - 90)."deg";
                        }

                        // Update gradient
                        $gradient = preg_replace("/linear-gradient\(([^,]+)/", $deg, "linear-gradient(".$gradient);

                    // top, left etc
                    }else{

                        // to left..
                        if(preg_match('/to /i', $direction)){

                            if($direction == "to left"){
                                $direction = "right";
                            }else if($direction == "to right"){
                                $direction = "left";
                            }else if($direction == "to top"){
                                $direction = "bottom";
                            }else if($direction == "to bottom"){
                                $direction = "top";
                            }

                            // Update Gradient
                            $gradient = preg_replace("/linear-gradient\(([^,]+)/", $direction, "linear-gradient(".$gradient);

                        }

                    }

                }

            }

            // Default no important
            $important = "";

            // Checks important tag
            if(isset($match[6])){
                $important = " ".$match[6];
            }

            // Generate result gradient
            $result = $match[1].":-webkit-linear-gradient(".$gradient.")".$important.";\r\t";
            $result .= $match[1].":-o-linear-gradient(".$gradient.")".$important.";\r\t";
            $result .= $match[1].":linear-gradient(".$gradientOriginal.")".$important.";";

            // return result
            return $result;

        }

    }

    // linear gradient support (-webkit-gradient is not supported)
    $css = preg_replace_callback("@(background-image|background)(\s+)?:(\s+)?linear-gradient\((.*?)\)(\s+)?(\!important)?;@i", 'wyp_linear_gradient_support', $css);

    return $css;

}


/* ---------------------------------------------------- */
/* Prefix for Animations EXPORT
/* ---------------------------------------------------- */
function wyp_export_animation_prefix($outputCSS) {

    return str_replace(array(

        ".yp_hover",
        ".yp_focus"

    ), array(

        ":hover",
        ":focus"

    ), $outputCSS);

}



/* ---------------------------------------------------- */
/* Adding no-index meta to head for demo mode YP Links!	*/
/* ---------------------------------------------------- */
function wyp_head_meta() {
    echo '<meta name="robots" content="noindex, follow">' . "\n";
}



/* ---------------------------------------------------- */
/* Shows the frame as visitor to logged user
/* ---------------------------------------------------- */
function wyp_out_mode() {

    if (isset($_GET['wyp_out']) && current_user_can("edit_theme_options")) {
        wp_set_current_user(-1);
    }

}



/* ---------------------------------------------------- */
/* Advanced link replacer
/* ---------------------------------------------------- */
function wyp_advanced_link_replace($match){

    // be sure this is stylesheet
    if(!preg_match("/rel=(\"|\')?stylesheet(\"|\')?/", $match[0])){
        return $match[0];
    }
    
    // not available
    if(!isset($match[4])){
      return $match[0];
    }
    
    // The link href
    $linkHref = $match[4];
    
    $domain = get_site_url();
    $domainParsed = wp_parse_url($domain);
    $linkHrefParsed = wp_parse_url($linkHref);
    
    // Skip if link is relative
    if(isset($domainParsed["host"]) === false || isset($linkHrefParsed["host"]) === false){
        return $match[0];
    }
    
    $domainHasWWW = true;
    if(!preg_match('/^www\./', $domainParsed["host"])){
      $domainHasWWW = false;
    }
    
    $linkHasWWW = true;
    if(!preg_match('/^www\./', $linkHrefParsed["host"])){
      $linkHasWWW = false;
    }
    
    $noWWWDomain = preg_replace("/^www\./", "", $domainParsed["host"]);
    $noWWWHref = preg_replace("/^www\./", "", $linkHrefParsed["host"]);
    
    // is same domain
    if($noWWWDomain === $noWWWHref && isset($domainParsed["scheme"]) && isset($linkHrefParsed["scheme"])){
    
      // www is not same
      if($domainHasWWW !== $linkHasWWW){
    
        if($domainHasWWW){
          $linkHrefParsed["host"] = "www.".$linkHrefParsed["host"];
        }else{
          $linkHrefParsed["host"] = $noWWWHref;
        }
    
      }
    
      if($domainParsed["scheme"] !== $linkHrefParsed["scheme"]){
        $linkHrefParsed["scheme"] = $domainParsed["scheme"];
      }
    
      // Parse
      $linkHref = wyp_unparse_url($linkHrefParsed);
    
      // Return
      return preg_replace('@href=(\s+)?(\"|\')?(.*?)(\"|\')@', "href=$1$2".$linkHref."$4", $match[0]);
    
    }
    
    // return original value
    return $match[0];
    
}

// reverse parsed_url */
function wyp_unparse_url($parts){
  $scheme = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';
  $host = isset($parts['host']) ? $parts['host'] : '';
  $port = isset($parts['port']) ? (':' . $parts['port']) : '';
  $user = isset($parts['user']) ? $parts['user'] : '';
  $pass = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
  $pass = ($user || $pass) ? ($pass . '@') : '';
  $path = isset($parts['path']) ? $parts['path'] : '';
  $query = empty($parts['query']) ? '' : ('?' . $parts['query']);
  $fragment = empty($parts['fragment']) ? '' : ('#' . $parts['fragment']);
  return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
}


/* ---------------------------------------------------- */
/* Prepare the CSS links before load the page
/* ---------------------------------------------------- */
function wyp_link_replace($buffer){

    // Replace links
    $buffer = preg_replace_callback('@\<link([^\>]+)href=(\s+)?(\"|\')(.*?)(\s+)?(\"|\')([^>]+)?>@', "wyp_advanced_link_replace", $buffer);

    return $buffer;

}


/* ---------------------------------------------------- */
/* Adding other CSS Data to Editor frame
/* ---------------------------------------------------- */
if (isset($_GET['yellow_pencil_frame'])) {
    add_action('wp_head', 'wyp_head_meta', 9997);
    add_action('init', 'wyp_out_mode', 9996);
    ob_start("wyp_link_replace");
}


/* ------------------------------------------------------------------- */
/* Other CSS Codes (All CSS Codes excluding current editing type CSS)
/* ------------------------------------------------------------------- */
function wyp_editor_styles($id, $type, $mode) {

    $get_type_option = '';
    $get_post_meta   = '';

    $id_is = false;
    $type_is = false;

    if($mode == 'template'){
        $type_is = true;
    }else if($mode == 'single'){
        $id_is = true;
    }

    $global = '';
    $template = '';
    $single = '';

    // Get Global, template, single data
    $get_option = get_option("wt_styles");
    $get_type_option = get_option("wt_" . $type . "_styles");

    if($type == "lostpassword" || $type == "register" || $type == "login"){
        $get_post_meta = get_option("wt_" . $type . "_styles");
    }else{
        $get_post_meta = get_post_meta($id, '_wt_styles', true);
    }

    // get global data
    if (empty($get_option) == false && $type != "lostpassword" && $type != "register" && $type != "login") {
        $global .= $get_option;
    }

    // Not load page template to Blog Page and Front Page
    if(get_option("show_on_front") == "page"){
        $FrontPage = get_option('page_on_front');
        $BlogPage = get_option('page_for_posts');
    }else{
        $FrontPage = 0;
        $BlogPage = 0;
    }

    // get template data
    if (empty($get_type_option) == false) {

        if($type != 'author' && $type != 'tag' && $type != 'category' && $type != '404' && $type != 'search' && $type != 'home' && $type != 'archive' && $type != "lostpassword" && $type != "register" && $type != "login"){
            if($id != $FrontPage && $id != $BlogPage){
                $template .= $get_type_option;
            }else if($id === 0){
                $template .= $get_type_option;
            }
        }

    }

    // get single data
    if (empty($get_post_meta) == false) {
        $single .= $get_post_meta;
    }

    // Advanced types
    if ($type == 'author') {
        $template .= get_option("wt_author_styles");
    }

    if ($type == 'tag') {
        $template .= get_option("wt_tag_styles");
    }

    if ($type == 'category') {
        $template .= get_option("wt_category_styles");
    }

    if ($type == 'archive') {
        $template .= get_option("wt_archive_styles");
    }

    if ($type == '404') {
        $template .= get_option("wt_404_styles");
    }

    if ($type == 'search') {
        $template .= get_option("wt_search_styles");
    }

    if ($type == 'home') {
        $single .= get_option("wt_home_styles");
    }

    // Get animations
    $animations = '';
    $all_options = wp_load_alloptions();foreach ($all_options as $name => $value) {if (stristr($name, 'yp_anim')) {$animations .= $value . str_replace("keyframes", "-webkit-keyframes", $value);}}

    // empty vars
    $globalActive = '';
    $templateActive = '';
    $singleActive = '';

    // add wyp-styles-area to active
    if($id_is && wyp_type_is_available("single")){
        $singleActive = ' id="wyp-styles-area"';
    }else if($type_is && wyp_type_is_available("template")){
        $templateActive = ' id="wyp-styles-area"';
    }else{
        $globalActive = ' id="wyp-styles-area"';
    }

    // Data Layout
    $return = '<div id="yellow-pencil-iframe-data"><!--

    <style class="wyp-inline-data"'.$globalActive.' data-source-mode="global">'.$global.'</style>
    <style class="wyp-inline-data"'.$templateActive.' data-source-mode="template">'.$template.'</style>
    <style class="wyp-inline-data"'.$singleActive.' data-source-mode="single">'.$single.'</style>';

    // return animations
    $return .= '<div id="wyp-animate-data"><style>' . $animations . '</style></div> --></div>';

    // return editor data
    echo wp_unslash($return);

}




/* ---------------------------------------------------- */
/* Include options Library
/* ---------------------------------------------------- */
include_once(YP_PLUGIN_DIR . 'editor/lib/options-controller.php');




/*-------------------------------------------------------*/
/*  Ajax Preview Save CallBack
/*-------------------------------------------------------*/
function wyp_preview_data_save() {

    $nonce = $_REQUEST["_wpnonce"];

    if (current_user_can("edit_theme_options") == true && wp_verify_nonce($nonce, "wyp_editor_nonce")) {

        $css = wp_strip_all_tags($_POST['wyp_data']);

        if (!update_option('yp_live_view_css_data', $css)) {
            add_option('yp_live_view_css_data', $css);
        }

    }else{

        if(current_user_can("edit_theme_options") == true){
            wp_die("nonce_error");
        }else{
            wp_die("authorized_error");
        }

    }

    wp_die();

}

add_action('wp_ajax_wyp_preview_data_save', 'wyp_preview_data_save');


/*-------------------------------------------------------*/
/*	Creating an Custom.css file (Static)
/*-------------------------------------------------------*/
function wyp_create_custom_css($data) {

    // by this point, the $wp_filesystem global should be working, so let's use it to create a file
    global $wp_filesystem;

    // Initialize the WP filesystem, no more using 'file-put-contents' function
    if (empty($wp_filesystem)) {
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }

    // Revisions
    $rev = get_option('yp_revisions');

    if ($rev == false) {
        $rev = 700;
    }

    // CSS Upload Directory
    $uploads_dir = wp_upload_dir()["basedir"] . '/yellow-pencil/';

    // Create folder if not available
    if(!is_dir($uploads_dir)){
        $wp_filesystem->mkdir($uploads_dir);
    }

    // Find all other old revisions
    $files = glob($uploads_dir . 'custom-*.css');

    // then delete old revisions before create new.
    foreach($files as $file){
        $wp_filesystem->delete($file);
    }

    // get the upload directory and make a test.txt file
    $filename = $uploads_dir . 'custom-' . $rev . '.css';
    
    if($data === NULL){
        $data = "";
    }

    if (!$wp_filesystem->put_contents($filename, $data, FS_CHMOD_FILE)) {
        echo 'Yellow Pencil: There was an error creating the custom.css file, please use "Dynamic Inline CSS" option.';
    }

}


/*-------------------------------------------------------*/
/*  Ajax Real Save Callback
/*-------------------------------------------------------*/
function wyp_ajax_save() {

    $nonce = $_REQUEST['_wpnonce'];

    if (current_user_can("edit_theme_options") == true && wp_verify_nonce($nonce, "wyp_editor_nonce")) {

        // Revisions
        $currentRevision = get_option('yp_revisions');

        // Update revision.
        if ($currentRevision != false) {
            update_option('yp_revisions', $currentRevision + 1);
        } else {
            add_option('yp_revisions', "1");
        }

        // Getting data
        $css = wp_strip_all_tags($_POST['wyp_data']);
        $styles = trim(wp_strip_all_tags($_POST['wyp_editor_data']));

        // replace ] */ to fix ajax problems.
        $styles = str_replace("YPOGRP", "/* [", $styles);
        $styles = str_replace("YPEGRP", "] */", $styles);

        $id   = '';
        $type = '';

        if (isset($_POST['wyp_page_id']) && empty($_POST['wyp_page_id']) == false) {
            $id = intval($_POST['wyp_page_id']);
        }

        if (isset($_POST['wyp_page_type']) && empty($_POST['wyp_page_type']) == false) {
            $type = trim(wp_strip_all_tags($_POST['wyp_page_type']));
            if (count(explode("#", $type)) == 2) {
                $type = explode("#", $type);
                $type = $type[0];
            }
        }

        // Global
        if ($id == '' && $type == '') {

            // CSS Data
            if (empty($css) == false) {
                if (!update_option('wt_css', $css)) {
                    add_option('wt_css', $css);
                }
            } else {
                delete_option('wt_css');
            }

            // Styles
            if (empty($css) == false) {
                if (!update_option('wt_styles', $styles)) {
                    add_option('wt_styles', $styles);
                }
            } else {
                delete_option('wt_styles');
            }

        // ID
        } elseif ($type == '') {

            // CSS Data
            if (empty($css) == false) {
                if (!update_post_meta($id, '_wt_css', wp_slash($css))) {
                    add_post_meta($id, '_wt_css', wp_slash($css), true);
                }
            } else {
                delete_post_meta($id, '_wt_css');
            }

            // Styles
            if (empty($css) == false) {
                if (!update_post_meta($id, '_wt_styles', wp_slash($styles))) {
                    add_post_meta($id, '_wt_styles', wp_slash($styles), true);
                }
            } else {
                delete_post_meta($id, '_wt_styles');
            }

        // Type
        } else {

            // CSS Data
            if (empty($css) == false) {
                if (!update_option('wt_' . $type . '_css', $css)) {
                    add_option('wt_' . $type . '_css', $css);
                }
            } else {
                delete_option('wt_' . $type . '_css');
            }

            // Styles
            if (empty($css) == false) {
                if (!update_option('wt_' . $type . '_styles', $styles)) {
                    add_option('wt_' . $type . '_styles', $styles);
                }
            } else {
                delete_option('wt_' . $type . '_styles');
            }

        }

    }else{

        if(current_user_can("edit_theme_options") == true){
            wp_die("nonce_error");
        }else{
            wp_die("authorized_error");
        }

    }

    wp_die();

}

add_action('wp_ajax_wyp_ajax_save', 'wyp_ajax_save');


/*-------------------------------------------------------*/
/*  Ajax Real Save Callback
/*-------------------------------------------------------*/
function wyp_ajax_update_css() {

    if (current_user_can("edit_theme_options") == true && check_admin_referer("wyp_editor_nonce")) {

        // Get All CSS data as ready-to-use
        $output = wyp_get_export_css("create");

        // Update custom.css file
        wyp_create_custom_css($output);

    }

    wp_die();

}

add_action('wp_ajax_wyp_ajax_update_css', 'wyp_ajax_update_css');



/* ---------------------------------------------------- */
/* Getting customizing type
/* ---------------------------------------------------- */
function wyp_customizing_type() {

    $type = $_GET['wyp_mode'];
    $typeSelf = strtolower(ucfirst(trim(wp_strip_all_tags($_GET['wyp_page_type']))));

    // Force Single
    if ($typeSelf == 'login' || $typeSelf == 'register' || $typeSelf == 'lostpassword') {
        return "single";
    }

    // requested "Single" if available
    if ($type == 'single' && wyp_type_is_available('single')) {

        $result = "single";

    // requested "Single" but if not available so setup "template"
    } elseif ($type == 'single' && wyp_type_is_available('template')) {

        $result = "template";

    // requested "Template" if available
    } elseif ($type == 'template' && wyp_type_is_available('template')) {

        $result = "template";

    // requested "Template" but if not available so setup "global"
    } elseif ($type == 'template' && wyp_type_is_available('global')) {

        $result = "global";

    // use global
    } elseif ($type == 'global' && wyp_type_is_available('global')){

        $result = "global";

    }

    return $result;

}


/* ---------------------------------------------------- */
/* Checking if these type is available in current page
/* ---------------------------------------------------- */
function wyp_type_is_available($gtype){

    // Get page type
    $id = trim(wp_strip_all_tags($_GET['wyp_page_id']));
    $type = trim(wp_strip_all_tags($_GET['wyp_page_type']));

    // Single is disabled on these types
    if($gtype == 'single'){

        if($type == 'author' || $type == 'tag' || $type == 'category' || $type == '404' || $type == 'search' || $type == 'archive'){
            return false;
        }

        if($id == "0" && $type != 'home'){
            return false;
        }

    }

    // Template is disabled on these types
    if($gtype == 'template'){

        if($type == 'home' || $type == "general" || $type == "login" || $type == "register" || $type == "lostpassword"){
            return false;
        }

        // get post page id
        if(get_option("show_on_front") == "page"){
            $page_for_posts = get_option('page_for_posts');
            $page_on_front = get_option('page_on_front');
        }else{
            $page_for_posts = 0;
            $page_on_front = 0;
        }

        // Template disabled on posts page
        if($id == $page_for_posts && $id != 0){
            return false;
        }

        // Template disabled on front page
        if($id == $page_on_front && $id != 0){
            return false;
        }

    }

    // Global disabled on these types
    if($gtype == "global"){

        if($type == "lostpassword" || $type == "register" || $type == "login"){
            return false;
        }

    }

    return true;

}


/* ---------------------------------------------------- */
/* Getting customizing types like a list
/* ---------------------------------------------------- */
function wyp_customizing_options(){

    // Get Current Type
    $type = wyp_customizing_type();

    // nulls
    $singleSelected = '';
    $templateSelected = '';
    $globalSelected = '';

    // disable single
    if(wyp_type_is_available('single') == false){
        $singleSelected = 'type-disabled';
    }

    // disable template
    if(wyp_type_is_available('template') == false){
        $templateSelected = 'type-disabled';
    }

    // disable global
    if(wyp_type_is_available('global') == false){
        $globalSelected = 'type-disabled';
    }

    // getting post type
    $page_type = $_GET['wyp_page_type'];

    // get slug label
    if (post_type_exists($page_type)){
        $page_type = get_post_type_object($page_type);
        $page_type = $page_type->labels->singular_name;
    }

    $page_type = '"'.ucfirst(strtolower(trim(wp_strip_all_tags($page_type)))).'" template';

    $defaultText = "";
    $singleText = wyp_page_name();
    $templateText = $page_type;
    $globalText = "Global";

    // Select Current
    if($type == 'single' && $singleSelected != "type-disabled"){

        $singleSelected = 'active-customizing-list';
        $defaultText = $singleText;

    }else if($type == 'template' && $templateSelected != "type-disabled"){

        $templateSelected = 'active-customizing-list';
        $defaultText = $templateText;

    }else{

        $globalSelected = 'active-customizing-list';
        $defaultText = $globalText;

    }



    // start
    $result = "<div id='customizing-mode' data-this-type='".$type."' class='wyp-type-menu-link'><span class='type-heading'>".$defaultText."</span> <span class='yicon icon-arrow-down'></span></div><div id='c-t-list'><ul>";

    // Options
    $result .= "<li data-value='single' class='".$singleSelected."'><i class='manage-this-type'></i><i class='reset-this-type'></i><h6><span><span class='current-type'></span>".$singleText."</span><small class='type-byte'><span>empty</span><i>changed</i></small></h6><p>apply style just to the current page.</p></li>";
    $result .= '<li data-value="template" class="'.$templateSelected.'"><i class="manage-this-type"></i><i class="reset-this-type"></i><h6><span><span class="current-type"></span>'.$templateText.'</span><small class="type-byte"><span>empty</span><i>changed</i></small></h6><p>apply style to all pages of the current post type.</p></li>';
    $result .= '<li data-value="global" class="'.$globalSelected.'"><i class="manage-this-type"></i><i class="reset-this-type"></i><h6><span><span class="current-type"></span>'.$globalText.'</span><small class="type-byte"><span>empty</span><i>changed</i></small></h6><p>apply style to the entire website.</p></li>';

    // switch to another page.
    $result .= '<li id="wyp-current-page"><h6>Customize another page</h6></li>';

    // close
    $result .= "</ul></div>";

    return $result;

}


/* ---------------------------------------------------- */
/* Getting current name
/* ---------------------------------------------------- */
function wyp_page_name(){

    $limit = 24;

    $result = "Unknown";

    // if page id isset
    if (isset($_GET['wyp_page_id'])) {

        // The id.
        $id = wp_strip_all_tags($_GET['wyp_page_id']);

        if($id == "lostpassword"){
            return 'Lost Password Page';
        }

        if($id == '404'){
            return '404 Error Page';
        }

        if($id == 'home'){
            return 'Homepage';
        }

        if($id == 'search'){
            return 'Search Results';
        }

        if(!is_numeric($id)){
            return ''.ucfirst(strtolower($id)).' page';
        }

        // Only Int
        $id = intval($id);

        $title = get_the_title($id);
        $slug  = get_post_type($id);

        // get slug label
        if (post_type_exists($slug)){
            $slug = get_post_type_object($slug);
            $slug = $slug->labels->singular_name;
        }

        $slug = strtolower($slug);
        $title = ucfirst(strtolower($title));

        if (strlen($title) > ($limit-strlen($slug)) ) {
            $result = '"' . mb_substr($title, 0, $limit, 'UTF-8') . '&hellip;" ' . $slug . '';
        } else {

            if ($title == '') {
                $title = "Untitled";
            }

            $result = '"' . $title . '" '.$slug;
        }

    }

    return $result;

}


/* ---------------------------------------------------- */
/* Adding helper style for wp-admin-bar                 */
/* ---------------------------------------------------- */
function wyp_yellow_pencil_style() {
    echo '<style>#wp-admin-bar-yp > .ab-item:before{content: "\f309";top:2px;}#wp-admin-bar-wyp-update .ab-item:before{content: "\f316";top:3px;}</style>';
}


/* ---------------------------------------------------- */
/* Trying to find all page information
/* ---------------------------------------------------- */
function wyp_get_page_ids(){

    global $wp_query;

    // Defaults
    $page_id = 0;
    $edit_mode = 'single';
    $page_type = "general";

    // Trying to getting the id
    if (isset($_GET['page_id']) && empty($_POST['page_id']) == false) {
        $page_id = intval($_GET['page_id']);
    } elseif (isset($_GET['post']) && empty($_GET['post']) == false && is_admin() == true) {
        $page_id = intval($_GET['post']);
    } elseif (isset($wp_query->queried_object->ID)) {
        $page_id = @$wp_query->queried_object->ID;
    }

    // Since 4.5.2
    // category,author,tag, 404 and archive page support.
    $page_type = get_post_type($page_id);

    // Getting specials pages
    if (is_author()) {

        $page_id = "author";
        $page_type = 'author';
        $edit_mode = 'template';

    } elseif (is_tag()) {

        $page_id = "tag";
        $page_type = 'tag';
        $edit_mode = 'template';

    } elseif (is_category()) {

        $page_id = "category";
        $page_type = 'category';
        $edit_mode = 'template';

    } elseif (is_404()) {

        $page_id = '404';
        $page_type = '404';
        $edit_mode = 'template';

    } elseif (is_archive()) {

        $page_id = 'archive';
        $page_type = 'archive';
        $edit_mode = 'template';

    } elseif (is_search()) {

        $page_id = 'search';
        $page_type = 'search';
        $edit_mode = 'template';

    }

    // Homepage
    if (is_front_page() && is_home()) {

        $page_id = 'home';
        $page_type = 'home';
        $edit_mode = 'single';

    }

    // WooCommerce Support
    if (class_exists('WooCommerce')) {

        // Shop Page
        if (is_shop()) {

            $page_id = wc_get_page_id('shop');
            $page_type = 'shop';
            $edit_mode = 'single';

        }

        // Product Category and tag
        if (is_product_category() || is_product_tag()) {

            $page_id = 0;
            $page_type = "general";
            $edit_mode = 'template';

        }

    }

    if(empty($page_type)){
        $page_type = "general";
    }

    // Use global in elementor_library
    if($page_type === "elementor_library"){
        $edit_mode = "global";
    }

    return array($page_id, $page_type, $edit_mode);

}


/* ---------------------------------------------------- */
/* Adding menu to wp-admin-bar							*/
/* ---------------------------------------------------- */
function wyp_yellow_pencil_edit_admin_bar($bar) {

    // get data
    $data = wyp_get_page_ids();

    // Getting page informations
    $page_id = $data[0];
    $page_type = $data[1];
    $edit_mode = $data[2];

    // get global option
    $global_option = get_option("yp-default-global");

    // use global instead of single
    if($edit_mode == "single" && $global_option == "1"){
        $edit_mode = "global";
    }

    // URL OF Editor
    $yellow_pencil_uri = wyp_get_uri();

    // Getting current page
    $href = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $href = remove_query_arg("wyp_rand", $href);
    $href = remove_query_arg("fl_builder", $href); // beaver builder param
    $href = remove_query_arg("et_fb", $href); // divi param

    // Append Menu
    $args = array(
        'id' => 'yp',
        'title' => 'Edit With YellowPencil',
        'href' => add_query_arg(array(
        'href' => wyp_urlencode(esc_url($href)),
        'wyp_page_id' => $page_id,
        'wyp_page_type' => $page_type,
        'wyp_mode' => $edit_mode
    ),$yellow_pencil_uri),
        'meta' => array(
            'class' => 'yp'
    ));

    // Add to wp admin bar
    $bar->add_node($args);

}


/* ---------------------------------------------------- */
/* Adding Body Classes
/* ---------------------------------------------------- */
function wyp_public_body_classes( $classes ) {

    if(!is_user_logged_in()){
        $classes[] = 'non-logged-in';
    }

    return $classes;

}

add_filter( 'body_class','wyp_public_body_classes' );


function wyp_body_class($classes) {

    $classes[] = 'yp-yellow-pencil';

    if (current_user_can("edit_theme_options") == false) {
        if (defined('YP_DEMO_MODE')) {
            $classes[] = 'yp-yellow-pencil-demo-mode';
        }
    }

    if (!defined('WTFV')) {
        $classes[] = 'wtfv';
    }

    return $classes;

}


/* ---------------------------------------------------- */
/* Customizing Type Iframe
/* ---------------------------------------------------- */
function wyp_customize_type_frame() {

    add_submenu_page("wyp-welcome-screen", "Customizing Type", "Customizing Type", 'edit_theme_options', 'yellow-pencil-customize-type', 'wyp_customizing_type_frame');

}

add_action('admin_menu', 'wyp_customize_type_frame');



/* ---------------------------------------------------- */
/*  We need an blank page (hack)
/* ---------------------------------------------------- */
function wyp_customizing_type_frame() {

}

add_action('load-admin_page_yellow-pencil-customize-type', 'wyp_customize_type_content');



/* ---------------------------------------------------- */
/*  Customize Type Popup
/* ---------------------------------------------------- */
function wyp_customize_type_content(){

    include(YP_PLUGIN_DIR . 'editor/lib/customization-popup.php');

    exit();

}



/* ---------------------------------------------------- */
/* Install the plugin
/* ---------------------------------------------------- */
function wyp_init() {


    // See Developer Documentation for more info.
    if (defined('YP_DEMO_MODE')) {
        include(YP_PLUGIN_DIR . 'editor/lib/demo-view.php');
    }


    // Iframe Settings.
    // Disable admin bar in iframe
    // Add Classes to iframe body.
    // Add Styles for iframe.
    if (wyp_check_let_frame()) {
        show_admin_bar(false);
        add_filter('body_class', 'wyp_body_class');
    }


    // If yellowpencil is active and theme support;
    // Adding Link to #wpadminbar.
    if (wyp_check_let()) {

        // If not admin page, Add Customizer link.
        if (is_admin() === false) {

            add_action('admin_bar_menu', 'wyp_yellow_pencil_edit_admin_bar', 999);

            // Adding CSS helper for admin bar link.
            add_action('wp_head', 'wyp_yellow_pencil_style');

        }

    }


    // Getting Current font families.
    add_action('wp_enqueue_scripts', 'wyp_load_fonts');
    add_action('login_enqueue_scripts', 'wyp_load_fonts_for_admin');

    // Live preview
    if (isset($_GET['wyp_live_preview']) == true) {
        add_action('wp_enqueue_scripts', 'wyp_load_fonts_for_live');
    }

    // Login preview
    if(isset($_GET['wyp_live_preview']) && $GLOBALS['pagenow'] === 'wp-login.php'){
        add_action('login_enqueue_scripts', 'wyp_load_fonts_for_admin_live');
    }

}

add_action("init", "wyp_init");




/* ---------------------------------------------------- */
/* Uploader Style
/* ---------------------------------------------------- */
function wyp_uploader_style() {

    if (isset($_GET['wyp_uploader']) && $_GET['wyp_uploader'] == 1) {

            echo '<style>
				tr.url,tr.post_content,tr.post_excerpt,tr.field,tr.label,tr.align,tr.image-size,tr.post_title,tr.image_alt,.del-link,#tab-type_url{display:none !important;}
				.media-item-info > tr > td > p:last-child,.savebutton,.ml-submit{display:none !important;}
				#media-upload #filter{width:auto !important;}
                .subsubsub{display:none !important;}
                .tablenav .alignleft.actions{display:none !important;}
                .tablenav{height:auto !important;margin:0 !important;}
                .tablenav-pages{margin:0px !important;text-align: right !important;}
                .media-upload-form{margin-top:0px !important;}
                #filter{margin-bottom:10px !important;}
                #media-search{display:none !important;}
                .tablenav .tablenav-pages a, .tablenav-pages-navspan{min-width: auto !important;font-size: 13px !important;}
				.media-item .describe input[type="text"], .media-item .describe textarea{width:334px;}
                .max-upload-size{opacity:0.7 !important;}
			</style>';

    }

}

add_action('admin_head', 'wyp_uploader_style');



/* ---------------------------------------------------- */
/* CSS library for YellowPencil
/* ---------------------------------------------------- */
function wyp_register_styles() {

    // by this point, the $wp_filesystem global should be working, so let's use it to create a file
    global $wp_filesystem;

    // Initialize the WP filesystem, no more using 'file-put-contents' function
    if (empty($wp_filesystem)) {
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }

    // Animate library for live preview
    if (isset($_GET['wyp_live_preview']) == true) {

        // Get CSS
        $css = wyp_get_live_css();

        // Test Animation
        if (strstr($css, "animation-name:")) {
            wp_enqueue_style('yellow-pencil-animate', plugins_url('public/css/animate.css?wypver='.YP_VERSION.'', __FILE__));
        }

    // No Live Preview
    }else{

        // Get CSS
        $css = wyp_get_css(true);

        // Animate library.
        if (strstr($css, "animation-name:")) {

            // Website
            if(isset($_GET['yellow_pencil_frame']) == false){

                // Load CSS if not draft mode
                if(get_option('yp-draft-mode') != '1'){
                    wp_enqueue_style('yellow-pencil-animate', plugins_url('public/css/animate.css?wypver='.YP_VERSION.'', __FILE__));
                }

            }

        }

    }

    // Add Custom.css to website.
    if (isset($_GET['yellow_pencil_frame']) == false && isset($_GET['wyp_live_preview']) == false && get_option('yp-output-option') == 'external') {

        // If not draft mode
        if(get_option('yp-draft-mode') != '1'){

            // New ref URL parameters on every new update.
            $rev = get_option('yp_revisions');

            if ($rev == false) {
                $rev = 700;
            }

            // Get uploads directory
            $uploads_dir = wp_upload_dir()["basedir"] . '/yellow-pencil/';

            // Get uploads URL
            $uploads_url = wp_upload_dir()["baseurl"] . '/yellow-pencil/';

            // Custom CSS Href
            $href = add_query_arg('revision', $rev, $uploads_url . 'custom-' . $rev . '.css');

            // First check if file is exists
            if($wp_filesystem->exists($uploads_dir . 'custom-'.$rev.'.css')){

                // Getting file size of custom css
                $customCSSSize = $wp_filesystem->size($uploads_dir . 'custom-'.$rev.'.css');

                // Add
                if($customCSSSize > 0){
                    wp_enqueue_style('wyp-custom', $href);
                }

            }

        }

    }

}

add_action('wp_enqueue_scripts', 'wyp_register_styles', 999999999);



/* ---------------------------------------------------- */
/* Jquery plugins for CSS Engine
/* ---------------------------------------------------- */
function wyp_register_scripts() {

    // if Not editor page
    if(isset($_GET['yellow_pencil_frame']) == false){

        // Get live preview CSS
        if(isset($_GET["wyp_live_preview"])){

            // add live preview CSS
            $css = get_option('yp_live_view_css_data');

        // Get direct saved CSS
        }else{

            // Get CSS data
            $css = wyp_get_css(true);

        }

        // Check if there any animation
        if (strstr($css, "animation-name:") == true || strstr($css, "animation-duration:") == true || strstr($css, "animation-delay:") == true) {

            // Live Preview
            if(isset($_GET["wyp_live_preview"])){

                // Loads
                wp_enqueue_script('yellow-pencil-library', plugins_url('public/js/animation-events.js?wypver='.YP_VERSION.'', __FILE__));

            // Website and If not draft mode
            }else if(get_option('yp-draft-mode') != '1'){

                // Loads
                wp_enqueue_script('yellow-pencil-library', plugins_url('public/js/animation-events.js?wypver='.YP_VERSION.'', __FILE__));

            }

        }

    }

}

add_action('wp_enqueue_scripts', 'wyp_register_scripts');



/* ---------------------------------------------------- */
/* Iframe Admin Page
/* ---------------------------------------------------- */
function wyp_yellow_pencil_editor() {

    add_submenu_page("wyp-welcome-screen", "YellowPencil Editor", "YellowPencil Editor", 'edit_theme_options', 'yellow-pencil-editor', 'wyp_editor_func');

}

add_action('admin_menu', 'wyp_yellow_pencil_editor');



/* ---------------------------------------------------- */
/*  We need an blank page (hack)
/* ---------------------------------------------------- */
function wyp_editor_func() {

}

add_action('load-admin_page_yellow-pencil-editor', 'wyp_frame_output');



/* ---------------------------------------------------- */
/* Custom Action yp_editor_header
/* ---------------------------------------------------- */
function yp_editor_header() {
    do_action('yp_editor_header');
}


/* ---------------------------------------------------- */
/* Custom Action yp_js_hook
/* ---------------------------------------------------- */
function yp_js_hook() {
    do_action('yp_js_hook');
}


/* ---------------------------------------------------- */
/* Custom Action yp_editor_footer
/* ---------------------------------------------------- */
function yp_editor_footer() {
    do_action('yp_editor_footer');
}


/* ---------------------------------------------------- */
/* Editor Page Markup
/* ---------------------------------------------------- */
function wyp_frame_output() {

    // Get protocol
    $protocol = is_ssl() ? 'https' : 'http';
    $protocol = $protocol . '://';

    // Fix WooCommerce shop page bug
    if (class_exists('WooCommerce')) {

        $currentID = 0;
        $href = '';

        // ID
        $currentID = intval($_GET['wyp_page_id']);

        // href
        $href = $_GET['href'];
        $type = $_GET['wyp_page_type'];
        $wyp_mode = $_GET['wyp_mode'];

        // get shop id
        $shopID = wc_get_page_id('shop');

        // If current id is shop
        if ($currentID == $shopID && $type != "shop") {

            // Redirect
            wp_safe_redirect(admin_url('admin.php?page=yellow-pencil-editor&href=' . wyp_urlencode(esc_url(get_post_type_archive_link("product"))) . '&wyp_page_id=' . $shopID . '&wyp_page_type=shop&wyp_mode='.$wyp_mode));
            exit;

        }

    }

    // Editor Markup
    include(YP_PLUGIN_DIR . 'editor/editor.php');

    exit;

}



/* ---------------------------------------------------- */
/* Adding link to plugins page
/* ---------------------------------------------------- */
add_filter('plugin_row_meta', 'wyp_plugin_links', 10, 2);

function wyp_plugin_links($links, $file) {

    if ($file == plugin_basename(dirname(__FILE__) . '/yellow-pencil.php')) {
        $links[] = '<a href="https://yellowpencil.waspthemes.com/documentation/">Documentation</a>';
    }

    return $links;

}



/* ---------------------------------------------------- */
/* Ading Prefix to CSS selectors for global export
/* ---------------------------------------------------- */
function wyp_add_prefix_to_css_selectors($css, $prefix) {

    # Wipe all block comments
    $css = preg_replace('!/\*.*?\*/!s', '', $css);

    $parts             = explode('}', $css);
    $mediaQueryStarted = false;

    foreach ($parts as &$part) {
        $part = trim($part); # Wht not trim immediately .. ?

        if (empty($part)) {
            continue;
        } else { # This else is also required

            $partDetails = explode('{', $part);

            if (substr_count($part, "{") == 2) {
                $mediaQuery        = $partDetails[0] . "{";
                $partDetails[0]    = $partDetails[1];
                $mediaQueryStarted = true;
            }

            $subParts = explode(',', $partDetails[0]);

            foreach ($subParts as &$subPart) {
                if (strstr(trim($subPart), "@") || strstr(trim($subPart), "%")) {
                    continue;
                } else {

                    // Selector
                    $subPart = trim($subPart);

                    // Array
                    $subPartArray = explode(" ", $subPart);
                    $low = strtolower($subPart);

                    $lMatch = str_replace("-", "US7XZX", $low);
                    $lMatch = str_replace("_", "TN9YTX", $lMatch);

                    preg_match_all("/\bbody\b/i", $lMatch, $bodyAll);
                    preg_match_all("/#body\b/i", $lMatch, $bodySlash);
                    preg_match_all("/\.body\b/i", $lMatch, $bodyDot);

                    preg_match_all("/\bhtml\b/i", $lMatch, $htmlAll);
                    preg_match_all("/#html\b/i", $lMatch, $htmlSlash);
                    preg_match_all("/\.html\b/i", $lMatch, $htmlDot);

                    // Get index of "body" term.
                    if (preg_match("/\bbody\b/i", $lMatch) && count($bodyAll[0]) > (count($bodyDot[0]) + count($bodySlash[0]))) {

                        $i     = 0;
                        $index = 0;
                        foreach ($subPartArray as $term) {
                            $term = trim(strtolower($term));
                            if ($term == 'body' || preg_match("/^body\./i", $term) || preg_match("/^body\#/i", $term) || preg_match("/^body\[/i", $term)) {
                                $index = $i;
                                break;
                            }
                            $i++;
                        }

                        // Adding prefix class to Body
                        $subPartArray[$index] = $subPartArray[$index] . $prefix;

                        // Update Selector
                        $subPart = implode(" ", $subPartArray);

                    } else if (preg_match("/\bhtml\b/i", $lMatch) && count($htmlAll[0]) > (count($htmlDot[0]) + count($htmlSlash[0]))) {

                        $i     = 0;
                        $index = 0;
                        foreach ($subPartArray as $term) {
                            $term = trim(strtolower($term));
                            if ($term == 'html' || preg_match("/^html\./i", $term) || preg_match("/^html\#/i", $term) || preg_match("/^html\[/i", $term)) {
                                $index = $i;
                                break;
                            }
                            $i++;
                        }

                        // Adding prefix class to Body
                        if (count($subPartArray) <= 1) {
                            if ($subPart != 'html' && preg_match("/^html\./i", $subPart) && preg_match("/^html\#/i", $subPart) && preg_match("/^html\[/i", $subPart)) {
                                $subPartArray[$index] = $subPartArray[$index] . " body" . $prefix;
                            }
                        } else {
                            $subPartArray[$index] = $subPartArray[$index] . " body" . $prefix;
                        }

                        // Update Selector
                        $subPart = implode(" ", $subPartArray);

                    } else {

                        // Adding prefix class to Body
                        $subPartArray[0] = "body" . $prefix . " " . $subPartArray[0];

                        // Update Selector
                        $subPart = implode(" ", $subPartArray);

                    }

                }
            }

            if (substr_count($part, "{") == 2) {
                $part = $mediaQuery . "\n" . implode(', ', $subParts) . "{" . $partDetails[2];
            } elseif (empty($part[0]) && $mediaQueryStarted) {
                $mediaQueryStarted = false;
                $part              = implode(', ', $subParts) . "{" . $partDetails[2] . "}\n"; //finish media query
            } else {
                if (isset($partDetails[1])) {
                    # Sometimes, without this check,
                    # there is an error-notice, we don't need that..
                    $part = implode(', ', $subParts) . "{" . $partDetails[1];
                }
            }

            unset($partDetails, $mediaQuery, $subParts); # Kill those three..

        }
        unset($part); # Kill this one as well
    }

    // Delete spaces
    $output = preg_replace('/\s+/', ' ', implode("} ", $parts));

    // Delete all other spaces
    $output = str_replace("{ ", "{", $output);
    $output = str_replace(" {", "{", $output);
    $output = str_replace("} ", "}", $output);
    $output = str_replace("; ", ";", $output);

    // Beatifull >
    $output = str_replace("{", "{\n\t", $output);
    $output = str_replace("}", "\n}\n\n", $output);
    $output = str_replace("}\n\n\n", "}\n\n", $output);
    $output = str_replace("){", "){\n", $output);
    $output = str_replace(";", ";\n\t", $output);
    $output = str_replace("\t\n}", "}", $output);
    $output = str_replace("}\n\n}", "\t}\n\n}\n\n", $output);


    # Finish with the whole new prefixed string/file in one line
    return (trim($output));

}


/* --------------------------------------------------------- */
/* Define Wtfv
/* --------------------------------------------------------- */
function wyp_define_wtfv(){

    // Get pcode
    $purchase_code = get_option("yp_purchase_code");

    // check
    if($purchase_code){
        if(!defined('WTFV')){
            define('WTFV',TRUE);
        }
    }

}

add_action("init","wyp_define_wtfv");


/* --------------------------------------------------------- */
/* Encoding & Decoding the data; Used for import and export
/* --------------------------------------------------------- */
function wyp_encode($value) {
    $func = 'base64' . '_encode';
    return $func($value);
}

function wyp_decode($value) {
    $func = 'base64' . '_decode';
    return $func($value);
}



/* ---------------------------------------------------- */
/* Getting All plugin options by prefix
/* ---------------------------------------------------- */
function wyp_get_all_options($prefix = '', $en = false) {

    global $wpdb;
    $ret     = array();
    $options = $wpdb->get_results($wpdb->prepare("SELECT option_name,option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $prefix . '%'), ARRAY_A);

    if (!empty($options)) {
        foreach ($options as $v) {
            if (strstr($v['option_name'], 'wt_theme') == false && strstr($v['option_name'], 'wt_available_version') == false && strstr($v['option_name'], 'wt_last_check_version') == false) {
                if ($en == true) {
                    $ret[$v['option_name']] = wyp_encode($v['option_value']);
                } else {
                    $ret[$v['option_name']] = wp_unslash($v['option_value']);
                }
            }
        }
    }

    return (!empty($ret)) ? $ret : false;

}



/* ---------------------------------------------------- */
/* Getting All post meta data by prefix
/* ---------------------------------------------------- */
function wyp_get_all_post_options($prefix = '', $en = false) {

    global $wpdb;
    $ret     = array();
    $options = $wpdb->get_results($wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $prefix . '%'), ARRAY_A);

    if (!empty($options)) {
        foreach ($options as $v) {
            if ($en == true) {
                $ret[$v['post_id'] . "." . $v['meta_key']] = wyp_encode($v['meta_value']);
            } else {
                $ret[$v['post_id'] . "." . $v['meta_key']] = wp_unslash($v['meta_value']);
            }
        }
    }

    return (!empty($ret)) ? $ret : false;

}



/* ---------------------------------------------------- */
/* Creating a json data for export data
/* ---------------------------------------------------- */
function wyp_get_export_data() {

    $allData       = array();
    $postmeta_CSS  = wyp_get_all_post_options('\_wt\_css', true);
    $postmeta_HTML = wyp_get_all_post_options('\_wt\_styles', true);
    $option_Data   = wyp_get_all_options('wt\_', true);
    $option_Anims  = wyp_get_all_options('yp\_anim', true);

    // @Ver 7.0.7
    $option_Output  = wyp_get_all_options('wyp-output-option', true); // output option
    $option_Comments  = wyp_get_all_options('yp\_selector\_comments', true); // selector comments
    $option_Option  = wyp_get_all_options('yp\_op\_', true); // Plugin options

    if (is_array($postmeta_CSS)) {
        array_push($allData, $postmeta_CSS);
    }

    if (is_array($postmeta_HTML)) {
        array_push($allData, $postmeta_HTML);
    }

    if (is_array($option_Data)) {
        array_push($allData, $option_Data);
    }

    if (is_array($option_Anims)) {
        array_push($allData, $option_Anims);
    }

    // @Ver 7.0.7
    if (is_array($option_Output)) {
        array_push($allData, $option_Output);
    }

    if (is_array($option_Comments)) {
        array_push($allData, $option_Comments);
    }

    if (is_array($option_Option)) {
        array_push($allData, $option_Option);
    }

    if (empty($allData) == false) {
        $data     = array_values($allData);
        $jsonData = json_encode($data);
        return $jsonData;
    }

    return false;

}



/* ---------------------------------------------------- */
/* Generate All CSS styles as ready-to-use
/* ---------------------------------------------------- */
function wyp_get_export_css($method) {

    // Array
    $allData = array();

    // Getting all from database
    $postmeta_CSS = wyp_get_all_post_options('\_wt\_css', false);
    $option_Data  = wyp_get_all_options('wt\_', false);
    $option_Anims = wyp_get_all_options('yp\_anim', false);

    // Push option data to Array
    if (is_array($option_Data)) {
        array_push($allData, $option_Data);
    }

    // Push postmeta data to Array
    if (is_array($postmeta_CSS)) {
        array_push($allData, $postmeta_CSS);
    }

    // Check if there have animations
    if (is_array($option_Anims)) {

        // Push custom animations to Array
        array_push($allData, $option_Anims);

        // New Array for webkit prefix
        $option_AnimWebkit = array();

        // Copy animations as webkit
        foreach ($option_Anims as $key => $animate) {
            $option_AnimWebkit["Webkit " . $key] = str_replace("@keyframes", "@-webkit-keyframes", $animate);
        }

        // Push Animations
        array_push($allData, $option_AnimWebkit);

    }

    // Be sure The data not empty
    if (empty($allData) == false) {

        // Clean array
        $data = array_values($allData);

        // Variables
        $output     = null;
        $table      = array();
        $tableIndex = 0;
        $prefix     = '';

        // Adding WordPress Page, category etc classes to all CSS Selectors.
        foreach ($data as $nodes) {

            // set necessary order
            $orderArray = array(
                'wt_css' => '',
                'wt_post_css' => '',
                'wt_page_css' => '',
                'wt_search_css' => '',
                'wt_tag_css' => '',
                'wt_category_css' => '',
                'wt_archive_css' => '',
                'wt_author_css' => '',
                'wt_404_css' => '',
            );
            //apply it
            $nodes = array_filter(array_replace($orderArray, $nodes));

            foreach ($nodes as $key => $css) {
                $tableIndex++;

                // skip style data options
                if (strstr($key, '_styles')) {
                    continue;
                }

                // Skip admin CSS in custom.css
                if($key == "wt_login_css" || $key == "wt_lostpassword_css" || $key == "wt_register_css"){
                    continue;
                }

                // blog
                if(get_option("show_on_front") == "page"){
                    $page_for_posts = get_option('page_for_posts');
                }else{
                    $page_for_posts = 0;
                }


                // dont add default home style to static home page
                if($key == "wt_home_css" && $page_for_posts != 0){
                    continue;
                }

                // If post meta
                if (strstr($key, '._')) {

                    $keyArray = explode(".", $key);
                    $postID   = $keyArray[0];
                    $type     = get_post_type($postID);
                    $title    = '"' . ucfirst(get_the_title($postID)) . '" ' . ucfirst($type) . '';

                    // Single post types
                    if ($page_for_posts == $postID) {
                        $prefix = '.blog';
                    } elseif ($type == 'page') {
                        $prefix = '.page-id-' . $postID . '';
                    } else {
                        $prefix = '.postid-' . $postID . '';
                    }

                    // not have page-id class in WooCommerce shop page.
                    if (class_exists('WooCommerce')) {
                        $shopID = wc_get_page_id('shop');
                        if ($postID == $shopID) {
                            $prefix = '.post-type-archive-product';
                        }
                    }

                } else {

                    if ($key == 'wt_css') {
                        $title  = 'Global Styles';
                        $prefix = '';
                    } else if ($key == 'wt_author_css') {
                        $title  = 'Author Page';
                        $prefix = '.author';
                    } else if ($key == 'wt_category_css') {
                        $title  = 'Category Page';
                        $prefix = '.category';
                    } else if ($key == 'wt_tag_css') {
                        $title  = 'Tag Page';
                        $prefix = '.tag';
                    } else if ($key == 'wt_404_css') {
                        $title  = '404 Error Page';
                        $prefix = '.error404';
                    } else if ($key == 'wt_search_css') {
                        $title  = 'Search Page';
                        $prefix = '.search';
                    } else if ($key == 'wt_home_css') {
                        $title  = 'Non-Static Homepage';
                        $prefix = '.home';
                    } else if ($key == 'wt_archive_css') {
                        $title  = 'Archive Page';
                        $prefix = '.archive';
                    }

                    // If anim
                    else if (strstr($key, 'yp_anim')) {
                        $title = str_replace("yp_anim_", "", $key);
                        $title = $title . " Animate";

                    // if post type
                    } else if (strstr($key, 'wt_') && strstr($key, '_css')) {

                        $title = str_replace("wt_", "", $key);
                        $title = str_replace("_css", "", $title);

                        if (strtolower($title) == 'page'){
                            $prefix = '.page:not(.home)';
                        } else if (strtolower($title) == 'shop'){
                            $prefix = '.post-type-archive-product';
                        } else {
                            $prefix = '.single-' . strtolower($title) . '';
                        }

                        $title = $title . " Template";

                    }

                }

                if(!isset($title)){
                  $title = "Unknown";
                }

                $len   = 48 - (strlen($title) + 2);
                $extra = null;

                for ($i = 1; $i < $len; $i++) {
                    $extra .= ' ';
                }

                array_push($table, ucfirst($title));
                $output .= "/*-----------------------------------------------*/\r\n";
                $output .= "/*  " . ucfirst($title) . "" . $extra . "*/\r\n";
                $output .= "/*-----------------------------------------------*/\r\n";
                $output .= wyp_add_prefix_to_css_selectors($css, $prefix) . "\r\n\r\n\r\n\r\n";

            }

        }
        // Foreach end.


        // Create a table list for CSS codes
        $tableList   = null;
        $plusNumber  = 1;
        $googleFonts = array();

        // Get fonts from CSS output
        if ($method == 'export') {
            $googleFonts = wyp_get_font_families($output, 'import');
        }

        // If has any Google Font; Add Font familes to first table list.
        if (count($googleFonts) > 0) {
            $tableList  = "    01. Font Families\r\n";
            $plusNumber = 2;
        }

        // Creating a table list.
        foreach ($table as $key => $value) {
            $tableList .= "    " . sprintf("%02d", $key + $plusNumber) . ". " . $value . "\r\n";
        }


        // Google Fonts
        if (count($googleFonts) > 0 && is_array($googleFonts)) {
            $FontsCSS = "/*-----------------------------------------------*/\r\n";
            $FontsCSS .= "/* Font Families                                 */\r\n";
            $FontsCSS .= "/*-----------------------------------------------*/\r\n";

            foreach ($googleFonts as $fontURL) {
                $FontsCSS .= "@import url('" . $fontURL . "');\r\n";
            }

            $FontsCSS .= "\r\n\r\n\r\n";
        }


        // All in.
        $allOutPut = "/*\r\n\r\n    The following CSS codes are created by the YellowPencil plugin.\r\n";
        $allOutPut .= "    https://yellowpencil.waspthemes.com/\r\n\r\n\r\n";
        $allOutPut .= "    T A B L E   O F   C O N T E N T S\r\n";
        $allOutPut .= "    ........................................................................\r\n\r\n";
        $allOutPut .= $tableList;
        $allOutPut .= "\r\n*/\r\n\r\n\r\n\r\n";

        // Adding Google Fonts to OutPut.
        if (count($googleFonts) > 0) {
            $allOutPut .= $FontsCSS;
        }

        // Adding all CSS codues
        $allOutPut .= $output;

        // Process with some PHP functions and return Output CSS code.
        if ($method == 'export') {
            return wyp_auto_prefix(wyp_export_animation_prefix(trim($allOutPut)));
        } else {
            return wyp_auto_prefix(trim($allOutPut));
        }

    }

}


/* ---------------------------------------------------- */
/* Import Plugin data
/* ---------------------------------------------------- */
function wyp_import_data($json) {

    if (empty($json)) {
        return false;
    }

    $json = wp_unslash($json);

    $array = json_decode($json, true);

    // No error
    if(json_last_error() === JSON_ERROR_NONE){

      // Continue
      foreach ($array as $nodes) {

          foreach ($nodes as $key => $value) {

              $value = wyp_decode($value);

              // If post meta
              if (strstr($key, '._')) {

                  $keyArray = explode(".", $key);
                  $postID   = $keyArray[0];
                  $metaKey  = $keyArray[1];

                  if (!update_post_meta($postID, $metaKey, wp_slash($value))) {
                      add_post_meta($postID, $metaKey, wp_slash($value), true);
                  }

              } else { // else option
                  if (!update_option($key, $value)) {
                      add_option($key, $value);
                  }
              }

          }

      }

    }

}



/* ---------------------------------------------------- */
/* Export CSS as style.css
/* ---------------------------------------------------- */
function wyp_exportCSS_admin_header() {

    if (current_user_can("export") && isset($_GET['wyp_exportCSS']) && check_admin_referer("wyp_export_nonce")) {

        $data = wyp_get_export_css("export");

        header('Content-Disposition: attachment; filename="style-' . strtolower(date("M-d")) . '.css"');
        header("Content-type: text/css; charset: UTF-8");
        header('Content-Length: ' . strlen($data));
        header('Connection: close');

        echo $data;

        die();

    }

}

add_action("admin_init", "wyp_exportCSS_admin_header", 999999999);


// @WaspThemes.
// Coded With Love..
