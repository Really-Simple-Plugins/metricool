<?php

declare(strict_types=1);

namespace Metricool\Features\AdminNotices;

use Metricool\Features\AbstractLoader;
use Metricool\Traits\HasAllowlistControl;

class AdminNoticesLoader extends AbstractLoader
{
    use HasAllowlistControl;

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        // Notifications can be shown to every logged-in user
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
