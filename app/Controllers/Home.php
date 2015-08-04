<?php
namespace Controllers;

use Core\Controller;
use Core\View;

class Home extends Controller
{

    public function index()
    {
    	$data['subtitle'] = 'Welcome';
    	
    	View::render('header', $data);
    	View::render('home');
    	View::render('footer');
    	
    }

    
    public function teste($param)
    {

    	View::output($param, 'json');

    }
}