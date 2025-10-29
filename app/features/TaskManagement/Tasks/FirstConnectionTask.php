<?php

namespace Metricool\Features\TaskManagement\Tasks;

// todo - add listener
class FirstConnectionTask extends AbstractTask
{
    const IDENTIFIER = 'first_connection';

    protected bool $required = false;
    

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return esc_html__('Connect your first account to Metricool', 'metricool');
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