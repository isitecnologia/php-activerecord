<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/sys/phpac/include.php');
    
    $time_script_start = microtime(true);
?>

Profiler (300 queries):
<br>
<br>
<?
    $time_max = 0;
    for ($i = 0; $i < 100; $i++){
        $time_start = microtime(true);
        $conteudo_visita = Conteudo::find_conteudo_by_url("/visitas$i");
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if ($time > $time_max)
            $time_max = $time;
    }
?>
<br>Tempo de query por conteúdo via URL: média <?=number_format($time,5)?> (max: <?=number_format($time_max,5)?>)
<?
    $time_max = 0;
    for ($i = 0; $i < 100; $i++){
        $time_start = microtime(true);
        $conteudo_destaque_home_5 = Conteudo::find_conteudo_by_destaque(6);
        $imagem_destaque_home_1 = Arquivo::find($conteudo_destaque_home_1->imagem_destaque);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if ($time > $time_max)
            $time_max = $time;
    }
?>
<br>Tempo de query por conteúdo via ID e arquivo associado: média <?=number_format($time,5)?> (max: <?=number_format($time_max,5)?>)

<br>
<br>
<?
    $time_script_end = microtime(true);
    $time = ($time_script_end - $time_script_start);
?>
Tempo de execução total do script: <?=number_format($time,5)?>