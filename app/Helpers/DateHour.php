<?php
namespace Helpers;

/*
 * DateHour Helper
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @date Out 23 2015
 */
class DateHour
{
    /**
     * Now
     * 
     * */

    public static function now($format = null)
    {
        if(!$format)
            return date('Y-m-d H:i:s');

        return date($format);
    }


    /**
     * 
     * Date US
     * */
    public static function dateUS($date = null)
    {
        if(!$date) return date('Y-m-d');
        return implode("-", array_reverse(explode("/", $date)));
    }

    /**
     * 
     * Date BR
     * */
    public static function dateBR($date = null)
    {
        
        if(!$date) return date('d/m/Y');

        $date = new \DateTime($date);
        return $date->format('d/m/Y');

    }

    /**
     * 
     * Date time US
     * */
    public static function dateTimeUS($date_time)
    {

        list($date, $time) = explode(" ", $date_time);
        list($day, $month, $year) = explode("/", $date);
        return sprintf("%d-%d-%d %d", $year, $month, $day, $time);

      }

    /**
     * 
     * Date time BR
     * */
    public static function dateTimeBR($date_time)
    {
        $date = new \DateTime($date_time);
        return $date->format('d/m/Y H:i:s');
    }

    /**
     * 
     * Extract time of date
     * */
    public static function dateToTime($date_time)
    {
        $time = new \DateTime($date_time);
        return $time->format('H:i:s');
    }

    /**
     * 
     * Add ou subtract day, week, month ou year in date
     * */
    public static function addInDate($amount, $type, $date)
    {
        if(!$date)
            $date = date('Y-m-d');

        return date('Y-m-d', strtotime("$amount $type", strtotime($date)));
    }

    /**
     * 
     * Add days in date
     * */
    public static function addDaysDate($amount_days, $date = null)
    {
        return static::addInDate("+$amount_days", 'day', $date);
    }


    /**
     * 
     * Add days in date
     * */
    public static function subDaysDate($amount_days, $date = null)
    {
        return static::addInDate("-$amount_days", 'day', $date);
    }

    /**
     * 
     * Add weeks in date
     * */
    public static function addWeeksDate($amount_weeks, $date = null)
    {
        return static::addInDate("+$amount_weeks", 'week', $date);
    }

    /**
     * 
     * Sub weeks in date
     * */
    public static function subWeeksDate($amount_weeks, $date = null)
    {
        return static::addInDate("-$amount_weeks", 'week', $date);
    }

    /**
     * 
     * Add months in date
     * */
    public static function addMonthsDate($amount_months, $date = null)
    {
        return static::addInDate("+$amount_months", 'month', $date);
    }

    /**
     * 
     * Sub months in date
     * */
    public static function subMonthsDate($amount_months, $date = null)
    {
        return static::addInDate("-$amount_months", 'month', $date);
    }

    /**
     * 
     * Add years in date
     * */
    public static function addYearsDate($amount_years, $date = null)
    {
        return static::addInDate("+$amount_years", 'year', $date);
    }

    /**
     * 
     * Sub years in date
     * */
    public static function subYearsDate($amount_years, $date = null)
    {
        return static::addInDate("-$amount_years", 'year', $date);
    }
    
    public static function extensiveDate($date_time = null)
    {
    
        if(!$date_time){
            $timestamp = time();
        }else{
            $timestamp = strtotime($date_time);
        }
    
        $date = getdate($timestamp);
    
        $day_week = array(
            "Domingo, ", 
            "Segunda-feira, ", 
            "Terça-feira, ", 
            "Quarta-feira, ", 
            "Quinta-feira, ", 
            "Sexta-feira, ", 
            "Sábado, "
        );

        $months = array(
            "", 
            "Janeiro", 
            "Fevereiro ", 
            "Março", 
            "Abril", 
            "Maio", 
            "Junho", 
            "Julho", 
            "Agosto", 
            "Setembro", 
            "Outubro", 
            "Novembro", 
            "Dezembro"
        );
    
        return $day_week[$date["wday"]] ." ". $date["mday"]." de ".$months[$date["mon"]]." de ".$date["year"];
    }

    /**
     * 
     * Convert time in seconds
     * */

    public static function timeToSec($time)
    { 
        list($hours, $minutes, $seconds) = explode(":", $time);
        return ($hours * 3600) + ($minutes * 60) + $seconds; 
    } 

    /**
     * 
     * Convert seconds in time
     * */
    public static function secToTime($seconds)
    { 
        $hours = floor($seconds / 3600);
        $minutes = floor($seconds % 3600 / 60); 
        $seconds = $seconds % 60; 

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds); 
    } 

    /**
     * get the difference between 2 dates
     * @param  date $from start date
     * @param  date $to   end date
     * @return string or array, if type is set then a string is returned otherwise an array is returned
     * 
     * Samples
     * 
     * DateHour::difference(Y-m-d)->y
     * DateHour::difference(Y-m-d)->m
     * DateHour::difference(Y-m-d)->d
     * DateHour::difference(Y-m-d)->h
     * DateHour::difference(Y-m-d)->i
     * DateHour::difference(Y-m-d)->s
     * DateHour::difference(Y-m-d)->weekday
     * DateHour::difference(Y-m-d)->days
     * DateHour::difference(Y-m-d)->format('%Y Years, %m Months e %d Days')
     * 
     * */

    public static function difference($date_from, $date_to = null)
    {
        if(!$date_to){
            $date_to = new \DateTime();
        }
        else{
            $date_to = new \DateTime($date_to);
        }

        $date_from = new \DateTime( $date_from );
        $result = $date_from->diff( $date_to );
        $result->months = ($result->y * 12) + $result->m;
        $result->weeks = number_format( ($result->days / 7), 2, '.', '');

        return $result;
    }


    /**
     * 
     * Get age of date
     * */
    public static function age($birth_date)
    {
        return static::difference($birth_date)->y;
    }
    
}
