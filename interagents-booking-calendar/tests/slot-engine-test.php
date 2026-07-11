<?php

require_once dirname( __DIR__ ) . '/includes/class-iabc-slot-engine.php';

$timezone = new DateTimeZone( 'Europe/Warsaw' );
$settings = array(
	'timezone'     => 'Europe/Warsaw',
	'work_start'   => '10:00',
	'work_end'     => '16:00',
	'duration_min' => 20,
	'step_min'     => 30,
	'notice_hours' => 24,
	'horizon_days' => 60,
	'weekdays'     => array( 1, 2, 3, 4, 5 ),
);

$failures = 0;

function iabc_assert( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures++;
		fwrite( STDERR, "FAIL: {$message}\n" );
		return;
	}
	fwrite( STDOUT, "PASS: {$message}\n" );
}

$now   = new DateTimeImmutable( '2026-07-10 08:00:00', $timezone );
$day   = new DateTimeImmutable( '2026-07-13 00:00:00', $timezone );
$slots = IABC_Slot_Engine::generate( $day, $now, $settings );
iabc_assert( 12 === count( $slots ), 'weekday has twelve 30-minute start positions' );
iabc_assert( '10:00–10:20' === $slots[0]['label'], 'first meeting is 10:00–10:20' );
iabc_assert( '15:30–15:50' === $slots[11]['label'], 'last meeting ends before 16:00' );
iabc_assert( ! in_array( '16:00–16:20', array_column( $slots, 'label' ), true ), '16:00 is never a start time' );

$weekend = IABC_Slot_Engine::generate( new DateTimeImmutable( '2026-07-12', $timezone ), $now, $settings );
iabc_assert( 0 === count( $weekend ), 'weekends produce no slots' );

$notice_now   = new DateTimeImmutable( '2026-07-12 10:15:00', $timezone );
$notice_slots = IABC_Slot_Engine::generate( $day, $notice_now, $settings );
iabc_assert( false === $notice_slots[0]['available'], 'a slot less than 24 hours away is unavailable' );
iabc_assert( true === $notice_slots[1]['available'], 'the next slot beyond 24 hours remains available' );

$too_far = IABC_Slot_Engine::generate( $now->setTime( 0, 0 )->modify( '+61 days' ), $now, $settings );
iabc_assert( 0 === count( $too_far ), 'day 61 is outside the horizon' );

$dst_slots = IABC_Slot_Engine::generate(
	new DateTimeImmutable( '2026-03-30 00:00:00', $timezone ),
	new DateTimeImmutable( '2026-03-27 08:00:00', $timezone ),
	$settings
);
iabc_assert( 12 === count( $dst_slots ), 'DST transition week keeps local opening hours' );
iabc_assert( '+02:00' === substr( $dst_slots[0]['start'], -6 ), 'post-DST slots use Warsaw summer offset' );

$busy = array(
	array(
		'start_ts' => ( new DateTimeImmutable( '2026-07-13 10:10:00', $timezone ) )->getTimestamp(),
		'end_ts'   => ( new DateTimeImmutable( '2026-07-13 10:40:00', $timezone ) )->getTimestamp(),
	),
);
$busy_slots = IABC_Slot_Engine::generate( $day, $now, $settings, $busy );
iabc_assert( false === $busy_slots[0]['available'], 'overlap blocks the 10:00 meeting' );
iabc_assert( false === $busy_slots[1]['available'], 'overlap blocks the 10:30 meeting' );
iabc_assert( true === $busy_slots[2]['available'], 'non-overlapping 11:00 meeting stays available' );
iabc_assert( IABC_Slot_Engine::overlaps( 100, 120, 110, 130 ), 'overlap predicate catches intersections' );
iabc_assert( ! IABC_Slot_Engine::overlaps( 100, 120, 120, 140 ), 'touching endpoints do not overlap' );

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} test(s) failed.\n" );
	exit( 1 );
}

fwrite( STDOUT, "\nAll slot-engine tests passed.\n" );
