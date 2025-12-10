<?php

declare(strict_types=1);

namespace Metricool\Services;

use Metricool\Interfaces\MigrationInterface;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;

class MigrationService
{
    private EnvironmentConfig $env;
    private ?string $toVersion = null;
    private ?string $fromVersion = null;

    public function __construct(EnvironmentConfig $env)
    {
        $this->env = $env;
    }

    /**
     * Main method to run all migrations fitting for the given versions. This
     * method will determine automatically if we are upgrading or downgrading
     * and run the migrations accordingly. It also skips migrations that do not
     * fit the version range.
     *
     * @param string|null $fromVersion The version we are migrating from, falls
     * back to the {@see $fromVersion} property if null.
     * @param string|null $toVersion The version we are migrating to, falls
     * back to the {@see $toVersion} property if null.
     */
    public function runApplicableMigrations(?string $fromVersion = null, ?string $toVersion = null): void
    {
        $this->fromVersion = ($fromVersion ?? $this->fromVersion);
        $this->toVersion = ($toVersion ?? $this->toVersion);

        $migrations = $this->getAllMigrations();

        foreach ($migrations as $migration) {
            $this->run($migration);
        }

        $this->cleanup();
    }

    /**
     * Run a single migration. Silently skips the migration if it does not fit
     * the version range.
     */
    public function run(MigrationInterface $migration): void
    {
        if ($this->shouldRunMigration($migration) === false) {
            return;
        }

        if ($this->isUpgrading()) {
            $migration->up();
        }

        if ($this->isDowngrading()) {
            $migration->down();
        }
    }

    /**
     * Determine if a migration should run based on version comparison.
     *
     * When upgrading: run migration if version is between fromVersion and
     * toVersion or equal to toVersion. Makes sure up() is run when upgrading to
     * the exact version of the migration to apply all changes up to that
     * version.
     *
     * When downgrading: run migration if version is between toVersion and
     * fromVersion or equal to fromVersion. Makes sure down() is run when
     * downgrading from the exact version of the migration to revert any
     * changes done by up() from that version.
     *
     * @return bool True if migration should run
     */
    public function shouldRunMigration(MigrationInterface $migration): bool
    {
        if ($this->isUpgrading()) {
            return version_compare($migration->version(), $this->fromVersion, '>')
                && version_compare($migration->version(), $this->toVersion, '<=');
        }

        if ($this->isDowngrading()) {
            return version_compare($migration->version(), $this->fromVersion, '<=')
                && version_compare($migration->version(), $this->toVersion, '>');
        }

        return false;
    }

    /**
     * Get all migration files from the migrations directory, sorted by version.
     * @return array<int, MigrationInterface>
     */
    public function getAllMigrations(): array
    {
        $migrationsPath = $this->env->getString('plugin.migrations_path');
        if (!is_dir($migrationsPath)) {
            return [];
        }

        $files = glob($migrationsPath . '*.php');
        if ($files === false) {
            return [];
        }

        $migrations = [];
        foreach ($files as $file) {
            $migration = require $file;
            $migrations[] = $migration;
        }

        // Sort migrations by version, lowest to highest
        usort($migrations, function ($a, $b) {
            return version_compare($a->version(), $b->version());
        });

        return $migrations;
    }

    public function cleanup(): void
    {
        $this->fromVersion = null;
        $this->toVersion = null;
    }

    public function setFromVersion(string $version): void
    {
        $this->fromVersion = $version;
    }

    public function setToVersion(string $version): void
    {
        $this->toVersion = $version;
    }

    private function isDowngrading(): bool
    {
        if ($this->fromVersion === null || $this->toVersion === null) {
            throw new \RuntimeException('From and To versions must be set before checking downgrade status.');
        }

        return version_compare($this->toVersion, $this->fromVersion, '<');
    }

    private function isUpgrading(): bool
    {
        if ($this->fromVersion === null || $this->toVersion === null) {
            throw new \RuntimeException('From and To versions must be set before checking upgrade status.');
        }

        return version_compare($this->toVersion, $this->fromVersion, '>');
    }
}
