<?php

/*
 * Populate $to_object with the desired $fields from $from_assoc_array, passing all values through $filter_function.
 * @arg $from_assoc_array associate array
 * @arg $to_object object
 * @arg $fields array
 * @arg $filter_function function name (string)
 */
function populateObjectFromAssocArray($from_assoc_array, $to_object, $fields = NULL, $filter_function = NULL){
    
    foreach ($from_assoc_array as $field => $value){
        
        if ($fields == NULL || in_array($field, $fields)){
            if ($filter_function)
                $to_object->{$field} = call_user_func($filter_function, $value);
            else
                $to_object->{$field} = $value;
        }
        
    }
    
}

/*
 * Monta parametros tipo par=1&outro=2 a partir de array associativo
 * 
 * 
 */

function buildGETStringFromAssocArray($assoc_array, $n_array = array()){
    
    if (!$assoc_array) return false;
    
    $string = "";
    foreach ($assoc_array as $key => $value){
        if (!in_array($key, $n_array))
            $string .= "$key=$value&";
    }
    return $string;
}



function redirect($location){
    // Permanent redirection (Akamai cacheia 302 e não cacheia 301)
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $location");
    die();
}



/**
 * Funcao que debuga um valor na tela
 * @param Object $valor valor a ser mostrado na tela
 *
 */
function printDebug($valor, $die=true)
{
    echo '<pre>';
    print_r($valor);
    echo '</pre>';
    if ($die)
        die();
}



/**
 * Funcao limpaString remove formatações e injections de uma string
 * @param string $string string para remoção de formatações
 *
 * @return string 
 */
function limpaString($string){
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    //$string = str_replace("'", "''", $string);

    return $string;
}


function limpaAssocArray(&$array){
    foreach($array as $key => &$value){
        if (is_array($value)){
            $value = limpaAssocArray($value);
        } else {
            $value = limpaString($value);
        }
    }
    return $array;
}

/**
 * Funcao arrayParaStringComSeparador recebe um array e retorna uma string com os elementos do array separados por um caractere
 * @param Array $array array com valores
 * @param string $separador string ou caractere que servira como separador entre os elementos
 * @param string $retorno_vazio string que será retornada no caso do array estar vazio
 *
 * @return string $string_formatada;
 */
function arrayParaStringComSeparador($array, $separador, $retorno_vazio = ""){
	if(sizeof($array) > 0){	
		$string_formatada = "";
		foreach($array as $chave => $valor){
			if($chave == sizeof($array)-1) $string_formatada .= "$valor";
			else $string_formatada .= "$valor$separador ";
		}
		
		return $string_formatada;
	}
	else{
		return $retorno_vazio;
	}
}



/**
 * Funcao que seta uma flashmsg na session
 * @param string $name nome da msg que ficara na session
 * @param string $msg conteudo da msg
 *
 */
function setFlashMsg($name,$msg)
{
    $_SESSION[$name] = $msg;
    
}


/**
 * Funcao que pega uma flashmsg da session e unseta ela
 * @param string $tabela nome da tabela
 *
 * @return string 
 */
function getFlashMsg($name)
{
    $msg = '';
    if (isset($_SESSION[$name]))
    {
        $msg = $_SESSION[$name];
        unset ($_SESSION[$name]);
    }
    return $msg;
}



 /**
 * Funcao resumeString resume $string para até o maximo de $chars caracteres, sem cortar palavras.
 * Adiciona reticencias caso seja maior.
 * 
 * @param string $string string a ser resumida
 * @param string $char numero maximo de caracteres
 * 
 * @return string resumida 
 */
function resumeString($string, $chars) {
    
      if (strlen($string) > $chars) {
        while (substr($string,$chars,1) <> ' ' && ($chars < strlen($string))){
          $chars++;
        }
        return substr($string,0,$chars).'...';
      }
      return substr($string,0,$chars);
      
};



/* 
 * Procura se uma chave existe em um array multidimensional. se encontra retorna o caminho separado por ":"
 */
function multi_array_key_exists($needle, $haystack) {
      foreach ($haystack as $key=>$value) {
        if ($needle==$key) {
          return $key;
        }
        if (is_array($value)) {
          if(multi_array_key_exists($needle, $value)) {
            return $key . ":" . multi_array_key_exists($needle, $value);
          }
        }
      }
  return false;
}


/*
 * Recebe um array multidimensional e retorna um array com todas as keys do array passado
 * 
 */
function multiarray_keys($ar) { 
            
    foreach($ar as $k => $v) { 
        $keys[] = $k; 
        if (is_array($ar[$k])) 
            $keys = array_merge($keys, multiarray_keys($ar[$k])); 
    } 
    return $keys; 
}

 /**
 * Funcao decodificaString decodifica $string do banco para remover possíveis erros de caracteres
 * 
 * @param string $string string a ser resumida
 * 
 * @return string decodificada 
 */

