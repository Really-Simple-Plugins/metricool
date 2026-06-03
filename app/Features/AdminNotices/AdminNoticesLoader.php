<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices;

use Metricool\Features\AbstractLoader;
use Metricool\Traits\HasAllowlistControl;

class AdminNoticesLoader extends AbstractLoader
{
    use HasAllowlistControl;

    /**
     * Feature enabled for every logged-in user
     */
    public function isEnabled(): bool
    {
        return is_user_logged_in();
    }


    /**
     * @inheritDoc
     */
    public function inScope(): bool
    {
        return true;
    }
}
