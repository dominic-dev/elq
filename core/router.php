<?php

namespace Elastique\Core;

class Router{
    private static $patterns = [
        'int' => '\d',
        'str' => '[A-Za-z\-]+'
    ];

    public function __construct(){
        $json = file_get_contents(__DIR__ . '/../config/routes.json');
        $this->routes = json_decode($json, true);
    }

    /**
     * Take a request and map it to a route.
     *
     * @param request (Request) the request object.
     */
    
    public function route(Request $request): string{
        $path = $request->getPath();
        foreach ($this->routes as $route => $info){
            // Does route have paramaters?
            $route_params = isset($info['params']) ? $info['params'] : null;
            // Get rexeg pattern
            $pattern = $this->getPattern($route, $route_params);
            //$path = $request->getPath();

            if (preg_match($pattern, $path)){
                // Extract params from uri.
                $uri_params = $this->extractParams($route, $path);
                return $this->executeController($route, $info, $request, $uri_params);
            }

        }
    }

    /**
     * Extract the paramaters from path.
     * @return $params (array) The paramaters as key => value pairs.
     */
    
    private function extractParams(string $route, string $path){
        str_replace('\\', '', $route);
        $keys = explode('/', trim($route, '/'));
        $values = explode('/', trim($path, '/'));
        $params = array_combine($keys, $values);
        foreach ($params as $key => $value){
            // Filter paramaters.
            if (strpos($key, ':') === 0 ){
                $params[trim($key, ':')] = $value;
            }
            unset($params[$key]);
        }
        return $params;

    }

    private function executeController(string $route, array $info, Request $request, array $uri_params=null){
        $controller_name = 'Elastique\App\\' . $info['controller'] . 'Controller';
        $method = $info['method'];

        $controller = new $controller_name($request);
        return call_user_func_array([$controller, $method], $uri_params);
    }

    /**
     * Take a route from configuration and return a regex pattern.
     *
     * @param $route (string) the routing pattern as it appears in configuration.
     * @param $params (array) the params appearing in this pattern.
     * @return (string) regex pattern.
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

}
?>
