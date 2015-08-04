<?php
namespace Core;

/*
 * View - load template pages
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
use Helpers\Validations;
use Delpers\Data;

class View
{
    /**
     * @var array Array of HTTP headers
     */
    private static $headers = array();

    /**
     * include template file
     * @param  string  $path  path to file from views folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function render($path, $data = false, $error = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        require "app/Views/$path.php";
    }

    /**
     * include template file
     * @param  string  $path  path to file from Modules folder
     * @param  array $data  array of data
     * @param  array $error array of errors
     */
    public static function renderModule($path, $data = false, $error = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
        require "app/Modules/$path.php";
    }

    /**
     * return absolute path to selected template directory
     * @param  string  $path  path to file from views folder
     * @param  array   $data  array of data
     * @param  string  $custom path to template folder
     */
    public static function renderTemplate($path, $data = false, $custom = false)
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }

        if ($custom === false) {
            require "app/templates/".DEFAULT_TEMPLATE."/$path.php";
        } else {
            require "app/templates/$custom/$path.php";
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
    
    public static function convertUtf8Enconde($data)
    {
    	//CODIFICA ARRAY EM UTF8
    
    	if (is_array($data)) {
    
    		$novo = array();
    
    		foreach ($data as $i => $value) {
    
    			if (is_array($value)) {
    				$value = self::convertUtf8Enconde($value);
    
    			} else if (!mb_check_encoding($value, 'UTF-8')) {
    				$value = utf8_encode($value);
    
    			}
    
    			$novo[$i] = $value;
    		}
    
    		return $novo;
    
    	} else {
    
    		if (!mb_check_encoding($data, 'UTF-8')) {
    			$value = utf8_encode($data);
    		}
    
    	}
    }
    
    public static function output($data, $tipo = null)
    {
    	//DEBUG ARRAY
    	switch ($tipo) {
    
    		case 'pr':
    		case 'printr':
    		case 'print_r':
    		case 'text':
    			print'<pre>';
    			print_r($data);
    			print'</pre>';
    			break;
    
    			
    		case 'vd':
    		case 'vardump':
    		case 'var_dump':
    		case 'details':
    			print'<pre>';
    			var_dump($data);
    			print'</pre>';
    			break;
    
    		case 'serialize':
    			print json_encode(self::convertUtf8Enconde($data));
    			break;
    
    		case 'json':
    			header('Content-Type: application/json');
    			print json_encode(self::convertUtf8Enconde($data));
    			break;
    
    		case 'xml':
    			print'Opção XML indisponível!';
    			break;
    
    		default:
    			return self::convertUtf8Enconde($data);
    			break;
    
    	}
    }
    
    
    public static function resumePost($texto, $maximoCaracteres)
    {
    
    	// REMOVE TODAS AS TAGS HTML E GERA UM RESUMO DE ACORDO COM A QUANTIDADE DE CARACTERES INFORMADO
    		
    	$post = self::html2txt($texto);
    		
    	$ex = explode(" ", strip_tags( $post ) );
    		
    	$novoTexto = '';
    		
    	foreach( $ex as $palavra ) {
    		if ( strlen($novoTexto) < $maximoCaracteres ) {
    			$tamanho = strlen($novoTexto) + strlen($palavra);
    				
    			if ( $tamanho < $maximoCaracteres ) {
    				$novoTexto .= " {$palavra}";
    			} else {
    				break;
    			}
    		} else {
    			break;
    		}
    	}
    		
    	return $novoTexto;
    }
    
    public static function html2txt($texto)
    {
    
    	// REMOVE TODAS AS TAGS HTML DO POST TRANFORMANDO EM APENAS TEXT SIMPLES
    
    	$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
    			'@<[\/\!]*?[^<>]*?>@si',            	  // Strip out HTML tags
    			'@<style[^>]*?>.*?</style>@siU',    	  // Strip style tags properly
    			'@<![\s\S]*?--[ \t\n\r]*>@'         	  // Strip multi-line comments including CDATA
    	);
    	$result = preg_replace($search, '', $texto);
    	return $result;
    }
}
