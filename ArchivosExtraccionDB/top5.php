<?php
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

function Top5Empresas(){
    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->anuncios_dia;

    $filtro = [
        "tipo" => "Constitución"
    ];
    $options = ['projection' => ["nombre_comercial" => 1, "provincia" => 1]];

    $Result = $collection->find($filtro, $options)->toArray();

    $ArrayAgrupado = NULL;
    if($Result != NULL){
        foreach($Result as $res){
            $Provincia = $res["provincia"];
            $Nombre = $res["nombre_comercial"];
            $id = $res["_id"];
            if(isset($ArrayAgrupado[$Provincia])){
                if(count($ArrayAgrupado[$Provincia]["empresas"]) < 5){
                    $ArrayAgrupado[$Provincia]["empresas"][] = $Nombre;
                }
            }else{
                $ArrayAgrupado[$Provincia]["empresas"][] = $Nombre;
                $ArrayAgrupado[$Provincia]["provincia"] = $Provincia;
            }
        }
    }   

    if($ArrayAgrupado != NULL){
        foreach($ArrayAgrupado as $array){
            $Documents[] = $array;
        }

        $Result = $database->top5->deleteMany([]);
        $Result = $database->top5->insertMany($Documents);
        if($Result->isAcknowledged()){
            return 1;
        }else{
            return 0;
        }
    }else{
        $Result = $database->top5->deleteMany([]);
        if($Result->isAcknowledged()){
            return 2;
        }else{
            return 3;
        }
    }

}

?>