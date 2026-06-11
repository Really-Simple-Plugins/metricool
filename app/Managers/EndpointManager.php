<?php

declare(strict_types=1);

namespace Metricool\Managers;

use Metricool\Bootstrap\App;
use Metricool\Interfaces\MiddlewareInterface;
use Metricool\Interfaces\MultiEndpointInterface;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Support\Helpers\Storages\EnvironmentConfig;
use Metricool\Support\Helpers\Storages\MiddlewareConfig;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasNonces;

final class EndpointManager extends AbstractManager
{
    use HasNonces;
    use HasAllowlistControl;

    private EnvironmentConfig $env;

    private array $routes = [];
    private array $defaultMiddleware;
    private array $aliases;

    public function __construct(MiddlewareConfig $middleware, EnvironmentConfig $env)
    {
        $this->env = $env;
        $this->aliases = $middleware->get('aliases', []);
        $this->defaultMiddleware = $middleware->get('default_middleware', []);
    }

    /**
     * @inheritDoc
     */
    public function isRegistrable(object $class): bool
    {
        return ($class instanceof SingleEndpointInterface
            || $class instanceof MultiEndpointInterface
        );
    }

    /**
     * @inheritDoc
     */
    public function registerClass(object $class): void
    {
        if ($class instanceof SingleEndpointInterface) {
            $this->registerSingleEndpointRoute($class);
        }

        if ($class instanceof MultiEndpointInterface) {
            $this->registerMultiEndpointRoute($class);
        }
    }

    /**
     * @inheritDoc
     */
    public function afterRegister(): void
    {
        $this->registerWordPressRestRoutes();
        do_action('metricool_endpoints_loaded');
    }

    /**
     * Register a plugin route for and endpoint instance that implements the
     * {@see SingleEndpointInterface}
     */
    private function registerSingleEndpointRoute(SingleEndpointInterface $endpoint): void
    {
        if ($endpoint->enabled() === false) {
            return;
        }

        $this->routes[$endpoint->registerRoute()] = $endpoint->registerArguments();
    }

    /**
     * Register plugin routes for an endpoint instance that implements the
     * {@see MultiEndpointInterface}
     */
    private function registerMultiEndpointRoute(MultiEndpointInterface $endpoint): void
    {
        if ($endpoint->enabled() === false) {
            return;
        }

        $routeEndpoints = $endpoint->registerRoutes();
        foreach ($routeEndpoints as $route => $arguments) {
            $this->routes[$route] = $arguments;
        }
    }

    /**
     * This method provides a way to register custom REST routes via the
     * metricool_rest_routes filter. A controller or feature should be
     * instantiated before this manager is called and the controller should
     * hook into the metricool_rest_routes filter to add its own routes.
     *
     *      public function registerArguments(): array
     *      {
     *          return [
     *              'methods' => \WP_REST_Server::READABLE,
     *              'callback' => [$this, 'callback'],
     *              'permission_callback' => [$this, 'permissionCallback'],
     *              'middleware' => [
     *                  'user_can:administrator', // alias in config/middleware.php
     *                  ExampleMiddleware::class, // MiddlewareInterface class
     *              ],
     *              'apply_default_middleware' => true, // optional, default is true
     *              'version' => 'v1', // optional, default is the value config/env.php
     *              'args' => [], // optional, args passed to register_rest_route
     *          ];
     *      }
     *
     * @uses apply_filters metricool_rest_routes
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function registerWordPressRestRoutes(): void
    {
        $routes = $this->getPluginRestRoutes();

        foreach ($routes as $route => $data) {
            $methods = ($data['methods'] ?? 'GET');
            $callback = ($data['callback'] ?? null);
            $permissionCallback = ($data['permission_callback'] ?? '__return_true');
            $middleware = ($data['middleware'] ?? []);
            $applyDefaultMiddleware = ($data['apply_default_middleware'] ?? true);
            $version = ($data['version'] ?? $this->env->getString('http.version'));
            $args = ($data['args'] ?? null);

            if (!is_callable($callback)) {
                throw new \InvalidArgumentException(
                    esc_html(sprintf('The callback for the route: %s is not callable.', $route))
                );
            }

            if ($applyDefaultMiddleware === true) {
                $middleware = array_merge($this->defaultMiddleware, $middleware);
            }

            $arguments = [
                'methods' => $this->normalizeMethods($methods),
                'callback' => $this->callbackMiddleware($callback, $middleware),
                'permission_callback' => $permissionCallback,
            ];

            if ($args !== null) {
                $arguments['args'] = $args;
            }

            register_rest_route($this->env->getString('http.namespace') . '/' . $version, $route, $arguments);
        }
    }

    /**
     * Get the plugins REST routes
     * @uses apply_filters metricool_rest_routes
     */
    private function getPluginRestRoutes(): array
    {
        /**
         * Filter: metricool_rest_routes
         * Can be used to add or modify the REST routes
         *
         * @param array $routes
         * @return array
         * @example [
         *      'route' => [ // key is the route name
         *          'methods' => 'GET', // required
         *          'callback' => 'callback_function', // required
         *          'permission_callback' => 'permission_callback_function', // optional to override the default permission callback
         *          'version' => 'v1' // optional to override the default version
         *      ]
         * ]
         */
        return apply_filters('metricool_rest_routes', $this->routes);
    }

