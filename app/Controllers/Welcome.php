<?php
namespace Controllers;

use Core\Controller;
use Core\View;

class Welcome
{

    public function index()
    {
    	$data['subtitle'] = 'Welcome';
    	
    	View::render('site/header', $data);
    	View::render('site/home');
    	View::render('site/footer');
    	
    }
}