<?php
namespace Core;

use Helpers\Session;

class Config
{
    public function __construct()
    {
        
        //Turn on output buffering
        ob_start();

        define('DEBUG', true);

        /**
        * determins if error should be emailed to SITEEMAIL defined in app/Core/Config.php
        * @var boolean
        */
        define('MAIL_ERROR', false);

        //Set default controller and method for legacy calls
        define('DEFAULT_CONTROLLER', 'Welcome');
        define('DEFAULT_METHOD', 'index');

        //Set default template
        define('DEFAULT_TEMPLATE', 'default');

        define('DB_TYPE', 'mysql');
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'babita');
        define('DB_USER', 'root');
        define('DB_PASS', '123456');
        define('DB_PORT', '3306');
        define('DB_PREFIX', 'bab_');
        define('DIR', 'http://localhost/babita');

        //Set session prefix
        define('SESSION_PREFIX', 'bab');
        
        //Set datetime column insert and update the database
        define('DATETIME_INSERT', 'created_at');
        define('DATETIME_UPDATE', 'updated_at');
        
        //Define data encryption key
        define('CHAVE_ENCRYPT', 'bf');

        //Sets title of the site / project
        define('SITETITLE', 'Babita Framework V1');

        //Administrator e-mail for error notification system
        define('SITEEMAIL', 'fabio@fabioassuncao.com.br');

        define('MAIL_SMTP_AUTH', true); // // Enable SMTP authentication
        define('MAIL_IS_HTML', true);  // Set email format to HTML
        define('MAIL_CHARSET', 'UTF-8');
        define('MAIL_SMTP_SECURE', 'tls'); // Enable TLS encryption, `ssl` also accepted
        define('MAIL_HOST', 'smtp.gmail.com'); //Outgoing Server
        define('MAIL_PORT', '587'); //Port forwarding
        define('MAIL_USER', 'fabiioassuncao@gmail.com'); //Login sending email
        define('MAIL_PASS', 'secret'); //Password

        //Enables custom error handling
        set_exception_handler('Core\Logger::ExceptionHandler');
        set_error_handler('Core\Logger::ErrorHandler');

        //Define timezone
        date_default_timezone_set('America/Sao_Paulo');

        //Start sessions
        Session::init();
    
    }
}
