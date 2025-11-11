<?php

namespace Metricool\Helpers;

class PostHelper
{
    /**
     * Returns all accessible post types. An accessible post type is a post type
     * that is public and isn't set as no-index.
     */
    public static function getPublicPostTypes(): array
    {
        $post_types = get_post_types(['public' => true]);
        return array_filter($post_types, 'is_post_type_viewable');
    }
}