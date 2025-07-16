<?php

namespace Metricool\Helpers;

/**
 * Event class to handle Metricool events. Useful for dispatching events and
 * catching them in different parts of the application based on the constants.
 * @see \Metricool\Features\TaskManagement\TaskManagementListener
 * @internal This could be an ENUM when supported.
 */
class Event
{
    /**
     * Event names
     */
    const EXAMPLE_EVENT = 'example_event';

    /**
     * Execute a WordPress event based on our constants.
     */
    public static function dispatch(string $event, array $arguments = []): void
    {
        self::validate($event);
        do_action('metricool_event_' . $event, $arguments);
    }

    /**
     * Check if the given event matches the specified event.
     */
    public static function matches(string $event, string $eventToCheck): bool
    {
        self::validate($event);
        self::validate($eventToCheck);

        return $event === $eventToCheck;
    }

    /**
     * Validate a given event name based on our constants.
     * @throws \InvalidArgumentException
     */
    private static function validate(string $event): void
    {
        if (!defined('self::' . strtoupper($event))) {
            throw new \InvalidArgumentException(sprintf('Invalid event name: %s', esc_html($event)));
        }
    }
}