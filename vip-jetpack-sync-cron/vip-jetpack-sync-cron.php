<?php 

/*
Plugin Name: VIP Jetpack Sync Cron
Description: This drop-in plugin ensures that Jetpack only syncs on the dedicated cron task.
Version: 2.0
Author: Rebecca Hum, Automattic 
*/

class VIP_Jetpack_Sync_Cron {

	const SYNC_INTERVAL_NAME = 'vip_jp_sync_cron_interval';

	/**
	 * __construct()
	 * 
	 * @return void
	 */
	function __construct() {
		if ( ! class_exists( 'Jetpack' ) ) { // Bail if no Jetpack.
			return;
		}

		if ( ! Jetpack_Sync_Actions::sync_via_cron_allowed() ) { // Bail if no syncing via cron allowed.
			return;
		}
		
		add_filter( 'cron_schedules', [ $this, 'jp_sync_cron_schedule_interval' ] );
		add_filter( 'jetpack_sync_incremental_sync_interval', [ $this, 'filter_jetpack_sync_interval' ], 999 );
		add_filter( 'jetpack_sync_full_sync_interval', [ $this, 'filter_jetpack_sync_interval' ], 999 );
		add_filter( 'jetpack_sync_sender_should_load', [ 'Jetpack_Sync_Settings', 'is_doing_cron' ], 999 ); // Short circuit loading of Jetpack sender to sync only on cron.
	}

	/**
	 * Filter to add custom interval to schedule.
	 * 
	 * @param array  $schedules
	 */
	function jp_sync_cron_schedule_interval( $schedules ) {
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

$vip_jetpack_sync_cron = new VIP_Jetpack_Sync_Cron();
