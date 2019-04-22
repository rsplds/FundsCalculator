<?php

/*
 *  Add Meta Box For Platform Funds & Exchange Trades
 *
 */
function platform_info() {
	add_meta_box( 'platform-info', 'Platform Data', 'platform_meta_fields', 'platform', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'platform_info' );


/*
 *  Add Custom Fields to Meta Box
 *
 */
function platform_meta_fields() {

	wp_nonce_field(basename(__FILE__), "meta-box-nonce");

	global $post, $wpdb;

	$table_name = $wpdb->prefix . 'fundscape_management';
	$platform_data = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE platform_id='" . $post->ID . "'" );

	$tiering_methods = array(
							"1" => "One rate based on total assets",
							"2" => "Combined tiered rate (Split)",
							"3" => "Combined tiered rate (Not split)",
							"4" => "Combined tiered rate using total assets across all products (Split)",
							"5" => "Combined tiered rate using total assets across all products (Not split)",
						);

?>
	<table class="platform-table">
		<tr>
			<td>
				<div class="platform-section">
					<div class="platform-fields-container">
						<div class="platform-fields-section">
							<label>Select Method</label>
							<select name="platform_method" id="platform_method">
								<?php
									$tiering_selected = '';
									foreach ( $tiering_methods as $key => $value ) {
										if ( !empty( $platform_data ) ) {
											if ( $key == $platform_data[0]->platform_method ) {
												$tiering_selected = 'selected';
											} else {
												$tiering_selected = '';
											}
										}
										echo '<option value="' . $key . '" ' . $tiering_selected . '>' . $value . '</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="platform-title">Funds</div>
			</td>
		</tr>
		<?php
			$funds_count = 1;
		?>
		<tr>
			<td>
				<div class="platform-section">
					<div class="platform-outer" id="funds-section">
						<?php
							$is_edit = false;
							if ( !empty( $platform_data ) ) :
								$platform_funds = json_decode( $platform_data[0]->platform_funds, true );
								if ( !empty( $platform_funds ) ) :
									foreach ( $platform_funds as $key => $value ) {
										$is_edit = true;
						?>
							<div class="platform-fields-container">
								<div class="platform-fields-section tierlabel"> Tier <?php echo $funds_count; ?> </div>
								<div class="platform-fields-section">
									<label>Bands From</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][bandsfrom]" min="0" value="<?php echo $value['bandsfrom']; ?>" />
								</div>
								<div class="platform-fields-section">
									<label>Bands To</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][bandsto]" min="0" value="<?php echo $value['bandsto']; ?>" />
								</div>
								<div class="platform-fields-section">
									<label>GIA</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][gia]" min="0" step="any" value="<?php echo $value['gia']; ?>" />
								</div>
								<div class="platform-fields-section">
									<label>ISA</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][isa]" min="0" step="any" value="<?php echo $value['isa']; ?>" />
								</div>
								<div class="platform-fields-section platfrom-remove-btn">
									<a href="javascript:" class="remove-funds button-primary dashicons-before dashicons-minus" data-count="<?php echo $funds_count; ?>"></a>
								</div>
							</div>
						<?php
									$funds_count++;
									}

								endif;
							else :
						?>
							<div class="platform-fields-container">
								<div class="platform-fields-section tierlabel">Tier <?php echo $funds_count; ?></div>
								<div class="platform-fields-section">
									<label>Bands From</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][bandsfrom]" min="0" />
								</div>
								<div class="platform-fields-section">
									<label>Bands To</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][bandsto]" min="0" />
								</div>
								<div class="platform-fields-section">
									<label>GIA</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][gia]" min="0" step="any" />
								</div>
								<div class="platform-fields-section">
									<label>ISA</label>
									<input type="number" name="funds[<?php echo $funds_count; ?>][isa]" min="0" step="any" />
								</div>
								<div class="platform-fields-section platfrom-remove-btn">
									<a href="javascript:" class="remove-funds button-primary dashicons-before dashicons-minus " data-count="<?php echo $funds_count; ?>"></a>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<div class="add-md-area clearfix">
						<?php 
							if( $is_edit == true ){
								?>
								<input type="hidden" id="funds_count" value="<?php echo $funds_count; ?>" />
								<?php 
							}else{
								?>
								<input type="hidden" id="funds_count" value="<?php echo $funds_count+1; ?>" />
								<?php
							} 
						?>
						<!-- <input type="hidden" id="funds_count" value="<?php echo $funds_count+1; ?>" /> -->
						<a href="javascript:" class="add-funds button-primary dashicons-before dashicons-plus"> Add</a>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="platform-title">Exchange Trades</div>
			</td>
		</tr>
		<?php $et_count = 1; ?>
		<tr>
			<td>
				<div class="platform-section">
					<div class="platform-outer" id="exchange-trades-section">
						<?php
							if ( !empty( $platform_data ) ) :
								$platform_exchange_trades = json_decode( $platform_data[0]->platform_exchange_trades, true );
								if ( !empty( $platform_exchange_trades ) ) :
									foreach ( $platform_exchange_trades as $key => $value ) {
						?>
							<div class="platform-fields-container">
								<div class="platform-fields-section tierlabel">Tier <?php echo $et_count; ?></div>
								<div class="platform-fields-section">
									<label>Bands From</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][bandsfrom]" min="0" value="<?php echo $value['bandsfrom']; ?>" />
								</div>
								<div class="platform-fields-section">
									<label>Bands To</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][bandsto]" min="0" value="<?php echo $value['bandsto']; ?>" />
								</div>
								<div class="platform-fields-section">
									<label>GIA</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][gia]" min="0" step="any" value="<?php echo $value['gia']; ?>" />
								</div>
								<div class="platform-fields-section">
									<label>ISA</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][isa]" min="0" step="any" value="<?php echo $value['isa']; ?>" />
								</div>
								<div class="platform-fields-section platfrom-remove-btn">
									<a href="javascript:" class="remove-exchange-trades button-primary dashicons-before dashicons-minus" data-count="<?php echo $et_count; ?>"></a>
								</div>
							</div>
						<?php
									$et_count++;
									}
								endif;
							else :
						?>
							<div class="platform-fields-container">
								<div class="platform-fields-section tierlabel">Tier <?php echo $et_count; ?></div>
								<div class="platform-fields-section">
									<label>Bands From</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][bandsfrom]" min="0" />
								</div>
								<div class="platform-fields-section">
									<label>Bands To</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][bandsto]" min="0" />
								</div>
								<div class="platform-fields-section">
									<label>GIA</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][gia]" min="0" step="any" />
								</div>
								<div class="platform-fields-section">
									<label>ISA</label>
									<input type="number" name="exchange_trades[<?php echo $et_count; ?>][isa]" min="0" step="any" />
								</div>
								<div class="platform-fields-section platfrom-remove-btn">
									<a href="javascript:" class="remove-exchange-trades button-primary dashicons-before dashicons-minus" data-count="<?php echo $et_count; ?>"></a>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<div class="add-md-area clearfix">
						<!-- <input type="hidden" id="et_count" value="<?php echo $et_count+1; ?>" /> -->
						<?php 
							if( $is_edit == true ){
								?>
								<input type="hidden" id="et_count" value="<?php echo $et_count; ?>" />
								<?php 
							}else{
								?>
								<input type="hidden" id="et_count" value="<?php echo $et_count+1; ?>" />
								<?php
							} 
						?>
						<a href="javascript:" class="add-exchange-trades button-primary dashicons-before dashicons-plus"> Add</a>
					</div>
				</div>
			</td>
		</tr>
	</table>

