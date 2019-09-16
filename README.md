This has been moved to https://github.com/Automattic/vip-jetpack-sync-cron now and will no longer be monitored.

---

# VIP Jetpack Sync Cron
This plugin ensures that Jetpack only syncs on the dedicated cron task, as Jetpack Sync piggybacks on various cron/API requests causing slowdowns on the shutdown hook.