function decodificaString($string){
    
    return utf8_encode($string);
}



 /**
 * Funcao removeAcentos remove acentos de uma string
 * @param string $string string de onde os caracteres especiais deverao ser removidos
 * @param string $slug caracter separador de palavras
 * 
 * @return boolean 
 */

/* Função do ambiente da ISI
 function removeAcentos($string, $slug = false){

        $string = strtolower($string);

	$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ';
	$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyybyRr';
	$string = utf8_decode($string);
	$string = strtr($string, utf8_decode($a), $b);
	$string = str_replace(" ",$slug,$string);
        $string = utf8_encode($string);

	// Slug?
	if ($slug) {
		// Troca tudo que não for letra ou número por um caractere ($slug)
		$string = preg_replace('/[^a-z0-9]/i', $slug, $string);
		// Tira os caracteres ($slug) repetidos
		$string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
		$string = trim($string, $slug);
	}

	return $string;
}
 * 
 */

function removeAcentos($string, $slug = false){
 	
    $array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" 
, "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" ); 
	$array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" 
, "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" ); 
    $string = str_replace( $array1, $array2, $string); 
    
    if ($slug) {
		// Troca tudo que não for letra ou número por um caractere ($slug)
		$string = preg_replace('/[^a-z0-9]/i', $slug, $string);
		// Tira os caracteres ($slug) repetidos
		$string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
		$string = trim($string, $slug);
	}
	
	$string = strtolower($string);

	return $string;
}

/**
 * Funcao removeAcentos remove acentos de uma string
 * @param string $string string de onde os caracteres especiais deverao ser removidos
 * @param string $slug caracter separador de palavras
 * 
 * @return boolean 
 */

function genarateUrlFromTitle($title){
    
    $title = removeAcentos($title, "-");
    $title = "/".$title;
    
    return $title;
}

/**
 * Funcao formataData recebe uma data no formato aaaa-mm-dd e retorna uma data
 * no formato dd/mm/yyyy
 * @param string $data data no formato aaaa-mm-dd
 *
 * @return string $data_formatada data no formato dd/mm/yyyy
 */
function formataData($data){
    if ($data && $data != ""){
        $vdatahora = explode(" ", $data);
        $vdatasemformato = $vdatahora[0];
        $vhora = $vdatahora[1];

        $vdata = explode("-", $vdatasemformato);
        $data_formatada = $vdata[2] ."/". $vdata[1] ."/". $vdata[0];
        return $data_formatada;
    }
    return $data;
}

/**
 * Funcao formataDataBanco recebe uma data no formato dd/mm/aaaa e retorna uma data
 * no formato aaaa-mm-dd
 * @param string $data data no formato dd/mm/aaaa
 *
 * @return string
 */
function formataDataBanco($data){
        if ($data == '')
            return $data;
	$vdata = explode("/", $data);
	$data_formatada = $vdata[2] ."-". $vdata[1] ."-". $vdata[0];
	return $data_formatada;
}

function limpaCacheAkamai($uri){
    
    $client = new SoapClient("file://".$_SERVER['DOCUMENT_ROOT']."/sys/akamai/Akamai-CCU-soapui-project.xml");

    $requestParams = array(
        'name' => 'suporte@isitecnologia.com.br',
        'pwd' => 'ISI@1s1m0b0711',
        'network' => 'production',
        'opt' => 'purge',
        'uri' => $uri
    );
    
    try{
        $response = $client->__soapCall('purgeRequest', $requestParams);
        var_dump($response);
    }catch(Exception $e) {
        echo $e->getMessage();
    }
    
}

define("AKAMAI_USER","suporte@isitecnologia.com.br");
define("AKAMAI_PASSWORD","ISI@1s1m0b0711");
// end of suggested constants

function pap_akamai_purge($url, $email = null){ // 'pap' stands for Php Akamai Purge

    $client = new SoapClient('https://ccuapi.akamai.com/ccuapi-axis.wsdl'); // xml schema Akamai uses for purging

    $options = array('action=invalidate','domain=staging','type=arl'); // email notification is optional. Thanks @dustinhood for the array options.
    
    if ($email)
        $options[] = 'email-notification='.$email;

    try {

        $purgeResult = $client->purgeRequest(AKAMAI_USER, AKAMAI_PASSWORD, '', $options, array($url));
        // once you have $purgeResult, you can handle the results any way you'd prefer.

        // the following lines are just a suggestion
        if ($purgeResult->resultCode==100) { // 100 = success
            return "Purge Success for: $url</br>";
        } else {
            return "Something went wrong. Akamai purge request failed: $purgeResult->resultCode</br>";
        }
        // end of suggestion

    } catch(SoapFault $e) {

        return "Something went wrong. Akamai purge request failed: $e</br>";

    }

}

?>