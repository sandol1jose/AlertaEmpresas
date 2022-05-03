<?php

    if(!isset($_POST['NombrePagina'])){
        header('Location: ../agregarpaginas.php');
    }

    $PaginaAEliminar = $_POST["NombrePagina"];

    if(stristr(strtoupper($PaginaAEliminar), ".php") === false){//Si no tiene la extension
        $PaginaAEliminar .= ".php";
    }

    unlink('../../footer/' . $PaginaAEliminar);//Eliminamos el archivo
    if(!file_exists("../../footer/" . $PaginaAEliminar)){

        $root = str_replace('\\', '/', dirname(__DIR__));
        $root = str_replace('/cpanel', '', $root);
        require_once($root . '/Archivos de Ayuda PHP/conexion.php');

        $PaginaSola = str_replace(".php", "", $PaginaAEliminar);
    
        $conexion = new Conexion();
        $database = $conexion->Conectar();
        $collection = $database->config;
        $filter = ['tipo' => 'titulo paginas'];
        $document = [ '$unset' => [
                'footer.'.$PaginaSola => ""
            ]
        ];
        $Result = $collection->updateOne($filter, $document);
        if($Result->getModifiedCount() == 1){
            echo "1";
        }else{
            fopen("../../footer/" . $PaginaAEliminar, "a+"); //Creamos el archivo de nuevo
            echo "0"; //No se pudo eliminar
        }
    }else{
        echo "0"; //No se pudo eliminar
    }
?>