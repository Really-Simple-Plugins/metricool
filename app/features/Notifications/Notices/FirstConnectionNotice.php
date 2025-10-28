<?php

namespace Metricool\Features\Notifications\Notices;


class FirstConnectionNotice extends AbstractNotice
{
    const IDENTIFIER = 'first_connection';
    protected bool $active = true;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return esc_html__('No connections detected', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('Connect your first social account to Metricool to start scheduling and tracking your content.', 'metricool');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE_INFO;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(): string
    {
        return 'connections';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        // todo - fetch from settings
        $queryArgs = array_filter([
            'blogId' => (defined('METRICOOL_BLOG_ID') ? METRICOOL_BLOG_ID : ''),
            'userId' => (defined('METRICOOL_USER_ID') ? METRICOOL_USER_ID : ''),
        ]);

        $link = add_query_arg($queryArgs, 'https://app.metricool.com/evolution/brandSummary');

        return [
            'text' => esc_html__('Connect', 'metricool'),
            'link' => $link,
            'target' => '_blank',
        ];
    }
}