<?php
}


/*
 *  Save Custom Fields info to DB
 *
 */
function platform_info_save( $post_id ) {

	/* Bail if we're doing an auto save */
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( !isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)) ) {
		return $post_id;
	}
	if( !current_user_can( 'edit_post' ) ) return;

	/* Collect Data */
	$funds = json_encode($_POST[ 'funds' ]);
	
	$exchange_trades = json_encode($_POST['exchange_trades']);
	$method = $_POST[ 'platform_method' ];

	/* Insert Data into Table */
	global $wpdb;
	$table_name = $wpdb->prefix . 'fundscape_management';
	$platform_id = $wpdb->get_row( "SELECT * FROM " . $table_name . " WHERE platform_id = '" . $post_id . "'" );

	if ( !empty( $funds ) || !empty( $exchange_trades ) ) {

		if ( !empty( $platform_id ) ) {
			$wpdb->update( $table_name,
				array(
					'platform_funds' => $funds,
					'platform_exchange_trades' => $exchange_trades,
					'platform_method' => $method,
					'updated_at' => current_time("Y-m-d H:i:s")
				),
				array(
					'platform_id' => $post_id,
				)
			);
		} else {
			$wpdb->insert( $table_name,
				array(
					'platform_id' => $post_id,
					'platform_funds' => $funds,
					'platform_exchange_trades' => $exchange_trades,
					'platform_method' => $method,
					'created_at' => current_time("Y-m-d H:i:s"),
					'updated_at' => current_time("Y-m-d H:i:s")
				)
			);
		}

	}

}
add_action( 'save_post', 'platform_info_save' );

?>