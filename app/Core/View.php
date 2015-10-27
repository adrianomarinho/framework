<?php

namespace Core;

/**
 * Views
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.1
 * @date June 27, 2014
 * @date May 18 2015
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Refactoring and inclusion of new methods
 */
use Helpers\Validations;

class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = [];

    /**
     * return absolute path to selected template directory
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function render($path, $data = false, $custom = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }

        if ($custom === false) {
            require "app/templates/".DEFAULT_TEMPLATE."/$path.php";
        } else {
            require "app/templates/$path.php";
        }
    }

    /**
     * add HTTP header to headers array
     * @param  string  $header HTTP header text
     */
    public function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
    * Add an array with headers to the view.
    * @param array $headers
    */
    public function addHeaders($headers = array())
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }
}
