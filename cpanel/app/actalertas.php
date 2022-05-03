<?php
    session_start();
    $root = str_replace('\\', '/', dirname(__DIR__));
    $root = str_replace('/cpanel', '', $root);
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    if(!isset($_POST['numalertas'])){
        header('Location: ../alertas.php');
    }

    $NumAlertas = $_POST['numalertas'];

    if($NumAlertas > 0){
        $conexion = new Conexion();
        $database = $conexion->Conectar();
        $collection = $database->config;
    
        $filter = ["tipo" => "Alertas"];//Buscando la configuracion de tipo Alertas
        $document = ['$set' => ["cantidad" => $NumAlertas]];
        $options = ['upsert' => true];
        $Result = $collection->updateOne($filter, $document, $options);
    }
    
    $_SESSION["Actualizacion_Exito"] = 1;
    header('Location: ../alertas.php');
?>