<?php
/**
 * This is a partial override of the password reset functionality for WordPress 3.1+. If the user enters
 * a bad password then they are returned to the password reset form with appropriate errors displayed.
 * Otherwise WordPress handles the password reset functionality as normal.
 */
function badpass_wp_password_reset_override() {
	//Grab the global common password array
	global $badpass_wp_common_passwords;
	
	//Check the password reset key
	$user = check_password_reset_key( $_GET['key'], $_GET['login'] );
	if ( is_wp_error( $user ) ) {
		wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=invalidkey' ) );
		exit;
	}
	
	//Check the password entered in the password reset form
	$errors = '';
	if ( isset( $_POST['pass1'] ) ) {
		//Check if it is one of the common passwords
		if ( in_array( strtolower( $_POST['pass1'] ), $badpass_wp_common_passwords ) ) {
			$errors = new WP_Error( 'badpass_wp_common_password', __( '<strong>ERROR:</strong> The password you entered is a commonly used password that can be guessed easily. Please use a different password.', 'badpass_wp' ) );
		}
		
		//Check if it is the same as the user's login name
		if ( strtolower( $user->user_login ) == strtolower( $_POST['pass1'] ) ) {
			$errors = new WP_Error( 'badpass_wp_password_is_login_name', __( '<strong>ERROR:</strong> Your password cannot be the same as your login name.' , 'badpass_wp' ) );
		}
	}
	
	//If no error occurred then bail out here and continue with the default WordPress functionality
	if ( '' == $errors ) {
		return;
	}
	
	//Display the password reset form with appropriate errors
	wp_enqueue_script( 'utils' );
	wp_enqueue_script( 'user-profile' );
	
	login_header( __( 'Reset Password' ), '<p class="message reset-pass">' . __( 'Enter your new password below.' ) . '</p>', $errors );
		?>
<form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass&key=' . urlencode( $_GET['key'] ) . '&login=' . urlencode( $_GET['login'] ), 'login_post' ) ?>" method="post">
	<input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />

	<p>
		<label><?php _e( 'New password' ) ?><br />
		<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" /></label>
	</p>
	<p>
		<label><?php _e( 'Confirm new password' ) ?><br />
		<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" /></label>
	</p>

	<div id="pass-strength-result" class="hide-if-no-js"><?php _e( 'Strength indicator' ); ?></div>
	<p class="description indicator-hint"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).' ); ?></p>

	<br class="clear" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e( 'Reset Password' ); ?>" tabindex="100" /></p>
</form>

<p id="nav">
<a href="<?php echo site_url( 'wp-login.php', 'login' ) ?>"><?php _e( 'Log in' ) ?></a>
<?php if ( get_option( 'users_can_register' ) ) : ?>
 | <a href="<?php echo site_url( 'wp-login.php?action=register', 'login' ) ?>"><?php _e( 'Register' ) ?></a>
<?php endif; ?>
</p>

<?php
	
	//Display the password reset form footer
	login_footer( 'user_pass' );
	
	//End execution here to prevent the default WordPress password reset functionality from executing
	exit;
}
?>