<?php

declare(strict_types=1);

namespace Metricool\Managers;

use Metricool\Bootstrap\App;

abstract class AbstractManager
{
    /**
     * Overwrite this property to true when the entries that the child Manager
     * registers should be added to the container registry. For details see:
     * {@see App::make}
     */
    protected bool $useRegistry = false;

    /**
     * Overwrite this property to true when the dependencies of the entries that
     * the  child Manager registers should be added to the container registry.
     * For details see: {@see App::make}
     */
    protected bool $useRegistryForDependencies = true;

    /**
     * Child class should return the type of classes it registers. In plural
     * form. The value is used to identify the type of classes parsed through
     * the hooks the Manager fires in {@see register}.
     */
    abstract protected function type(): string;

    /**
     * Child class should check if the given class can be registered. For
     * example by checking if it implements an interface to know the logic in
     * the {@see registerClass} method can be executed.
     */
    abstract public function isRegistrable(object $class): bool;

    /**
     * Logic to register the given class. If this method can be executed is
     * checked by the {@see isRegistrable} method.
     */
    abstract public function registerClass(object $class): void;

    /**
     * Method called before all classes given to the manager are registered. Can
     * be used by child classes to initiate functionality that should be called
     * before the registration of the classes.
     */
    protected function beforeRegister(): void
    {
    }

    /**
     * Method called after all classes given to the manager are registered. Can
     * be used by child classes to initiate functionality that should be called
     * after the registration of the classes.
     */
    protected function afterRegister(): void
    {
    }

    /**
     * Register the given class as long as the entries are registrable according
     * to the child managers. Classes are autowired. See {@see useRegistry} for
     * registration in the container.
     *
     * Example filters / hooks:
     *
     *       metricool_plugin_controllers / metricool_plugin_controllers_loaded
     *       metricool_plugin_endpoints / metricool_plugin_endpoints_loaded
     *       metricool_plugin_listeners / metricool_plugin_listeners_loaded
     *       metricool_plugin_features / metricool_plugin_features_loaded
     *       metricool_plugin_providers / metricool_plugin_providers_loaded
     *
     * @uses apply_filters metricool_plugin_{@see type} to filter classes
     * @uses do_action metricool_plugin_{@see type}_loaded after registration
     *
     * @throws \LogicException When a developer is doing it wrong.
     * @throws \ReflectionException When the controller cannot be loaded.
     */
    final public function register(array $classes): void
    {
        $this->beforeRegister();

        $classes = apply_filters('metricool_plugin_' . $this->type(), $classes);

        foreach ($classes as $fullyClassifiedName) {
            if (is_string($fullyClassifiedName) === false) {
                $type = gettype($fullyClassifiedName);
                throw new \LogicException(esc_html("Class must be a fully qualified name. Given type: $type"));
            }

            $class = App::getInstance()->make($fullyClassifiedName, $this->useRegistry, $this->useRegistryForDependencies);

            if ($this->isRegistrable($class) === false) {
                throw new \LogicException('Class is not registrable: ' . esc_html($fullyClassifiedName));
            }

            $this->registerClass($class);
        }

        $this->afterRegister();

        do_action('metricool_plugin_' . $this->type() . '_loaded');
    }
}
