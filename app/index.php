<?php
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/app/conf.php');
    header('Location: '. $Redirigir);
?>