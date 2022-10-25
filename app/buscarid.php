<?php
    session_start();
    $root = str_replace('\\', '/', dirname(__DIR__));
    require_once($root . '/Archivos de Ayuda PHP/conexion.php');

    if(!isset($_POST["buscadorEmpresa"])){
        if(!isset($_GET["buscadorEmpresa"])){
            header('Location: ../');
        }else{
            $Busqueda = urldecode($_GET["buscadorEmpresa"]);
        }
    }else{
        $Busqueda = $_POST["buscadorEmpresa"];
    }

    $IdNombreComercial = Id_DeNombre($Busqueda);

    echo $buscador;

    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $Result = $database->anuncios->findOne(["id_nombre_comercial" => $IdNombreComercial, 'eliminado' => ['$ne' => 1]]);

    if($Result != NULL){
        $id = $Result["_id"];
        header('Location: ../MostrarEmpresa?id=' . $id);
    }else{
        $_SESSION["buscador"] = $IdNombreComercial;
        header('Location: ../ListarResultados.php');
    }

    function Id_DeNombre($Nombre){
        $IdNombre = str_replace(".", "", $Nombre);
        $IdNombre = str_replace(",", "", $IdNombre);
        $IdNombre = str_replace(" ", "", $IdNombre);
        return $IdNombre;
    }
    
?>