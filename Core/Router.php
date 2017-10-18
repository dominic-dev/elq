<?php

namespace Elastique\Core;

class Router{
    private $routes;
    private $configuredParams;

    private static $patterns = [
        'int' => '\d',
        'str' => '[A-Za-z\-]+'
    ];

    public function __construct(){
        $json = file_get_contents(__DIR__ . '/../config/routes.json');
        $this->routes = json_decode($json, true);
        $this->configured_params = $this->getConfiguredParams();
    }

    /**
     * Take a request and map it to a route.
     *
     * @param request (Request) the request object.
     *
     * @return string The output to render.
     */
    
    public function route(Request $request): string{
        $path = $request->getPath();
        foreach ($this->routes as $route => $info){
            // Does route have paramaters?
            $route_params = isset($info['params']) ? $info['params'] : null;
            // Get rexeg pattern
            $pattern = $this->getPattern($route, $route_params);

            if (preg_match($pattern, $path)){
                // Extract params from uri.
                $uri_params = $this->extractParams($route, $path);
                return $this->executeController($info, $request, $uri_params);
            }
        }
    }

    /**
     * Extract the paramaters from URI.
     *
     * @param route (string) the route pattern from condig/routes.json
     * @param path (string) the URI
     *
     * @return array The paramaters as key => value pairs.
     */
    
    private function extractParams(string $route, string $path) : array {
        // Compare the matched route pattern to the URI
        // e.g. books/:id to /books/1
        // Remove trailing and leading slashes and split by remaining slashes.
        $route_params = explode('/', trim($route, '/'));
        $path_params = explode('/', trim($path, '/'));
        // Combine both arrays as key => value pairs.
        // And reduce the array to parameter matches
        // Parameter matches are sanitized.
        $potential_params = array_combine($route_params, $path_params);
        $sanitized_params = [];
        foreach ($potential_params as $name => $value){
            // Valid params start with a colon
            if (strpos($name, ':') === 0 ){
                $name = trim($name, ':');
                $sanitized_params[$name] = $this->sanitizeParam($name, $value);
            }
        }
        return $sanitized_params;
    }

    /**
     * Execute controller.
     *
     * @param request (Request) the request object.
     * @param uri_params (array) The paramaters extracted from URI.
     *
     * @return string The output to render.
     */
    
    private function executeController(array $info, Request $request, array $uri_params=null) : string {
        $controller_name = 'Elastique\App\Controllers\\' . $info['controller'] . 'Controller';
        $method = $info['method'];

        $controller = new $controller_name($request);
        return call_user_func_array([$controller, $method], $uri_params);
    }

    /**
     * Take a route from configuration and return a regex pattern.
     *
     * @param $route (string) the routing pattern as it appears in configuration.
     * @param $params (array) the params appearing in this pattern.
     *
     * @return string The regex pattern.
     */

    private function getPattern(string $route, array $route_params=null): string {
        // Escape slashes.
        $route = str_replace('/', '\/', $route);
        // Replace :params in route with regex pattern according to type.
        if (isset($route_params)){
            foreach ($route_params as $name => $type){
                $route = str_replace(':' . $name,
                            '(' . self::$patterns[$type] . ')',
                            $route);
            }
        }
        // Return regex pattern.
        return "/$route/";

    }

    /**
     * Take a paramater from URI and sanitize it's value.
     *
     * @param name (string) the name of the param
     * @param value (string) the value to sanitize
     *
     * @return mixed The sanitized parameter.
     */
    
    private function sanitizeParam( string $name, string $value){
        $type = $this->configured_params[$name];
        switch ($type){
            case 'int':
                return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'str':
                return filter_var($value, FILTER_SANITIZE_STRING);
                break;
        }
    }

    /**
     * Get all configured params from routes.
     *
     * @return array All parameters that are configured in config/routes.json
     */
    
    private function getConfiguredParams() : array{
        $array = [];
        foreach ($this->routes as $name => $info){
            foreach ($info as $key => $value){
               if($key == 'params'){ 
                   $array += $value;
               }
            }
        }
        return $array;
    }

}
?>
