<?php
namespace Core;

use Helpers\PhpMailer\Mail;

/*
 * logger class - Custom errors
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Logger
{

    /**
    * determins if error should be displayed
    * @var boolean
    */
    private static $printError = false;

    /**
    * determins if error should be emailed to SITEEMAIL defined in app/Core/Config.php
    * @var boolean
    */
    private static $emailError = false;

    /**
    * clear the errorlog
    * @var boolean
    */
    private static $clear = false;

    /**
    * path to error file
    * @var boolean
    */
    private static $errorFile = 'log.html';

    /**
    * in the event of an error show this message
    */
    public static function customErrorMsg()
    {

        $title = SITETITLE;// . " [Erro inesperado]";
        $mail = SITEEMAIL;
        $link_log = DIR . "/log.html";
        
        $template = <<<EOT
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>{$title}</title>
            <style>
                html,body,div,span,object,iframe,
                h1,h2,h3,h4,h5,h6,p,blockquote,pre,
                abbr,address,cite,code,
                del,dfn,em,img,ins,kbd,q,samp,
                small,strong,sub,sup,var,
                b,i,
                dl,dt,dd,ol,ul,li,
                fieldset,form,label,legend,
                table,caption,tbody,tfoot,thead,tr,th,td,
                article,aside,canvas,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section,summary,
                time,mark,audio,video {
                    margin: 0;
                    padding: 0;
                    border: 0;
                    outline: 0;
                    font-size: 100%;
                    vertical-align: baseline;
                    background: transparent;
                }
                body {
                    line-height: 1;
                }
                article,aside,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section {
                    display: block;
                }
                nav ul {
                    list-style: none;
                }
                blockquote,q {
                    quotes: none;
                }
                blockquote:before,blockquote:after,
                q:before,q:after {
                    content: '';
                    content: none;
                }
                a {
                    margin: 0;
                    padding: 0;
                    font-size: 100%;
                    vertical-align: baseline;
                    background: transparent;
                }
                ins {
                    background-color: #ff9;
                    color: #000;
                    text-decoration: none;
                }
                mark {
                    background-color: #ff9;
                    color: #000;
                    font-style: italic;
                    font-weight: bold;
                }
                del {
                    text-decoration: line-through;
                }
                abbr[title],dfn[title] {
                    border-bottom: 1px dotted;
                    cursor: help;
                }
                table {
                    border-collapse: collapse;
                    border-spacing: 0;
                }
                hr {
                    display: block;
                    height: 1px;
                    border: 0;
                    border-top: 1px dashed #CCC;
                    margin: 2em 0;
                    padding: 0;
                }
                input,select {
                    vertical-align: middle;
                }
                html {
                    background: #EDEDED;
                    height: 100%;
                }
                body {
                    background: #FFF;
                    margin: 50px auto;
                    min-height: 100%;
                    padding: 0 30px;
                    width: 440px;
                    color: #666;
                    font: 14px/23px Arial,Verdana,sans-serif;
                }
                h1,h2,h3,p,ul,ol,form,section {
                    margin: 0 0 20px 0;
                }
                h1 {
                    color: #6F6F6F;
                    font-size: 20px;
                }
                h2,h3 {
                    color: #989898;
                    font-size: 14px;
                }
                h3 {
                    margin: 0;
                    font-size: 12px;
                    font-weight: bold;
                }
                ul,ol {
                    list-style-position: inside;
                    color: #999;
                }
                ul {
                    list-style-type: square;
                }
                code,kbd {
                    background: #EEE;
                    border: 1px solid #DDD;
                    border: 1px solid #DDD;
                    border-radius: 4px;
                    -moz-border-radius: 4px;
                    -webkit-border-radius: 4px;
                    padding: 0 4px;
                    color: #666;
                    font-size: 12px;
                }
                pre {
                    background: #EEE;
                    border: 1px solid #DDD;
                    border-radius: 4px;
                    -moz-border-radius: 4px;
                    -webkit-border-radius: 4px;
                    padding: 5px 10px;
                    color: #666;
                    font-size: 12px;
                }
                pre code {
                    background: transparent;
                    border: none;
                    padding: 0;
                }
                a {
                    color: #70a23e;
                }
                header {
                    padding: 30px 0;
                    text-align: center;
                }

                .dev-msg {
                    color: #ccc;
                    text-align: center;
                    font-size: 0.8em;
                }

                .dev-msg a{
                    color: #ccc;
                }
                            
            </style>
        </head>
        <body>
            <header>
                <h1>{$title}</h1>
            </header>

            <p>
               Ooops, ocorreu um erro inesperado no sistema. 
               Já estamos trabalhando para corrigir o problema.
               
               <br><br>Se o erro persistir escreva um email para <a href="mailto:{$mail}">{$mail}</a>
            </p>
            
            <hr>
            
            <section>
                    <p class="dev-msg">Desenvolvedor, veja o log de erros <a href="{$link_log}" target="_blank">aqui</a></p>
            </section>

        </body>
    </html>
EOT;
        echo $template;
        exit;
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
        $date = date('M d, Y G:iA');

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
        self::sendEmail($logMessage);

        if (self::$printError == true) {
            echo $logMessage;
            exit;
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
        $date = date('M d, Y G:iA');
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
        self::sendEmail($logMessage);

        if (self::$printError == true) {
            echo $logMessage;
            exit;
        }
    }

    public static function sendEmail($message)
    {
            $mail = new \Helpers\PhpMailer\Mail(array('type' => 'php'));
            
            $subject = 'Novo erro no '.SITETITLE;
            $from = array('mail' => SITEEMAIL, 'name' => SITETITLE);
            $destination = array(SITEEMAIL .",". SITETITLE);
            
            $mail->quick($subject, $message, $from, $destination);
    }
}
