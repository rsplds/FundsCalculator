<?php

/**
 * Add Option Page as submenu to Platform Posttype
 *
 */

function platform_submenu_options() {
	add_submenu_page( 'edit.php?post_type=platform', __( 'Platform Options', 'fundscape_calculations' ), __( 'Platform Options', 'fundscape_calculations' ), 'manage_options', 'platform_options', 'platform_options_function' );
}
add_action('admin_menu', 'platform_submenu_options');


/**
 *  Platform Options display Function
 *
 */
function platform_options_function() {

	/* Save Options */
	if ( !empty($_POST) ) {

		if ( !isset($_POST["meta-box-nonce"]) || !wp_verify_nonce( $_POST["meta-box-nonce"], basename(__FILE__) ) ) {
			wp_die('Nonce verification failed');
		}

		if ( isset($_POST['active_platform']) ) {
			$active_platform = $_POST['active_platform'];
			$active_platform = !empty( $active_platform ) ? $active_platform : 'none';
			update_option( 'active_platform', $active_platform );
		}
	}

?>
	<h1>Platform Options</h1>
	<div class="platform-admin">
		<form name="platform_active_form" id="platform_active_form" method="post">

			<?php wp_nonce_field( basename(__FILE__), "meta-box-nonce" ); ?>

			<table class="wp-list-table widefat fixed striped posts">
				<tr>
					<th>Select Active Platform</th>
				</tr>
				<tr>
					<td>
						<?php $active_platform = stripslashes( get_option( 'active_platform' ) ); ?>
						<div>
							<select name="active_platform" id="active_platform">
								<?php
									$all_platforms = get_posts( array( 'post_type' => 'platform', 'numberposts' => -1 ) );
									foreach ( $all_platforms as $key => $value ) {
										if ( $value->ID == $active_platform ) {
											$selected = 'selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $value->ID . '" ' . $selected . '>' . $value->post_title . '</option>';
									}
								?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td><input type="submit" id="sevf_submit" value="Save" class="button button-primary button-large"></td>
				</tr>
			</table>
		</form>

		<table class="wp-list-table widefat fixed striped posts">
			<tr>
				<th>
					About Fundscape Calculation
				</th>
			</tr>
			<tr>
				<td>
					<h3>Basic Info</h3>
					<hr>
					<p>This plugin is used to calculate GIA & ISA charges based on your investments. </p>
					<p>&nbsp;</p>
					<h3>How to use?</h3>
					<hr>
					<h4>For Admin:</h4>
					<p>As a Admin, First you have to create a platform with necessary values.</p>
					<p>After creating a platform, visit platform settings page and save it as a active platform to make calculations.</p>
					<h4>To display form on Front-end:</h4>
					<p>To display form on Front-end, you use our shortcode <span class="highlight">[fundscape_form]</span> in any of the page or post content editor. or You can use php function <span class="highlight">&lt;?php echo do_shortcode( '[fundscape_form]' ); ?&gt;</span> in the PHP file direct.</p>
					<p>You can also use our plugin created page <a href="<?php echo esc_url( home_url( '/fundscape-calculations' ) ); ?>" target="_blank">Fundscape Calculations</a></p>
				</td>
			</tr>
		</table>
	</div>

<?php } ?>