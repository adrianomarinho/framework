<?php
namespace Core;

use Helpers\Session;

class Config
{
    public function __construct()
    {
    	
        //Ativa o buffer de saída
        ob_start();
        

        //Definir controller padrão e método para chamadas legados
        define('DEFAULT_CONTROLLER', 'Welcome');
        define('DEFAULT_METHOD', 'index');

        //Denifir template padrão
        define('DEFAULT_TEMPLATE', 'default');

        define('DB_TYPE', 'mysql');
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'babita');
        define('DB_USER', 'root');
        define('DB_PASS', '123456');
        define('DB_PORT', '3306');
        define('PREFIX', 'bab_');
        define('DIR', 'http://localhost/babita');

        //Define prefixo de sessão
        define('SESSION_PREFIX', 'bab');
        
        //Define coluna datetime de inserção e atualização no banco de dados
        define('DATETIME_INSERT', 'datetime_insert');
        define('DATETIME_UPDATE', 'datetime_update');
        
        //Define chave de encriptação de dados
        define('CHAVE_ENCRYPT', 'bf');

        //Define título do site / projeto
        define('SITETITLE', 'Babita Framework V1');

        //Email do administrador para notificação de erros no sistema
        define('SITEEMAIL', 'fabio@fabioassuncao.com.br');

        define('MAIL_SMTP_AUTH', true); // // Enable SMTP authentication
        define('MAIL_IS_HTML', true);  // Set email format to HTML
        define('MAIL_CHARSET', 'UTF-8');
        define('MAIL_SMTP_SECURE', 'tls'); // Enable TLS encryption, `ssl` also accepted
        define('MAIL_HOST', 'smtp.gmail.com'); //Servidor de envio
        define('MAIL_PORT', '587'); //Porta de envio
        define('MAIL_USER', 'fabiioassuncao@gmail.com'); //Login do email de envio
        define('MAIL_PASS', 'secret'); //Senha

        //Ativa a manipulação de erro personalizada
        set_exception_handler('Core\Logger::ExceptionHandler');
        set_error_handler('Core\Logger::ErrorHandler');

        //Define timezone
        date_default_timezone_set('America/Sao_Paulo');

        //Inicia sessões
        Session::init();
        
        //Habilita os erros em ambiente local
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        error_reporting(E_ALL);
    }
}
