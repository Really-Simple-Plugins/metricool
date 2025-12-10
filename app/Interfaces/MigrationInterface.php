<?php

declare(strict_types=1);

namespace Metricool\Interfaces;

interface MigrationInterface
{
    /**
     * Version to run this migration for.
     */
    public function version(): string;

    /**
     * Perform schema/data changes required for this migration.
     */
    public function up(): void;

    /**
     * Revert the migration. Undo the changes made in {@see up()}.
     */
    public function down(): void;
}
