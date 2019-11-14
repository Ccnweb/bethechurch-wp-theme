<?php 
require_once(CCN_LIBRARY_PLUGIN_DIR . '/lib.php'); use \ccn\lib as lib;

if (is_user_logged_in() && current_user_can('edit_posts')) {

// get info on user
$user = wp_get_current_user();
$roles = ( array ) $user->roles;

// init
lib\php_console_log('=== USER LOGGED IN ===', 'log', 'color:blue;font-weight:bold;');
lib\php_console_log('user roles :'.json_encode($roles));

wp_enqueue_style('ccnbtc-translation-style');
wp_enqueue_script('ccnbtc-translation-script');
?>

<button id="activate_translation" btc-editable="false" onclick="toggle_translation()"><i class="fas fa-edit"></i></button>

<?php } ?>