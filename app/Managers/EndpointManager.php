<?php

declare(strict_types=1);

namespace Metricool\Managers;

use Carbon\Carbon;
use Metricool\Bootstrap\App;
use Metricool\Http\Middleware\MiddlewareInterface;
use Metricool\Interfaces\MultiEndpointInterface;
use Metricool\Interfaces\SingleEndpointInterface;
use Metricool\Traits\HasAllowlistControl;
use Metricool\Traits\HasNonces;

final class EndpointManager extends AbstractManager
{
    use HasNonces;
    use HasAllowlistControl;

    private array $routes = [];

    /** @var array<string, class-string<MiddlewareInterface>> */
    private array $middlewareAliases = [];

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
        $this->loadMiddlewareAliases();
        $this->registerWordPressRestRoutes();
        do_action('metricool_endpoints_loaded');
    }

    /**
     * Load middleware aliases from the config file.
     */
    private function loadMiddlewareAliases(): void
    {
        $configPath = $this->env->getString('plugin.path') . '/config/middleware.php';

        if (file_exists($configPath)) {
            $this->middlewareAliases = require $configPath;
        }
    }

    /**
     * Resolve middleware alias strings to MiddlewareInterface instances.
     *
     * @param string[] $middlewareNames
     * @return MiddlewareInterface[]
     */
    private function resolveMiddleware(array $middlewareNames): array
    {
        $resolved = [];

        foreach ($middlewareNames as $alias) {
            if (!isset($this->middlewareAliases[$alias])) {
                throw new \InvalidArgumentException(
                    esc_html(sprintf("Middleware alias '%s' is not registered.", $alias))
                );
            }

            $instance = App::getInstance()->make($this->middlewareAliases[$alias]);

            if (!$instance instanceof MiddlewareInterface) {
                throw new \InvalidArgumentException(
                    esc_html(sprintf("Middleware '%s' must implement MiddlewareInterface.", $alias))
                );
            }

            $resolved[] = $instance;
        }

        return $resolved;
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
     * metricool_rest_routes filter. A controller of feature should be
     * instantiated before this manager is called and the controller should
     * hook into the metricool_rest_routes filter to add its own routes.
     * @uses apply_filters metricool_rest_routes
     * @throws \InvalidArgumentException
     */
    public function registerWordPressRestRoutes(): void
    {
        $routes = $this->getPluginRestRoutes();

        foreach ($routes as $route => $data) {
            $version = ($data['version'] ?? $this->env->getString('http.version'));
            $callback = ($data['callback'] ?? null);
            $middleware = ($data['middleware'] ?? []);

            if (!is_callable($callback)) {
                throw new \InvalidArgumentException(
                    esc_html(sprintf('The callback for the route "%s" is not callable.', $route))
                );
            }

            $arguments = [
                'methods' => $this->normalizeMethods($data['methods'] ?? 'GET'),
                'callback' => $this->callbackMiddleware($callback, $middleware),
                'permission_callback' => ($data['permission_callback'] ?? [$this, 'defaultPermissionCallback']),
            ];

            if (!empty($data['args'])) {
                $arguments['args'] = $data['args'];
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
     * Wrap the endpoint callback with the default locale-switching middleware
     * and any named middleware from the pipeline.
     *
     * @param string[] $middlewareNames
     */
    public function callbackMiddleware(callable $callback, array $middlewareNames = []): callable
    {
        return function (\WP_REST_Request $request) use ($callback, $middlewareNames) {
            $this->defaultMiddlewareCallback();

            $middlewareInstances = $this->resolveMiddleware($middlewareNames);
            foreach ($middlewareInstances as $middleware) {
                $response = $middleware->handle($request);
                if ($response instanceof \WP_REST_Response) {
                    return $response;
                }
            }

            return $callback($request);
        };
    }

    /**
     * This method is used to switch the user locale to the current user locale.
     * This is important because we will otherwise show the default site
     * language to the user for the Tasks and Notifications. Those
     * translations are created in PHP and not in JS.
     */
    private function defaultMiddlewareCallback(): void
    {
        switch_to_user_locale(get_current_user_id());
        Carbon::setLocale(get_user_locale());
    }

    /**
     * The default permission callback, will check if the nonce is valid and if
     * the user has the required permissions to do a request.
     * @return bool|\WP_Error
     */
    public function defaultPermissionCallback(\WP_REST_Request $request)
    {
        $method = $request->get_method();
        $nonce = $request->get_param('nonce');

        // For methods that modify data, verify the nonce
        $methodsRequiringNonce = ['POST', 'PUT', 'PATCH', 'DELETE'];
        if (in_array($method, $methodsRequiringNonce) && ($this->verifyNonce($nonce) === false)) {
            return new \WP_Error(
                'rest_forbidden',
                __('Forbidden.', 'metricool'),
                ['status' => 403]
            );
        }

        return true;
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
