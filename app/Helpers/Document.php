<?php
namespace Helpers;

/*
 * Document Helper - collection of methods for working with documents
 *
 * @author David Carr <dave@simplemvcframework.com>
 * @version 1.0
 * @date updated Feb 07, 2015
 * @date May 18 2015
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @updated in July 31 2015
 * Inclusion of new methods
 */
class Document
{
    /**
     * group types into collections, its purpose is to assign the passed extension to the suitable group
     * @param  string $extension file extension
     * @return string            group name
     */
    public static function getFileType($extension)
    {
        $images = array('jpg', 'gif', 'png', 'bmp');
        $docs   = array('txt', 'rtf', 'doc', 'docx', 'pdf');
        $apps   = array('zip', 'rar', 'exe', 'html');
        $video  = array('mpg', 'wmv', 'avi', 'mp4');
        $audio  = array('wav', 'mp3');
        $db     = array('sql', 'csv', 'xls','xlsx');

        if (in_array($extension, $images)) {
            return "Image";
        }
        if (in_array($extension, $docs)) {
            return "Document";
        }
        if (in_array($extension, $apps)) {
            return "Application";
        }
        if (in_array($extension, $video)) {
            return "Video";
        }
        if (in_array($extension, $audio)) {
            return "Audio";
        }
        if (in_array($extension, $db)) {
            return "Database/Spreadsheet";
        }
        return "Other";
    }

    /**
     * create a human friendly measure of the size provided
     * @param  integer  $bytes     file size
     * @param  integer $precision precision to be used
     * @return string             size with measure
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Converts a human readable file size value to a number of bytes that it
     * represents. Supports the following modifiers: K, M, G and T.
     * Invalid input is returned unchanged.
     *
     * Example:
     * <code>
     * $config->getBytesSize(10);          // 10
     * $config->getBytesSize('10b');       // 10
     * $config->getBytesSize('10k');       // 10240
     * $config->getBytesSize('10K');       // 10240
     * $config->getBytesSize('10kb');      // 10240
     * $config->getBytesSize('10Kb');      // 10240
     * // and even
     * $config->getBytesSize('   10 KB '); // 10240
     * </code>
     *
     * @param number|string $value
     * @return number
     */
    public static function getBytesSize($value)
    {
        return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
            switch (strtolower($m[2])) {
                case 't':
                    $m[1] *= 1024;
                    break;
                case 'g':
                    $m[1] *= 1024;
                    break;
                case 'm':
                    $m[1] *= 1024;
                    break;
                case 'k':
                    $m[1] *= 1024;
                    break;
            }
            return $m[1];
        }, $value);
    }

    /**
     * return the bytes file of a folder
     * @param string $path
     * @return string
     */
    public static function getFolderSize($path)
    {
        $io = popen('/usr/bin/du -sb '.$path, 'r');
        $size = intval(fgets($io, 80));
        pclose($io);
        return $size;
    }

    /**
     * return the file type based on the filename provided
     * @param  string $file
     * @return string
     */
    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * remove extension of file
     * @param  string  $file filename and extension
     * @return file name missing extension
     */
    public static function removeExtension($file)
    {
        if (strpos($file, '.')) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }
        return $file;
    }
    
    public static function upload($config, $files)
    {
        //Ex.:
        // $config['path'] = 'uploads/candidatos/';
        // $config['size'] = 1024 * 1024 * 2;
        // $config['type'] = array('jpg', 'png', 'gif');
        // $config['rename'] = false;

        if (!file_exists($config['path'])) {
            mkdir($config['path'], 0777, true);
        }

        foreach ($files as $key => $value) {

            //\Core\View::output($value, 'text');

            $error[0] = 'Não houve erro';
            $error[1] = 'O arquivo no upload é maior do que o limite do PHP';
            $error[2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
            $error[3] = 'O upload do arquivo foi feito parcialmente';
            $error[4] = 'Não foi feito o upload do arquivo';

            $name = $value['name'];
            $extension = self::getExtension( $name );
            $file_error = $value['error'];
            $size = $value['size'];
            $tmp_name = $value['tmp_name'];

            if ($file_error != 0) {

                array_push(
                    self::$log, 
                    array(
                        "field" => $key,
                        "file" => $name,
                        "status" => false, 
                        "message" => "Não foi possível fazer o upload, erro:" . $error[$file_error]
                    )
                );
            }

            
            if (array_search($extension, $config['type']) === false) {

                array_push(
                    self::$log, 
                    array(
                        "field" => $key,
                        "file" => $name,
                        "status" => false, 
                        "message" => "Por favor, envie arquivos com as seguintes extensões: jpg, png ou gif"
                    )
                );
            }

            if ($config['size'] < $size) {

                array_push(
                    self::$log, 
                    array(
                        "field" => $key,
                        "file" => $name,
                        "status" => false, 
                        "message" => "O arquivo enviado é maior que o permitido"
                    )
                );
            }

            if ($config['rename'] == true) {
              
              $name_final = $key .'.' . $extension;

            } else {
              
              $extension = self::getExtension( $name );
              $name_final = self::removeExtension( $name ) .'_'. md5( time() ) .'.'. $extension;

            }
              

            if (move_uploaded_file($tmp_name, $config['path'] . $name_final)) {

                array_push(
                    self::$log, 
                    array(
                        "field" => $key,
                        "file" => $name,
                        "status" => true, 
                        "message" => DIR . '/' . $config['path'] . $name_final
                    )
                );                    

            } else {

                array_push(
                    self::$log, 
                    array(
                        "field" => $key,
                        "file" => $name,
                        "status" => false, 
                        "message" => "Não foi possível enviar o arquivo, tente novamente"
                    )
                );

            }

        }

        return self::$log;
    }
}
