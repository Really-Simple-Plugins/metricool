<?php namespace Metricool\Managers;

use Metricool\Interfaces\ControllerInterface;

final class ControllerManager
{
    /**
     * Register a single controller as long as it implements the
     * ControllerInterface.
     * @uses do_action metricool_controllers_loaded
     */
    public function registerControllers(array $controllers)
    {
        // Serve each controller when it implements ControllerInterface
        foreach ($controllers as $controller) {
            if ($controller instanceof ControllerInterface) {
                $controller->register();
            }
        }

        do_action('metricool_controllers_loaded');
    }
}