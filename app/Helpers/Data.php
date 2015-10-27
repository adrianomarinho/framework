<?php
namespace Helpers;

/*
 * data Helper - common data lookup methods
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 1.0
 * @date March 28, 2015
 * @date May 18 2015
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Inclusion of new methods
 */
class Data
{

    public static function resumeText($text, $maximum_characters)
    {
    
    	// REMOVE TODAS AS TAGS HTML E GERA UM RESUMO DE ACORDO COM A QUANTIDADE DE CARACTERES INFORMADO
    		
    	$post = self::html2txt($text);
    		
    	$ex = explode(" ", strip_tags( $post ) );
    		
    	$newText = '';
    		
    	foreach( $ex as $palavra ) {
    		if ( strlen($newText) < $maximum_characters ) {
    			$size = strlen($newText) + strlen($palavra);
    				
    			if ( $size < $maximum_characters ) {
    				$newText .= " {$palavra}";
    			} else {
    				break;
    			}
    		} else {
    			break;
    		}
    	}
    		
    	return $newText;
    }

    public static function html2txt($text)
    {
    
    	// REMOVE TODAS AS TAGS HTML DO POST TRANFORMANDO EM APENAS TEXT SIMPLES
    
    	$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
    			'@<[\/\!]*?[^<>]*?>@si',            	  // Strip out HTML tags
    			'@<style[^>]*?>.*?</style>@siU',    	  // Strip style tags properly
    			'@<![\s\S]*?--[ \t\n\r]*>@'         	  // Strip multi-line comments including CDATA
    	);
    	$result = preg_replace($search, '', $text);
    	return $result;
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

	public static function unserialize( $data ) {
		if ( self::isSerialized( $data ) ) // don't attempt to unserialize data that wasn't serialized going in
			return unserialize( $data );
		return $data;
	}

	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 *
	 *
	 * @param string $data   Value to check to see if was serialized.
	 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
	 * @return bool False if not serialized and true if it was.
	 */
	public static function isSerialized( $data, $strict = true ) {
		// if it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
	 	if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace )
				return false;
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 )
				return false;
			if ( false !== $brace && $brace < 4 )
				return false;
		}
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}

	/**
	 * Check whether serialized data is of string type.
	 *
	*
	 *
	 * @param string $data Serialized data.
	 * @return bool False if not a serialized string, true if it is.
	 */
	public static function isSerializedString( $data ) {
		// if it isn't a string, it isn't a serialized string.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( strlen( $data ) < 4 ) {
			return false;
		} elseif ( ':' !== $data[1] ) {
			return false;
		} elseif ( ';' !== substr( $data, -1 ) ) {
			return false;
		} elseif ( $data[0] !== 's' ) {
			return false;
		} elseif ( '"' !== substr( $data, -2, 1 ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Serialize data, if needed.
	 *
	 *
	 *
	 * @param string|array|object $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	public static function serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) )
			return serialize( $data );

		// Double serialization is required for backward compatibility.
		// See https://core.trac.wordpress.org/ticket/12930
		if ( self::isSerialized( $data, false ) )
			return serialize( $data );

		return $data;
	}

    /**
     * strlen call - count the lengh of the string
     * @param  string $data
     * @return string return the count
     */
    public static function sl($data)
    {
        return strlen($data);
    }

    /**
     * strtoupper - convert string to uppercase
     * @param  string $data
     * @return string
     */
    public static function stu($data)
    {
        return strtoupper($data);
    }

    /**
     * strtolower - convert string to lowercase.
     *
     * @param  string $data
     * @return string
     */
    public static function stl($data)
    {
        return strtolower($data);
    }

    /**
     * ucwords - the first letter of each word to be a capital
     * @param  string $data
     * @return string
     */
    public static function ucw($data)
    {
        return ucwords($data);
    }

    public static function pr($data)
    {
    	print "<pre>";
    	print_r($data);
    	print "</pre>";
    }

    public static function vd($data)
    {
    	print "<pre>";
    	var_dump($data);
    	print "</pre>";
    }

    public static function json($data)
    {
        print json_encode($data);
    }

    /**
     * key - this will generate a 35 character key
     * @return string
     */
     public static function createKey($length = 32)
     {
        $chars = "!@#$%^&*()_+-=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars{rand(0, strlen($chars) - 1)};
        }
        return $key;
     }
}
