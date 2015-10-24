<?php
namespace Helpers;

use Helpers\Session;
use Helpers\Validations;

/*
 * url Class
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Inclusion of new methods
 */
class Url
{

    /**
     * Redirect to chosen url
     * @param  string  $url      the url to redirect to
     * @param  boolean $fullpath if true use only url in redirect instead of using DIR
     */
    public static function redirect($url = null, $fullpath = false)
    {
        if ($fullpath == false) {
            $url = DIR . $url;
        }

        header('Location: ' . $url);
        exit;
    }
    
    /**
     * 
     * Request method
     * 
     * @param string $url
     * @param string $method
     * @param array $data
     * @param bool $fullpath
     */

    public static function request($url, $method, $data = array(), $fullpath = false)
    {
        
        if ($fullpath == false) {
            $url = DIR . $url;
        }
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Redirecting...</title>
        </head>
        <body>
            Redirecting...
            <form id="redirect" method="<?=$method?>" action="<?=$url?>">
                <?php
                if ( !is_null($data) ) {
                    foreach ($data as $k => $v) {
                        echo "<input type=\"hidden\" name=\"$k\" value=\"$v\">";
                    }
                }
                ?>
            </form>
            <script type="text/javascript">
               window.onload = function() {
                 document.getElementById("redirect").submit();
               }
            </script>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Go to the bck url.
     */
    public static function back($method = null, $data = null)
    {
    	$url = $_SERVER['HTTP_REFERER'];
    	
    	if($method && $data){
    		self::request($url, $method, $data, true);
    	}
    	
    	else{
    		self::redirect($url, true);
    	}
    }

    /**
     * created the absolute address to the template folder
     * @return string url to template folder
     */
    public static function templatePath($custom = false)
    {
        if ($custom == true) {
            return DIR . '/app/templates/' . $custom.'/';
        } else {
            return DIR . '/app/templates/' . DEFAULT_TEMPLATE.'/';
        }
    }

    /**
     * converts plain text urls into HTML links, second argument will be
     * used as the url label <a href=''>$custom</a>
     *
     * @param  string $text   data containing the text to read
     * @param  string $custom if provided, this is used for the link label
     * @return string         returns the data with links created around urls
     */
    public static function autoLink($url, $label = null, $class = null, $id = null, $target = null)
    {
        $regex   = '@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@';

        $class = ($class) ? ' class="'.$class.'" ' : '';
        $id = ($id) ? ' id="'.$id.'" ' : '';
        $target = ($target) ? ' target="'.$target.'"' : '';
        
        if ($label === null) {
            $replace = '<a' . $class . $id . $target . ' href="http$2://$4">$1$2$3$4</a>';
        } else {
            $replace = '<a' . $class . $id . $target . ' href="http$2://$4">'.$label.'</a>';
        }

        return preg_replace($regex, $replace, $url);
    }

    /**
     * This function converts and url segment to an safe one, for example:
     * `test name @132` will be converted to `test-name--123`
     * Basicly it works by replacing every character that isn't an letter or an number to an dash sign
     * It will also return all letters in lowercase
     *
     * @param $slug - The url slug to convert
     *
     * @return mixed|string
     */
    public static function generateSafeSlug($slug)
    {
        // transform url
        $slug = Validations::removeAccents($slug);
        $slug = preg_replace('/[^a-zA-Z0-9]/', '-', $slug);
        $slug = strtolower(trim($slug, '-'));

        //Removing more than one dashes
        $slug = preg_replace('/\-{2,}/', '-', $slug);

        return $slug;
    }


    /**
     * get last item in array
     */
    public static function lastSegment()
    {
        return end( self::segments('NUMBERS') );
    }

    /**
     * get first item in array
     */
    public static function firstSegment()
    {
    	$path_uri = self::segments('NUMBERS');
        return $path_uri[0];
    }
    
    public static function pathUri()
    {
    	return str_replace(dirname($_SERVER['PHP_SELF']) . '/', '', $_SERVER['REQUEST_URI']);
    }
    
    
    /**
     * @return string url
     */
    
    public static function get(){
    	return DIR . '/' . self::pathUri();
    }
    
    
    /**
     * @param mixed segment
     * @return function callback
     * Ex.:
     *      Url::segments() //Return array all segments;
     *      Url::segments(int) //Return array ints;
     *      Url::segments(last) //Return last segment uri;
     *      Url::segments(first) //Return first segment uri
     *      Url::segments(1) //Return sring position route;
     *      Url::segments(a) //Return sring position route;
     *      Url::segments(a, 'app') //Return boolen;
     *      Url::segments(a, 'app', fn callback) //Return sring position route;
     */
    
    public static function segments(){
    	//Args
    	$args = func_get_args();
    	
    	//Letters params rotas
    	$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
    
    	if(isset($args[0])){
    		$args[0] = is_string($args[0]) ? strtoupper($args[0]) : $args[0];
    	}
    
    	$path_info = explode('/', self::pathUri());
    	
    	$route = array();
    
    	for ($i = 0; $i < count($path_info); $i++){
    		$route[$i] = $path_info[$i];
    	}
    
    	//Exibir apenas array com indices numericos
    	if(count($args) == 1){
    		switch($args[0]){
    			
    			case 'numbers':
    			case 'NUMBERS':
    			case 'number':
    			case 'int':
    				
    				return $route;
    				break;
    				
    			case 'first':
    			case 'FIRST':
    				
    				return $route[0];
    				break;
    				
    			case 'end':
    			case 'END':	
    			case 'last':
    			case 'LAST':
    				return end( $route );
    				break;
    		}
    	}
    
    	for ($i = 0; $i < count($path_info); $i++){
    		if(count($letters) == $i){
    			break;
    		}
    
    		$letter = $letters[$i];
    		$route[$letter] = $path_info[$i];
    	}
    
    
    	switch (count($args)) {
    		case 1:
    			 
    			return isset($route[$args[0]]) ? $route[$args[0]] : null ;
    			break;
    
    		case 2:
    			return $args[1] == $route[$args[0]] ? true : false;
    			break;
    
    		case 3:
    			return $args[1] == $route[$args[0]] ? $args[2]() : null;
    			break;
    			 
    		default:
    			return $route;
    			break;
    
    	}
    
    }
}
