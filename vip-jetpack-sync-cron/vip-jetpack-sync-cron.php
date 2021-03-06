<?php

/*
Plugin Name: VIP Jetpack Sync Cron
Description: This plugin ensures that Jetpack only syncs on the dedicated cron task.
Version: 2.0
Author: Rebecca Hum, Automattic
*/

use Automattic\Jetpack\Sync\Actions;
use Automattic\Jetpack\Sync\Settings;

if ( class_exists( 'VIP_Jetpack_Sync_Cron' ) ) {
	return;
}

class VIP_Jetpack_Sync_Cron {

	const SYNC_INTERVAL_NAME = 'vip_jp_sync_cron_interval';

	/**
	 * Initiate an instance of this class if one doesn't exist already.
	 *
	 * @return VIP_Jetpack_Sync_Cron instance
	 */
	static public function init() {
		if ( ! class_exists( 'Jetpack' ) ) { // Bail if no Jetpack.
			return;
		}

		if ( ! Actions::sync_via_cron_allowed() ) { // Bail if no syncing via cron allowed.
			return;
		}

		static $instance = false;

		if ( ! $instance ) {
			$instance = new VIP_Jetpack_Sync_Cron;
		}

		return $instance;
	}

	/**
	 * Class constructor for hooking actions/filters.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'cron_schedules', [ $this, 'jp_sync_cron_schedule_interval' ] );
		add_filter( 'jetpack_sync_incremental_sync_interval', [ $this, 'filter_jetpack_sync_interval' ], 999 );
		add_filter( 'jetpack_sync_full_sync_interval', [ $this, 'filter_jetpack_sync_interval' ], 999 );
		add_filter( 'jetpack_sync_sender_should_load', [ Settings::class, 'is_doing_cron' ], 999 ); // Short circuit loading of Jetpack sender to sync only on cron.
	}

	/**
	 * Filter to add custom interval to schedule.
	 *
	 * @param array  $schedules
	 */
	public function jp_sync_cron_schedule_interval( $schedules ) {
		$schedules[ self::SYNC_INTERVAL_NAME ] = [
		    'interval' => 60,
		    'display'  => esc_html__( 'Every minute' ),
		];

		return $schedules;
	}

	/**
	 * Filter to return custom cron interval name.
	 *
	 * @param string  $incremental_sync_cron_schedule
	 */
	public function filter_jetpack_sync_interval() {
		return self::SYNC_INTERVAL_NAME;
	}
}

add_action( 'after_setup_theme', [ 'VIP_Jetpack_Sync_Cron', 'init' ] );
