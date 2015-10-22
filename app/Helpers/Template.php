<?php

namespace Helpers;

/**
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @date Out 20 2015
 */

use Mustache_Engine;

class Template extends Mustache_Engine
{

    public function renderPath($path, $data, $custom = false)
    {

        if ($custom === false) {
            $filename = "app/templates/".DEFAULT_TEMPLATE."/$path";
        } else {
            $filename = "app/templates/$path";
        }

        $fd = fopen ($filename, "r");
        $template = fread ($fd, filesize ($filename));
        $rendered = $this->render($template, $data);
        fclose ($fd);

        return stripslashes($rendered);
    }

    public function renderPrint($path, $data, $custom = false){
        print $this->renderPath($path, $data, $custom)   ;
    }
 
}
