<?php
/*
Plugin Name: BadPass-WP
Plugin URI: http://www.nickbloor.co.uk/badpass/badpass-wp
Description: Helps to promote better password selection by warning users when they are using a common password.
Version: 1.2
Author: Nick Bloor
Author URI: http://www.nickbloor.co.uk/
*/

//Include the common-passwords file
require_once( dirname( __FILE__ ) . '/common-passwords.php' );
require_once( dirname( __FILE__ ) . '/password-reset.php' );

/**
 * When a user updates their password through the profile page BadPass-WP checks the password and
 * sets this global variable if there is a problem.
 * @var string
 */
$badpass_wp_profile_update_error = '';

//Set up WordPress action handlers
add_action( 'wp_print_styles', 'badpass_wp_add_stylesheet' );
add_action( 'profile_update', 'badpass_wp_profile_updated', 10, 1 );
add_action( 'wp_footer', 'badpass_wp_footer' );
add_action( 'check_passwords', 'badpass_wp_update_password_check', 10, 3 );
add_action( 'user_profile_update_errors', 'badpass_wp_update_password_error', 10, 1 );
add_action( 'login_form_rp', 'badpass_wp_password_reset' );
add_action( 'login_form_resetpass', 'badpass_wp_password_reset' );
add_action( 'password_reset', 'badpass_wp_on_password_reset', 10, 1 );

//Set up a deactivation hook
register_deactivation_hook( __FILE__, 'badpass_wp_deactivate' );

/**
 * Inject the stylesheet for the BadPass warning message.
 */
function badpass_wp_add_stylesheet() {
	//Inject the BadPass warning message stylesheet into the page
	if ( file_exists( WP_PLUGIN_DIR . '/badpass-wp/badpass-wp.css' ) ) {
		wp_register_style( 'badpass-wp-stylesheet', WP_PLUGIN_URL . '/badpass-wp/badpass-wp.css' );
		wp_enqueue_style( 'badpass-wp-stylesheet' );
	}
}

/**
 * Set the 'using common password' user meta field to false when a user's profile is updated because
 * a bad password cannot be entered in the change password form.
 * 
 * @param integer $user_id the ID of the user profile that was updated
 */
function badpass_wp_profile_updated( $user_id ) {
	//Clear the 'using common password' user meta field in case the user's password changed
	update_user_meta( $user_id, 'badpass_wp_using_common_password', 'false' );
}

/**
 * Set the 'using common password' user meta field to false when a user's password is reset because
 * a bad password cannot be entered in the password reset form.
 * 
 * @param WP_User $user the user whose password was reset
 */
function badpass_wp_on_password_reset( $user ) {
	//Clear the 'using common password' user meta field in case the user's password changed
	update_user_meta( $user->ID, 'badpass_wp_using_common_password', 'false' );
}

/**
 * Outputs a warning if a user is logged in and using a common password.
 */
function badpass_wp_footer() {
	//A user must be logged in before their password can be checked
	if ( is_user_logged_in() ) {
		//Retrieve the logged in user details
		$current_user = wp_get_current_user();
		
		//Retrieve the 'using common password' user meta field for the logged in user
		$using_common_password = get_user_meta( $current_user->ID, 'badpass_wp_using_common_password', true );
		
		//Test the user's password and set the 'using common password' user meta field if necessary
		if ( '' == $using_common_password ) {
			//Store the result of the password test to use in deciding whether to output a warning
			$using_common_password = ( badpass_wp_test_password( $current_user ) ? 'true' : 'false' );
		} 
		
		//Output a warning message if the user is using a common password
		if ( 'true' == $using_common_password ) {
			badpass_wp_output_warning();
		}
	}
}

/**
 * Displays a warning message to the user and a link to wp-admin/profile.php so they can update their
 * password.
 */
function badpass_wp_output_warning() {
	//Check if the WordPress 3.1+ admin bar is enabled
	$current_user = wp_get_current_user();
	$show_admin_bar = get_user_meta( $current_user->ID, 'show_admin_bar_front', true );
	
	//Output a warning
	echo '<p id="badpass_wp_warning' .
		( $show_admin_bar == 'true' ? '_withadminbar' : '' ) .
		'" >' .
		__( '<strong>WARNING:</strong> The password you are using is a commonly used password that can be guessed easily. It is recommended that you change it immediately.', 'badpass_wp' ) .
		'<a href="' . site_url( '/wp-admin/profile.php' ) . '">' .
		__( 'Click here to update your password.', 'badpass_wp' ) . '</a>' .
		'</p>';
}

/**
 * Called when the user changes the password through their profile page. If the password is a bad one
 * then a global error message variable is set and used to output an error in
 * badpass_wp_update_password_error().
 * 
 * @param string $user the login name of the user
 * @param string $pass1 the password entered
 * @param string $pass2 the password (entered again for verification)
 */
function badpass_wp_update_password_check( $user, $pass1, $pass2 ) {
	//Grab the global error and common password variables
	global $badpass_wp_profile_update_error;
	global $badpass_wp_common_passwords;
	
	//Check if the submitted password is a bad one
	if ( in_array( strtolower( $pass1 ), $badpass_wp_common_passwords ) ) {
		//Bad password!
		$badpass_wp_profile_update_error = __( '<strong>ERROR:</strong> The password you entered is a commonly used password that can be guessed easily. Please use a different password.', 'badpass_wp' );
	} else {
		$badpass_wp_profile_update_error = '';
	}
	
	//Check if the submitted password is the same as the login name
	if ( strtolower( $user ) == strtolower( $pass1 ) ) {
		//Bad password!
		$badpass_wp_profile_update_error = __( '<strong>ERROR:</strong> Your password cannot be the same as your login name.', 'badpass_wp' );
	}
}

/**
 * When the user profile is updated this function checks the global password error message and generates
 * an error if necessary.
 * 
 * @param WP_Error $errors
 */
function badpass_wp_update_password_error( $errors ) {
	//Grab the global error variable
	global $badpass_wp_profile_update_error;
	
	//If an error occurred whilst changing the password then add it to $errors
	if ( '' != $badpass_wp_profile_update_error ) {
		$errors->add( 'pass', $badpass_wp_profile_update_error );
	}
}

/**
 * This function partially overrides the password reset page to prevent the user from entering a bad
 * password. If a bad password is not detected then execution falls through to the standard WordPress
 * password reset functionality.
 */
function badpass_wp_password_reset() {
	if( function_exists( 'check_password_reset_key' ) ) {
		badpass_wp_password_reset_override();
	}
}

/**
 * Remove all 'using common password' user meta field values when the plugin is disabled.
 */
function badpass_wp_deactivate() {
	//Remove all badpass_wp_using_common_password meta fields
	delete_metadata( 'user', -1, 'badpass_wp_using_common_password', '', true );
}
?>