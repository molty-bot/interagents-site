<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IABC_Admin {
	/** @return void */
	public function init() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_iabc_cancel_booking', array( $this, 'cancel_booking' ) );
	}

	/** @return void */
	public function menu() {
		add_menu_page(
			__( 'interagents bookings', 'interagents-booking-calendar' ),
			__( 'interagents bookings', 'interagents-booking-calendar' ),
			'manage_options',
			'interagents-bookings',
			array( $this, 'page' ),
			'dashicons-calendar-alt',
			26
		);
	}

	/** @return void */
	public function register_settings() {
		register_setting(
			'iabc_settings_group',
			IABC_Plugin::OPTION_SETTINGS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'IABC_Plugin', 'sanitize_settings' ),
				'default'           => IABC_Plugin::defaults(),
			)
		);
	}

	/** @return void */
	public function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = IABC_Plugin::settings();
		$paged    = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
		$per_page = 50;
		$total    = IABC_Bookings::count_bookings();
		$rows     = IABC_Bookings::list_bookings( $per_page, ( $paged - 1 ) * $per_page );
		$days     = array(
			1 => __( 'Monday', 'interagents-booking-calendar' ),
			2 => __( 'Tuesday', 'interagents-booking-calendar' ),
			3 => __( 'Wednesday', 'interagents-booking-calendar' ),
			4 => __( 'Thursday', 'interagents-booking-calendar' ),
			5 => __( 'Friday', 'interagents-booking-calendar' ),
			6 => __( 'Saturday', 'interagents-booking-calendar' ),
			7 => __( 'Sunday', 'interagents-booking-calendar' ),
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'interagents bookings', 'interagents-booking-calendar' ); ?></h1>
			<?php settings_errors(); ?>
			<?php if ( isset( $_GET['iabc_cancelled'] ) ) : ?>
				<div class="notice notice-warning is-dismissible"><p><?php esc_html_e( 'Booking cancelled and the time is available again. The customer was not emailed; notify them manually.', 'interagents-booking-calendar' ); ?></p></div>
			<?php endif; ?>

			<div class="card" style="max-width:900px;padding:20px;margin-top:18px">
				<h2 style="margin-top:0"><?php esc_html_e( 'Calendar settings', 'interagents-booking-calendar' ); ?></h2>
				<p><?php esc_html_e( 'Place this shortcode on any page:', 'interagents-booking-calendar' ); ?> <code>[interagents_booking_calendar]</code></p>
				<form action="options.php" method="post">
					<?php settings_fields( 'iabc_settings_group' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="iabc-notification-email"><?php esc_html_e( 'Notification email', 'interagents-booking-calendar' ); ?></label></th>
							<td><input class="regular-text" id="iabc-notification-email" name="iabc_settings[notification_email]" type="email" value="<?php echo esc_attr( $settings['notification_email'] ); ?>" required></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Working days', 'interagents-booking-calendar' ); ?></th>
							<td>
								<?php foreach ( $days as $number => $label ) : ?>
									<label style="display:inline-block;margin:0 14px 8px 0"><input name="iabc_settings[weekdays][]" type="checkbox" value="<?php echo esc_attr( $number ); ?>" <?php checked( in_array( $number, $settings['weekdays'], true ) ); ?>> <?php echo esc_html( $label ); ?></label>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Working hours', 'interagents-booking-calendar' ); ?></th>
							<td>
								<label><?php esc_html_e( 'From', 'interagents-booking-calendar' ); ?> <input name="iabc_settings[work_start]" type="time" value="<?php echo esc_attr( $settings['work_start'] ); ?>" required></label>
								&nbsp;&nbsp;
								<label><?php esc_html_e( 'To', 'interagents-booking-calendar' ); ?> <input name="iabc_settings[work_end]" type="time" value="<?php echo esc_attr( $settings['work_end'] ); ?>" required></label>
								<p class="description"><?php esc_html_e( 'Timezone is fixed to Europe/Warsaw. A meeting must end by the closing time.', 'interagents-booking-calendar' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="iabc-duration"><?php esc_html_e( 'Meeting duration', 'interagents-booking-calendar' ); ?></label></th>
							<td><input id="iabc-duration" name="iabc_settings[duration_min]" type="number" min="5" max="240" step="5" value="<?php echo esc_attr( $settings['duration_min'] ); ?>"> <?php esc_html_e( 'minutes', 'interagents-booking-calendar' ); ?></td>
						</tr>
						<tr>
							<th scope="row"><label for="iabc-step"><?php esc_html_e( 'Start-time interval', 'interagents-booking-calendar' ); ?></label></th>
							<td><input id="iabc-step" name="iabc_settings[step_min]" type="number" min="5" max="120" step="5" value="<?php echo esc_attr( $settings['step_min'] ); ?>"> <?php esc_html_e( 'minutes', 'interagents-booking-calendar' ); ?></td>
						</tr>
						<tr>
							<th scope="row"><label for="iabc-min-booking-days"><?php esc_html_e( 'Minimum booking distance', 'interagents-booking-calendar' ); ?></label></th>
							<td><input id="iabc-min-booking-days" name="iabc_settings[min_booking_days]" type="number" min="0" max="30" value="<?php echo esc_attr( $settings['min_booking_days'] ); ?>"> <?php esc_html_e( 'calendar days', 'interagents-booking-calendar' ); ?></td>
						</tr>
						<tr>
							<th scope="row"><label for="iabc-horizon"><?php esc_html_e( 'Booking horizon', 'interagents-booking-calendar' ); ?></label></th>
							<td><input id="iabc-horizon" name="iabc_settings[horizon_days]" type="number" min="1" max="365" value="<?php echo esc_attr( $settings['horizon_days'] ); ?>"> <?php esc_html_e( 'days', 'interagents-booking-calendar' ); ?></td>
						</tr>
					</table>
					<?php submit_button( __( 'Save calendar settings', 'interagents-booking-calendar' ) ); ?>
				</form>
			</div>

			<h2 style="margin-top:32px"><?php esc_html_e( 'Bookings', 'interagents-booking-calendar' ); ?> <span class="count">(<?php echo esc_html( number_format_i18n( $total ) ); ?>)</span></h2>
			<div style="overflow:auto">
			<table class="widefat striped" style="min-width:1100px">
				<thead><tr>
					<th><?php esc_html_e( 'Meeting (Europe/Warsaw)', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Status', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Name', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Business email', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Company / phone', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Message', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Language', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Emails', 'interagents-booking-calendar' ); ?></th>
					<th><?php esc_html_e( 'Action', 'interagents-booking-calendar' ); ?></th>
				</tr></thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="9"><?php esc_html_e( 'No bookings yet.', 'interagents-booking-calendar' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<?php
						$start = ( new DateTimeImmutable( $row['start_utc'], new DateTimeZone( 'UTC' ) ) )->setTimezone( new DateTimeZone( 'Europe/Warsaw' ) );
						$end   = ( new DateTimeImmutable( $row['end_utc'], new DateTimeZone( 'UTC' ) ) )->setTimezone( new DateTimeZone( 'Europe/Warsaw' ) );
						?>
						<tr>
							<td><strong><?php echo esc_html( $start->format( 'Y-m-d H:i' ) . '–' . $end->format( 'H:i' ) ); ?></strong><br><small>#<?php echo esc_html( $row['id'] ); ?> · <?php echo esc_html( $row['created_at_utc'] ); ?> UTC</small></td>
							<td><?php echo esc_html( ucfirst( $row['status'] ) ); ?></td>
							<td><?php echo esc_html( $row['customer_name'] ); ?></td>
							<td><a href="mailto:<?php echo esc_attr( $row['customer_email'] ); ?>"><?php echo esc_html( $row['customer_email'] ); ?></a></td>
							<td><?php echo esc_html( $row['company'] ); ?><br><small><?php echo esc_html( $row['phone'] ); ?></small></td>
							<td style="max-width:280px;white-space:normal"><?php echo esc_html( $row['workflow_bottleneck'] ); ?></td>
							<td><?php echo esc_html( strtoupper( $row['lang'] ) ); ?></td>
							<td><?php echo esc_html( $row['customer_email_sent'] ? 'Customer ✓' : 'Customer ✕' ); ?><br><?php echo esc_html( $row['admin_email_sent'] ? 'Admin ✓' : 'Admin ✕' ); ?></td>
							<td>
								<?php if ( 'confirmed' === $row['status'] ) : ?>
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Cancel this booking and reopen the slot? You must notify the customer manually.', 'interagents-booking-calendar' ) ); ?>');">
										<input type="hidden" name="action" value="iabc_cancel_booking">
										<input type="hidden" name="booking_id" value="<?php echo esc_attr( $row['id'] ); ?>">
										<?php wp_nonce_field( 'iabc_cancel_' . $row['id'] ); ?>
										<button class="button button-small" type="submit"><?php esc_html_e( 'Cancel', 'interagents-booking-calendar' ); ?></button>
									</form>
								<?php else : ?>—<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			</div>
			<?php
			$total_pages = (int) ceil( $total / $per_page );
			if ( $total_pages > 1 ) {
				echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(
					paginate_links(
						array(
							'base'    => add_query_arg( 'paged', '%#%' ),
							'format'  => '',
							'current' => $paged,
							'total'   => $total_pages,
						)
					)
				) . '</div></div>';
			}
			?>
		</div>
		<?php
	}

	/** @return void */
	public function cancel_booking() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do that.', 'interagents-booking-calendar' ), '', array( 'response' => 403 ) );
		}
		$id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
		check_admin_referer( 'iabc_cancel_' . $id );
		IABC_Bookings::cancel( $id );
		wp_safe_redirect( add_query_arg( 'iabc_cancelled', '1', admin_url( 'admin.php?page=interagents-bookings' ) ) );
		exit;
	}
}
