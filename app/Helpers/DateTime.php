<?php
namespace Helpers;

/*
 * DataTime [temporary]
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @version 1.0
 * @date July 31 2015
 */
class DateTime {


    public static function now($format = null){
        return ($format) ? date($format) : date('Y-m-d H:i:s');
    }
	
    public static function dataHoraDateTimePicker($dataHora){
        $data = substr($dataHora, 0, -6);
        $hora = substr($dataHora, 11);
        $hora .= ":00";
        $data_inverter = explode("/",$data);
        $x = $data_inverter[2].'-'. $data_inverter[1].'-'. $data_inverter[0];
        $string = "$x $hora";
        return $string;
    }


    public static function dataHoraAvalEditar($dataHora){
        $data = substr($dataHora, 0, -9);
        $hora = substr($dataHora, 11);
        $hora = substr($hora, 0, -3);
    
        $timestamp  = strtotime($data);
        $dia        = date('d', $timestamp);
        $mes        = date('m', $timestamp);
        $ano        = date('Y', $timestamp);
        $string     = "$dia/$mes/$ano $hora";
        return $string;
    }

    public static function contaSegundos($tempo){
        $hora = explode(":", $tempo);
        $h = $hora[0] * 3600;
        $m = $hora[1] * 60;
        $s = $hora[2];
        $segundos = $h + $m + $s;
        return $segundos;
    }

    public static function timeStampData($tm){
        return date('d/m/Y', $tm /= 1000);
    }
    
    public static function timeStampDataHora($tm){
        return date('d/m/Y H:i', $tm /= 1000);
    }

    #========================================================================

    
    public static function dataMysql($data){
    	
    	//CONVERTE A DATA DO FORM EM PADRAO DO DB
    	
    	if(isset($data) && !empty($data)){
    		
    		$result = explode("/", $data);
    		return $result[2].'-'. $result[1].'-'. $result[0];
    		
    	}else{
    		return date('Y-m-d');
    	}
        
    }
    

    public static function dataHoraMysql($dataHora){
        $data = substr($dataHora, 0, -6);
        $hora = substr($dataHora, 11);

        $data = explode("/", $data);
            return $data[2].'-'. $data[1].'-'. $data[0] .' '. $hora;
      }

    public static function dataHoraBr($data_hora){
    	//CONVERTE DATA HORA US PARA DATA HORA PADRÃO PT-BR
    
    	$timestamp      = strtotime($data_hora);
    	$dia            = date('d', $timestamp);
    	$mes            = date('m', $timestamp);
    	$ano            = date('Y', $timestamp);
    
    	$horas          = date('H', $timestamp);
    	$minutos        = date('i', $timestamp);
    	$segundos       = date('s', $timestamp);
    
    	return $dia."/".$mes."/".$ano ." - ". $horas.":".$minutos.":".$segundos;
    
    }
    
    public static function dataBr($data){
    	//CONVERTE DATA HORA US PARA DATA PADRÃO PT-BR
    
    	$timestamp      = strtotime($data);
    	$dia            = date('d', $timestamp);
    	$mes            = date('m', $timestamp);
    	$ano            = date('Y', $timestamp);
    
    	return $dia."/".$mes."/".$ano;
    
    }
    
    public static function horaBr($data_hora){
    	//CONVERTE DATA HORA US PARA HORA PADRÃO PT-BR
    
    	$timestamp      = strtotime($data_hora);
    	$horas          = date('H', $timestamp);
    	$minutos        = date('i', $timestamp);
    	$segundos       = date('s', $timestamp);
    
    	return $horas.":".$minutos.":".$segundos;
    
    }
    
    public static function dataExtenso($data_hora = ''){
    
    	//CONVERTE DATA HORA US PARA DATA PADRÃO PT-BR POR EXTENSO
    
    	if(isset($data_hora) && !empty($data_hora)){
    		$timestamp = strtotime($data_hora);
    	}else{
    		$timestamp = time();
    	}
    
    	$data = getdate($timestamp);
    
    	$dia_semana = array("Domingo, ", "Segunda-feira, ", "Terça-feira, ", "Quarta-feira, ", "Quinta-feira, ", "Sexta-feira, ", "Sábado, ");
    	$meses 		= array("", "Janeiro", "Fevereiro ", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    
    
    	return $dia_semana[$data["wday"]] ." ". $data["mday"]." de ".$meses[$data["mon"]]." de ".$data["year"];
    }
    


}