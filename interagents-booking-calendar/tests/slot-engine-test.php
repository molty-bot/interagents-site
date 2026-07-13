<?php

require_once dirname( __DIR__ ) . '/includes/class-iabc-slot-engine.php';

$timezone = new DateTimeZone( 'Europe/Warsaw' );
$settings = array(
	'timezone'     => 'Europe/Warsaw',
	'work_start'   => '10:00',
	'work_end'     => '15:00',
	'duration_min' => 30,
	'step_min'     => 30,
	'notice_hours' => 0,
	'min_booking_days' => 1,
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
iabc_assert( 10 === count( $slots ), 'weekday has ten 30-minute meetings' );
iabc_assert( '10:00–10:30' === $slots[0]['label'], 'first meeting is 10:00–10:30' );
iabc_assert( '14:30–15:00' === $slots[9]['label'], 'last meeting ends at 15:00' );
iabc_assert( ! in_array( '15:00–15:30', array_column( $slots, 'label' ), true ), '15:00 is never a start time' );

$weekend = IABC_Slot_Engine::generate( new DateTimeImmutable( '2026-07-12', $timezone ), $now, $settings );
iabc_assert( 0 === count( $weekend ), 'weekends produce no slots' );

$same_day = IABC_Slot_Engine::generate(
	new DateTimeImmutable( '2026-07-13 00:00:00', $timezone ),
	new DateTimeImmutable( '2026-07-13 00:01:00', $timezone ),
	$settings
);
iabc_assert( 0 === count( $same_day ), 'same-day booking is unavailable' );

$next_day = IABC_Slot_Engine::generate(
	new DateTimeImmutable( '2026-07-14 00:00:00', $timezone ),
	new DateTimeImmutable( '2026-07-13 23:59:00', $timezone ),
	$settings
);
iabc_assert( 10 === count( $next_day ), 'the complete next-day schedule is offered' );
iabc_assert( true === $next_day[0]['available'], 'the first next-day meeting remains available under 24 clock-hours' );

$too_far = IABC_Slot_Engine::generate( $now->setTime( 0, 0 )->modify( '+61 days' ), $now, $settings );
iabc_assert( 0 === count( $too_far ), 'day 61 is outside the horizon' );

$dst_slots = IABC_Slot_Engine::generate(
	new DateTimeImmutable( '2026-03-30 00:00:00', $timezone ),
	new DateTimeImmutable( '2026-03-27 08:00:00', $timezone ),
	$settings
);
iabc_assert( 10 === count( $dst_slots ), 'DST transition week keeps local opening hours' );
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
