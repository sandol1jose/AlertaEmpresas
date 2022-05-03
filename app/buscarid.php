<?php
    session_start();
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    if(!isset($_POST["buscadorEmpresa"])){
        header('Location: ../');
    }

    $IdNombreComercial = Id_DeNombre($_POST["buscadorEmpresa"]);

    echo $buscador;

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $Result = $database->anuncios->findOne(["id_nombre_comercial" => $IdNombreComercial]);

    if($Result != NULL){
        $id = $Result["_id"];
        header('Location: ../MostrarEmpresa?id=' . $id);
    }else{
        header('Location: ../');
    }

    function Id_DeNombre($Nombre){
        $IdNombre = str_replace(".", "", $Nombre);
        $IdNombre = str_replace(",", "", $IdNombre);
        $IdNombre = str_replace(" ", "", $IdNombre);
        return $IdNombre;
    }
    
?>