<?php
namespace Controllers;

use Core\Controller;
use Core\View;

class Welcome
{

    public function index()
    {
    	$data['subtitle'] = 'Welcome';
    	
    	View::render('header', $data);
    	View::render('home');
    	View::render('footer');
    	
    }
}