<?php
namespace Controllers;

use Core\Controller;
use Core\View;

class Welcome
{

    public function index()
    {
    	$data['subtitle'] = 'Welcome';
    	
    	View::render('site/php/header', $data);
    	View::render('site/php/home');
    	View::render('site/php/footer');
    	
    }
}