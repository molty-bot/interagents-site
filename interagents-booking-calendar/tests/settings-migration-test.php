<?php

define( 'ABSPATH', __DIR__ . '/' );
require_once dirname( __DIR__ ) . '/includes/class-iabc-plugin.php';

$failures = 0;

function iabc_settings_assert( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures++;
		fwrite( STDERR, "FAIL: {$message}\n" );
		return;
	}
	fwrite( STDOUT, "PASS: {$message}\n" );
}

$saved = array_merge(
	IABC_Plugin::defaults(),
	array(
		'notification_email' => 'owner@example.com',
		'work_start'         => '09:00',
		'work_end'           => '16:00',
		'duration_min'       => 20,
		'step_min'           => 30,
		'notice_hours'       => 24,
		'horizon_days'       => 45,
		'weekdays'           => array( 2, 3, 4 ),
	)
);
$migrated = IABC_Plugin::migrate_settings( $saved, '1.2.1' );

iabc_settings_assert( '10:00' === $migrated['work_start'], 'migration sets 10:00 opening' );
iabc_settings_assert( '15:00' === $migrated['work_end'], 'migration sets 15:00 closing' );
iabc_settings_assert( 30 === $migrated['duration_min'], 'migration sets 30-minute meetings' );
iabc_settings_assert( 30 === $migrated['step_min'], 'migration keeps 30-minute starts' );
iabc_settings_assert( 0 === $migrated['notice_hours'], 'migration removes rolling-hour notice' );
iabc_settings_assert( 1 === $migrated['min_booking_days'], 'migration requires the next calendar day' );
iabc_settings_assert( 'owner@example.com' === $migrated['notification_email'], 'migration preserves notification email' );
iabc_settings_assert( 45 === $migrated['horizon_days'], 'migration preserves booking horizon' );
iabc_settings_assert( array( 2, 3, 4 ) === $migrated['weekdays'], 'migration preserves working days' );

$custom_current = array_merge(
	$migrated,
	array(
		'work_start'       => '11:00',
		'work_end'         => '17:00',
		'duration_min'     => 45,
		'min_booking_days' => 3,
	)
);
$unchanged = IABC_Plugin::migrate_settings( $custom_current, '1.3.0' );
iabc_settings_assert( $custom_current === $unchanged, 'current-version settings are not overwritten' );

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} test(s) failed.\n" );
	exit( 1 );
}

fwrite( STDOUT, "\nAll settings-migration tests passed.\n" );
