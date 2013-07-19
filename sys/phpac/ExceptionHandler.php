<?php

function formataExcecao($exception){
    // these are our templates
    $traceline = "#%s %s(%s): %s(%s)";
    $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s<br>Stack trace:<br>%s<br>  thrown in %s on line %s";

    // alter your trace as you please, here
    $trace = $exception->getTrace();
    foreach ($trace as $key => $stackPoint) {
        // I'm converting arguments to their type
        // (prevents passwords from ever getting logged as anything other than 'string')
        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
    }

    // build your tracelines
    $result = array();
    foreach ($trace as $key => $stackPoint) {
        $result[] = sprintf(
            $traceline,
            $key,
            $stackPoint['file'],
            $stackPoint['line'],
            $stackPoint['function'],
            implode(', ', $stackPoint['args'])
        );
    }
    // trace always ends with {main}
    $result[] = '#' . ++$key . ' {main}';

    // write tracelines into main template
    $msg = sprintf(
        $msg,
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        implode("<br>", $result),
        $exception->getFile(),
        $exception->getLine()
    );
    
    return $msg;
}

if ($producao) {
        
    if (!$debug){
        
        function exception_handler($exception) {
            
            //Envia e-mail para o suporte
            require_once($_SERVER['DOCUMENT_ROOT'].'/sys/phpac/Emailer.php'); 
            $excecao = formataExcecao($exception);
            $datetime = new DateTime();
            $horario = $datetime->format('d/m/Y H:i:s');
            $site = $_SERVER["SERVER_NAME"];
            $url = $site.$_SERVER['REQUEST_URI'];
            $session = print_r($_SESSION, true);
            $post = print_r($_POST, true);
            $get = print_r($_GET, true);
            $msg = "<strong>Horário:</strong> $horario<br><br><strong>Url:</strong> $url<br><br><strong>Erro:</strong><br>$excecao<br><br>".
                   "<strong>SESSION:</strong><br>$session<br><br><strong>GET:</strong><br>$get<br><br><strong>POST:</strong><br>$post";
            $assunto = "Erro no site $site";
            $emailer = new Emailer();
            $success = $emailer->send('Suporte', 'lucas.polonio@isitecnologia.com.br', 'lucas.polonio@isitecnologia.com.br', $assunto, $msg);

            //Direcionar para uma página de erro do sistema onde o usuário pode continuar navegando
            redirect('/admin-cms/erro/');

        }
        set_exception_handler('exception_handler');

    } else {
        
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set("display_errors", 1);

        // define uma função para tratar todas exceções que não foram pegas com catch()
        function exception_handler_debug($exception) {
            $msg = formataExcecao($exception);
            printDebug($msg);
        }
        
        set_exception_handler('exception_handler_debug');
    }
    


}
?>