    /**
     * Wrap the endpoint callback with the default middleware and any named middleware from the pipeline.
     * Each entry can be either a registered alias (e.g. 'auth:metricool') or a fully qualified class name (e.g. MetricoolAuthenticated::class).
     * @throws \ReflectionException
     */
    public function callbackMiddleware(callable $callback, array $middlewares = []): callable
    {
        $middlewareInstances = $this->resolveMiddleware($middlewares);

        return static function (\WP_REST_Request $request) use ($callback, $middlewareInstances) {
            try {
                foreach ($middlewareInstances as $middleware) {
                    $response = $middleware->handle($request);
                    if ($response instanceof \WP_REST_Response) {
                        return $response;
                    }
                }
            } catch (\Exception $e) {
                return new \WP_REST_Response(['message' => $e->getMessage()], 500);
            }

            return $callback($request);
        };
    }

    /**
     * Resolve middleware entries to MiddlewareInterface instances.
     *
     * @param string[] $middleware
     * @return MiddlewareInterface[]
     * @throws \ReflectionException
     */
    private function resolveMiddleware(array $middleware): array
    {
        $resolved = [];

        foreach ($middleware as $entry) {
            $class = $this->aliases[$entry] ?? $entry;

            if (!is_string($class) || !class_exists($class)) {
                throw new \InvalidArgumentException(
                    esc_html(sprintf("Middleware: %s could not be resolved to a valid class.", $entry))
                );
            }

            $instance = App::getInstance()->get($class);

            if (!$instance instanceof MiddlewareInterface) {
                throw new \InvalidArgumentException(
                    esc_html(sprintf('Middleware: %s must implement MiddlewareInterface.', $entry))
                );
            }

            $resolved[] = $instance;
        }

        return $resolved;
    }

    /**
     * Process the given methods and compare them to the allowed
     * {@see \WP_REST_Server::ALLMETHODS} methods. Remove unwanted entries and
     * cleanup method usage from, for example, "get " to "GET".
     *
     * @return string From "get, POSt, fake" to "GET,POST"
     */
    private function normalizeMethods(string $methods): string
    {
        // Split into array, trim whitespace and uppercase entries
        $methodsArray = array_map('trim', explode(',', $methods));
        $methodsArray = array_map('strtoupper', $methodsArray);

        // Split allowed entries into array and trim whitespaces
        $allowedMethodsArray = array_map('trim', explode(',', \WP_REST_Server::ALLMETHODS));

        // Keep only allowed methods
        $methodsArray = array_intersect($methodsArray, $allowedMethodsArray);
        $methodsArray = array_values(array_unique($methodsArray));

        // Convert back to CSV format for register_rest_route usage
        return implode(',', $methodsArray);
    }
}
