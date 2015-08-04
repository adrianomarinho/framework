<?php

namespace Helpers;

/*
 * Data collection Helper
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @version 1.0
 * @date July 31 2015
 */
class Collection
{
    private $items = array();

    public static function add($obj, $key = null) {
        if ($key == null) {
            $this->items[] = $obj;
        }
        else {
            if (isset($this->items[$key])) {
                print("A chave $key já está sendo utilizada.");
            }
            else {
                $this->items[$key] = $obj;
            }
        }
    }

    public static function delete($key) {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
        else {
            print("A chave $key é inválida.");
        }
    }

    public static function get($key) {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        else {
            print("A chave $key é inválida.");
        }
    }

    public static function keys() {
        return array_keys($this->items);
    }

    public static function length() {
        return count($this->items);
    }

    public static function keyExists($key) {
        return isset($this->items[$key]);
    }
}
