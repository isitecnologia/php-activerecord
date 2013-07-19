<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/sys/phpac/ActiveRecord.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/sys/phpac/Basic.php'); 


//inicializa o ActiveRecord
ActiveRecord\Config::initialize(function($cfg)
{
    $cfg->set_model_directory($_SERVER['DOCUMENT_ROOT'].'/sys/cms/model/');
    //$cfg->set_connections(array('development' => 'mysql://portal:!hcb123#@localhost/portal')); //producao
    $cfg->set_connections(array('development' => 'mysql://hcb:jU2d6A1@nBz@localhost/hcb'));
});


//sempre inicializa a sessão
session_start();
session_name('cms');


//configura e inclui o handler de exceções
$producao = false;
$debug = false;
require_once($_SERVER['DOCUMENT_ROOT'].'/sys/phpac/ExceptionHandler.php'); 

?>