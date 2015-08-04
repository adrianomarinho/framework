<?php 

namespace Helpers;

/*
 * Session Class - prefix sessions with useful methods
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
class Session {

	/**
	 * determinar se a sessão já iniciou
	 * @var boolean
	 */
	private static $sessionStarted = false;

	public static function init()
	{
		if (self::$sessionStarted == false) {
			session_start();
			self::$sessionStarted = true;
		}
	}

	/**
	 * adicionar valor a uma sessão
	 * @param array $key array com chave e valor
	 */
	public static function set($key, $value = false){
		
		if (is_array($key) && $value === false) {
			foreach ($key as $name => $value) {
				$_SESSION[SESSION_PREFIX][$name] = $value;
			}
		} else {
			$_SESSION[SESSION_PREFIX][$key] = $value;
		}
		
	}

	/**
	 * buscar valor da sessao
	 * 
	 * @param  string  $key
	 * @return string  valor retornado
	 */
	public static function get($key){
		return isset($_SESSION[SESSION_PREFIX][$key]) ? $_SESSION[SESSION_PREFIX][$key] : false;
	}
	
	/**
	 * @return string com id da seção
	 */
	public static function id() {
		return session_id();
	}

	/**
	 * @return retorna sessao inteira
	 */
	public static function view($tipo = null){
		$result =  isset($_SESSION[SESSION_PREFIX]) ? $_SESSION[SESSION_PREFIX] : false;
		return View::output($result, $tipo);
	}
	
	/**
	 * encerra a seção inteira ou apenas exclui um ou mais registros caso seja infomrado a chave ou array de chaves
	 * @param  string or array $key chave ou array de caves dos registros que serão deletados
	 */
	public static function destroy($key = null) {
		if(self::$sessionStarted == true) {

			if(!$key) {
				session_unset();
				session_destroy();
				return true;
			} else {
				
				if(is_array($key)){
					
					foreach ($key as $name => $value) {
						unset($_SESSION[SESSION_PREFIX][$value]);
					}
					
				}else{
					unset($_SESSION[SESSION_PREFIX][$key]);
				}

			}

		}
	}

}
