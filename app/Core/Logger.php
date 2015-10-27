<?php
namespace Core;

use Helpers\Mail;
use Helpers\Template;


/**
 * logger class - Custom errors
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Refactoring and inclusion of new methods
 */
class Logger
{

    /**
    * clear the errorlog
    * @var boolean
    */
    private static $clear = false;

    /**
    * path to error file
    * @var boolean
    */
    private static $errorFile = 'storage/logs/error/log.html';

    /**
    * in the event of an error show this message
    */
    public static function customErrorMsg()
    {

        $data = [
            'title' => 'Error &rsaquo; ' . SITETITLE,
            'url_log' => DIR . "/storage/logs/error/log.html",
            'mail_dev' => SITEEMAIL,
            'dir' => DIR
        ];

        $view = new Template;

        if(DEBUG === true){
            $view->renderPrint('error/feedback_dev.html', $data);
        }
        else{
            $view->renderPrint('error/feedback.html', $data);
        }
    }

    /**
    * saved the exception and calls customer error function
    * @param  exeption $e
    */
    public static function exceptionHandler($e)
    {
        self::newMessage($e);
        self::customErrorMsg();
    }

    /**
    * saves error message from exception
    * @param  numeric $number  error number
    * @param  string $message the error
    * @param  string $file    file originated from
    * @param  numeric $line   line number
    */
    public static function errorHandler($number, $message, $file, $line)
    {
        $msg = "$message in $file on line $line";

        if (($number !== E_NOTICE) && ($number < 2048)) {
            self::errorMessage($msg);
            self::customErrorMsg();
        }

        return 0;
    }

    /**
    * new exception
    * @param  Exception $exception
    * @param  boolean   $printError show error or not
    * @param  boolean   $clear       clear the errorlog
    * @param  string    $errorFile  file to save to
    */
    public static function newMessage(\Exception $exception)
    {

        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $date = date('Y-m-d H:i:s');

        $logMessage = "<h3>Exception information:</h3>\n
           <p><strong>Date:</strong> {$date}</p>\n
           <p><strong>Message:</strong> {$message}</p>\n
           <p><strong>Code:</strong> {$code}</p>\n
           <p><strong>File:</strong> {$file}</p>\n
           <p><strong>Line:</strong> {$line}</p>\n
           <h3>Stack trace:</h3>\n
           <pre>{$trace}</pre>\n
           <hr />\n";

        if (is_file(self::$errorFile) === false) {
            file_put_contents(self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen(self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents(self::$errorFile);
        }

        file_put_contents(self::$errorFile, $logMessage . $content);

        //send email
        if(MAIL_ERROR === true){
            self::sendEmail($logMessage);
        }
    }

    /**
    * custom error
    * @param  string  $error       the error
    * @param  boolean $printError display error
    * @param  string  $errorFile  file to save to
    */
    public static function errorMessage($error)
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "<p>Error on $date - $error</p>";

        if (is_file(self::$errorFile) === false) {
            file_put_contents(self::$errorFile, '');
        }

        if (self::$clear) {
            $f = fopen(self::$errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents(self::$errorFile);
            file_put_contents(self::$errorFile, $logMessage . $content);
        }

        //send email
        if(MAIL_ERROR === true){
            self::sendEmail($logMessage);
        }
    }

    public static function sendEmail($message)
    {
            $mail = new Mail(array('type' => 'smtp'));
            
            $subject = 'Novo erro no '.SITETITLE;
            $from = array('mail' => SITEEMAIL, 'name' => SITETITLE);
            $destination = array(SITEEMAIL .",". SITETITLE);
            
            $mail->quick($subject, $message, $from, $destination);
    }
}