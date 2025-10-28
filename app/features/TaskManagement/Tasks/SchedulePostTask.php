<?php

namespace Metricool\Features\TaskManagement\Tasks;

class SchedulePostTask extends AbstractTask
{
    const IDENTIFIER = 'schedule_post';

    /**
     * We can check scheduled posts with:
     * https://app.metricool.com/api/v2/scheduler/posts?start=2025-10-28T14:00:00&end=2026-10-28T14:00:00
     * We don't call this endpoint and it is unknown if this endpoint is workable for this task.
     * todo: lets change this task to schedule a post from our Plugin instead?
     */

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