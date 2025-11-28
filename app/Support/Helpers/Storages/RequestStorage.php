<?php

declare(strict_types=1);

namespace Metricool\Support\Helpers\Storages;

use Metricool\Support\Helpers\Storage;

/**
 * General config helper used in DI container.
 */
final class RequestStorage extends Storage
{
    public function __construct()
    {
        parent::__construct([
            'global' => $_REQUEST,
            'files' => $_FILES,
        ]);
    }
}
