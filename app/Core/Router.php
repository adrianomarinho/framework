<?php

namespace Core;

/**
 * Router - routing urls to closurs and controllers - modified from https://github.com/NoahBuscher/Macaw
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 2.2
 * @date Auguest 16th, 2014
 * @date updated May 18 2015
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Refactoring and inclusion of new methods
 */

/**
 * @method static Router get(string $route, Callable $callback)
 * @method static Router post(string $route, Callable $callback)
 * @method static Router put(string $route, Callable $callback)
 * @method static Router delete(string $route, Callable $callback)
 * @method static Router options(string $route, Callable $callback)
 * @method static Router head(string $route, Callable $callback)
 */
class Router {
    
    // Fallback for auto dispatching feature.
    public static $fallback = false;

    // If true - do not process other routes when match is found
    public static $halts = true;

    // Set routes, methods and etc.
    public static $routes = array();
    public static $methods = array();
    public static $callbacks = array();
    public static $error_callback;

    // Set route patterns
    public static $patterns = array(
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );

    /**
     * Defines a route w/ callback and method
     *
     * @param   string $method
     * @param   array @params
     */
    public static function __callstatic($method, $params){

    	$php_self = $_SERVER['PHP_SELF'];
    	
        $uri = dirname($php_self).'/'.$params[0];
        $callback = $params[1];        

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    /**
     * Defines callback if route is not found
     * @param   string $callback
     */
    public static function error($callback){
        self::$error_callback = $callback;
    }

    /**
     * Don't load any further routes on match
     * @param  boolean $flag 
     */
    public static function haltOnMatch($flag = true){
        self::$halts = $flag;
    }

    /**
     * Call object and instantiate
     *
     * @param  object $callback 
     * @param  array $matched  array of matched parameters
     * @param  string $msg      
     */
    public static function invokeObject($callback, $matched = null, $msg = null){
    	
        //grab all parts based on a / separator and collect the last index of the array
        $params = explode('/',$callback);
        
        $first = array_shift($params);

        //grab the controller name and method call
        $segments = explode('@',$first);
        
        //instanitate controller with optional msg (used for error_callback)
        
        $path = explode('\\',$segments[0]);
        
        foreach ($path as $k => $v){
            $path[$k] = ucfirst($path[$k]);
        }
        
        $new_path = self::convertToStudly( implode('\\', $path) );
        
        
        if(!$path[1]){
            
            $new_path .= DEFAULT_CONTROLLER;
            $method = DEFAULT_METHOD;
            
        } else{
            
            $method = self::convertToCamel( $segments[1] );
            
        }
        

       //verifica se classe existe
       if(class_exists($new_path)){
        
           $controller = new $new_path($msg);       
    
           if($matched == null){
    
                //verifica se método existe
                if( method_exists($controller, $method) ){
                    $controller->$method($params);
                 }else{
                     self::invokeObject('Core\\Error@index');
                 }
    
            } else {
    
                //call method and pass in array keys as params
                call_user_func_array(array($controller, $method), $matched);
            
            }
       }else{
            self::invokeObject('Core\\Error@index');
       }
    }

    /**
     * Runs the callback for the given request
     */
    public static function run(){

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];  

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//','/',self::$routes);  
        
        $found_route = false;

        // parse query parameters
        {
            $query = '';
            $q_arr = array();
            if(strpos($uri, '&') > 0) {
                $query = substr($uri, strpos($uri, '&') + 1);
                $uri = substr($uri, 0, strpos($uri, '&'));
                $q_arr = explode('&', $query);
                foreach($q_arr as $q) {
                    $qobj = explode('=', $q);
                    $q_arr[] = array($qobj[0] => $qobj[1]);
                    if(!isset($_GET[$qobj[0]]))
                    {
                        $_GET[$qobj[0]] = $qobj[1];
                    }
                }
            }
        }

        // check if route is defined without regex
        if (in_array($uri, self::$routes)) {
            $route_pos = array_keys(self::$routes, $uri);

            // foreach route position
            foreach ($route_pos as $route) {

                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $found_route = true;

                    //if route is not an object 
                    if(!is_object(self::$callbacks[$route])){

                        //call object controller and method
                        self::invokeObject(self::$callbacks[$route]);
                        if (self::$halts) return;

                    } else { 

                        //call closure
                        call_user_func(self::$callbacks[$route]);
                        if (self::$halts) return;

                    }
                }

            }
            // end foreach

        } else {

            // check if defined with regex
            $pos = 0;

            // foreach routes
            foreach (self::$routes as $route) {

                $route = str_replace('//','/',$route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {

                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                        $found_route = true; 

                        //remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        if(!is_object(self::$callbacks[$pos])){

                            //call object controller and method
                            self::invokeObject(self::$callbacks[$pos],$matched);
                            if (self::$halts) return;

                        } else {

                            //call closure
                            call_user_func_array(self::$callbacks[$pos], $matched);
                            if (self::$halts) return;

                        }

                    }
                }
                $pos++;
            }
            // end foreach
        }

        // run the error callback if the route was not found
        if (!$found_route) {
            if (!self::$error_callback) {
                self::$error_callback = function() {
                   self::invokeObject('Core\\Error@index');
                };
            } 

            if(!is_object(self::$error_callback)){

                //call object controller and method
                self::invokeObject(self::$error_callback, null, 'No routes found.');
                if (self::$halts) return;

            } else {

                call_user_func(self::$error_callback); 
                if (self::$halts) return;

            }

        }

    }
    
    public static function autoRun(){
        self::any('/', 'Controllers\\' . DEFAULT_CONTROLLER . '@'. DEFAULT_METHOD);
        
        self::any('/(:any)/?(:all)', function($c, $m) {
            $path = ($m) ? "Controllers\\{$c}@{$m}" : "Controllers\\{$c}@index";
            self::invokeObject($path);
            
        });
        
        self::run();
    }
    
    public static function convertToStudly($string){
        if( strpos($string, '-') !== false ){
            
            $string = ucwords(str_replace('-', ' ', $string));
            $string = str_replace(' ', '', $string);
            return ucfirst($string);
            
        }else{
            return $string;
        }
    }
    
    // snake_case to camelCase
    public static function convertToCamel($string){
        if( strpos($string, '-') !== false ){
            
            $string = ucwords(str_replace('-', ' ', $string));
            $string = str_replace(' ', '', $string);
            return lcfirst($string);
            
        }else{
            return $string;
        }
    }
    
}
