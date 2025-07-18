<?php

namespace Metricool\Features\TaskManagement\Tasks;

class SchedulePostTask extends AbstractTask
{
    const IDENTIFIER = 'schedule_post';

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('Schedule a post to promote your site!', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [
            'text' => esc_html__('Schedule a post', 'metricool'),
            'link' => admin_url('edit.php?metricool_action=' . self::IDENTIFIER),
        ];
    }
}