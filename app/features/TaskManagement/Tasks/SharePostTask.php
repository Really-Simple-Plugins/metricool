<?php

namespace Metricool\Features\TaskManagement\Tasks;

class SharePostTask extends AbstractTask
{
    const IDENTIFIER = 'share_post';

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return __('Share a post to promote your site!', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => __('Share a post', 'metricool'),
            'link' => admin_url( 'edit.php'),
        ];
    }
}