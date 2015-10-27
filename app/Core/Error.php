<?php
namespace Core;

use Helpers\Template;

/**
 * error class
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Refactoring and inclusion of new methods
 */
class Error
{
    /**
     * $error holder
     * @var string
     */
    private $error = null;
    private $view;

    /**
     * save error to $this->_error
     * @param string $error
     */
    public function __construct($error)
    {
        $this->error = $error;
        $this->view = new Template;

    }

    /**
     * load a 404 page with the error message
     */
    public function index()
    {
        header("HTTP/1.0 404 Not Found");

        $data = [
            'title' => '404 &rsaquo; ' . SITETITLE,
            'mail_dev' => SITEEMAIL,
            'error' => $this->error,
            'dir' => DIR
        ];

        $this->view->renderPrint('error/404.html', $data);

    }

    /**
     * display errors
     * @param  array  $error an error of errors
     * @param  string $class name of class to apply to div
     * @return string        return the errors inside divs
     */
    public static function display($error, $class = 'alert alert-danger')
    {
        if (is_array($error)) {
            foreach ($error as $error) {
                $row.= "<div class='$class'>$error</div>";
            }
            return $row;
        } else {
            if (isset($error)) {
                return "<div class='$class'>$error</div>";
            }
        }
    }
}
