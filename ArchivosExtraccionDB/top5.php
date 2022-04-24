<?php
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

//Top5Empresas();
function Top5Empresas(){
    $conexion = new Conexion();
    $database = $conexion->Conectar();
    $collection = $database->anuncios_dia;

    date_default_timezone_set("UTC");

    $fecha_Millis = getdate();
    $Fecha = date("Y-m-d", $fecha_Millis[0]);
    //$Fecha = "2009-02-05";
    $FechaInsert = new MongoDB\BSON\UTCDatetime(strtotime($Fecha . " 00:00:00")*1000);
    $filtro = [
        "tipo" => "Constitución",
        "fecha" => ['$eq' => $FechaInsert]
    ];
    $options = ['projection' => ["nombre_comercial" => 1, "provincia" => 1]];

    $Result = $collection->find($filtro, $options)->toArray();

    $ArrayAgrupado = NULL;
    if($Result != NULL){
        foreach($Result as $res){
            $Provincia = $res["provincia"];
            $Nombre = $res["nombre_comercial"];
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
    

    /*foreach($ArrayAgrupado as $Provincia=>$Empresas){
        echo $Provincia . "<br>";
        $contador = 1;
        foreach($Empresas as $empresa){
            echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . $contador . ") " . $empresa . "<br>";
            $contador++;
        }
        echo "------------------------------------<br><br><br>";
    }*/
    //echo "si";
}

?>