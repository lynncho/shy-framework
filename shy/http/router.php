<?php

/**
 * Shy Framework Router
 *
 * @author    lynn<admin@lynncho.cn>
 * @link      http://lynncho.cn/
 */

namespace shy\http;

use shy\http\exception\httpException;

class router
{
    /**
     * Controller
     *
     * @var string
     */
    protected $controller;

    /**
     * Method of Controller
     *
     * @var string
     */
    protected $method;

    /**
     * Is Parse Route Success
     *
     * @var bool
     */
    protected $success;

    /**
     * Base Url Path
     *
     * @var string
     */
    protected $uri;

    /**
     * Middleware
     *
     * @var array
     */
    protected $middleware;

    /**
     * Init Router
     */
    protected function init()
    {
        $this->controller = 'home';
        $this->method = 'index';
        $this->success = false;
        $this->middleware = [];
    }

    /**
     * Pipeline Handle
     *
     * @param \Closure $next
     * @param \shy\http\request $request
     * @return string|view
     */
    public function handle($next, $request)
    {
        $this->init();
        $this->uri = $request->getUri();

        if (!is_string($this->uri)) {
            throw new httpException(404, 'Route not found 404');
        }

        /**
         * Parse Url
         */
        if (config_key('route_by_config')) {
            $this->success = $this->configRoute();
        }
        if (!$this->success && config_key('route_by_path')) {
            $this->success = $this->pathRoute();
        }
        if ($this->success) {
            $this->controller = 'app\\http\\controller\\' . $this->controller;
            if (!class_exists($this->controller) || !method_exists($this->controller, $this->method)) {
                throw new httpException(404, 'Route not found 404');
            }
        } else {
            throw new httpException(404, 'Route not found 404');
        }

        /**
         * Run controller and middleware
         */
        if (empty($this->middleware)) {
            $response = $this->runController();
        } else {
            foreach ($this->middleware as $key => $middleware) {
                if (class_exists($namespaceMiddleware = 'app\\http\middleware\\' . $middleware)) {
                    $this->middleware[$key] = $namespaceMiddleware;
                } elseif (class_exists($namespaceMiddleware = 'shy\\http\\middleware\\' . $middleware)) {
                    $this->middleware[$key] = $namespaceMiddleware;
                }
            }
            $response = shy('pipeline')
                ->through($this->middleware)
                ->then(function () {
                    return $this->runController();
                });

            shy_clear(array_values($this->middleware));
        }

        shy_clear($this->controller);

        return $next($response);
    }

    /**
     * Get Controller Name
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get Method Name
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Run controller
     *
     * @return mixed
     */
    protected function runController()
    {
        return shy('pipeline')
            ->through($this->controller)
            ->via($this->method)
            ->run();
    }

    /**
     * Route by Config
     *
     * @return bool
     */
    protected function configRoute()
    {
        $fasterRouter = json_decode(@file_get_contents(CACHE_PATH . 'app/router'), true);
        if (config_key('env') === 'development' || empty($fasterRouter) || !is_array($fasterRouter)) {
            /**
             * build faster router
             */
            $fasterRouter = [];
            $router = config('router');
            /**
             * path router
             */
            if (isset($router['path'])) {
                foreach ($router['path'] as $path => $handle) {
                    $fasterRouter[$path] = ['handle' => $handle];
                }
            }
            /**
             * group router
             */
            if (isset($router['group'])) {
                foreach ($router['group'] as $group) {
                    if (isset($group['path']) && is_array($group['path'])) {
                        $groupPath = $group['path'];
                        unset($group['path']);

                        /**
                         * prefix
                         */
                        $prefix = '';
                        if (isset($group['prefix']) && !empty($group['prefix'])) {
                            $prefix = '/' . $group['prefix'];
                        }

                        foreach ($groupPath as $path => $handle) {
                            $fasterRouter[$prefix . $path] = array_merge($group, ['handle' => $handle]);
                        }
                    }
                }
            }
            file_put_contents(CACHE_PATH . 'app/router', json_encode($fasterRouter));
        }

        /**
         * parse router
         */
        if (isset($fasterRouter[$this->uri])) {
            $handle = $fasterRouter[$this->uri]['handle'];
            if (isset($fasterRouter[$this->uri]['middleware'])) {
                $this->middleware = $fasterRouter[$this->uri]['middleware'];
            }

            list($this->controller, $this->method) = explode('@', $handle);

            return true;
        }
    }

    /**
     * Route by Url Path
     *
     * @return bool
     */
    protected function pathRoute()
    {
        $path = explode('/', $this->uri);
        if (!empty($path[1])) {
            $this->controller = lcfirst($path[1]);
            if (isset($path[2]) && !empty($path[2])) {
                $this->method = lcfirst($path[2]);

                return true;
            }
        } elseif (count($path) === 2) {
            //home page
            if ($defaultController = config_key('default_controller')) {
                $this->controller = $defaultController;
            }

            return true;
        }
    }

}
