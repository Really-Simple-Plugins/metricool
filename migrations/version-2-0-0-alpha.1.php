<?php

declare(strict_types=1);

use Metricool\Interfaces\MigrationInterface;

return new class() implements MigrationInterface
{
    public function version(): string
    {
        return '2.0.0-alpha.1'; // first version in the 2.x series
    }

    /**
     * Perform schema/data changes required for this migration.
     * @uses add_option to avoid changing existing values.
     */
    public function up(): void
    {
        if (!function_exists('get_option')) {
            require_once ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'option.php';
        }

        $profileId = trim(get_option('metricool_profile_id', ''));

        if (!empty($profileId)) {
            add_option('metricool_tracking_script_hash', $profileId, '', false);
            add_option('metricool_tracking_script_active', true, '', false);
        }

        delete_option('metricool_profile_id');
    }

    /**
     * Revert the migration. Undo the changes made in {@see up()}.
     * @uses add_option to avoid changing existing values.
     */
    public function down(): void
    {
        if (!function_exists('get_option')) {
            require_once ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'option.php';
        }

        $trackingHash = trim(get_option('metricool_tracking_script_hash', ''));

        if (!empty($trackingHash)) {
            add_option('metricool_profile_id', $trackingHash, '', false);
        }

        delete_option('metricool_tracking_script_hash');
        delete_option('metricool_tracking_script_active');
    }
};
