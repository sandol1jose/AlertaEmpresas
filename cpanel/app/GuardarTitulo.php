<?php
    $root = str_replace('\\', '/', dirname(__DIR__));
    $root = str_replace('/cpanel', '', $root);
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    if(!isset($_POST['Carpeta']) || !isset($_POST['Pagina']) || !isset($_POST['Titulo'])){
        header('Location: ../titulopaginas.php');
    }

    $Carpeta = $_POST["Carpeta"];
    $Pagina = $_POST["Pagina"];
    $Titulo = $_POST["Titulo"];

    if($Titulo != ""){
        $conexion = new Conexion();
        $database = $conexion->Conectar();
        $collection = $database->config;
    
        $filter = ["tipo" => "titulo paginas"];//Buscando la configuracion de tipo Alertas
        $document = ['$set' => [$Carpeta . "." . $Pagina  => $Titulo]];
        $options = ['upsert' => true];
        $Result = $collection->updateOne($filter, $document, $options);

        echo "1";
    }else{
        echo "0";
    }
?>