<?php
$root = str_replace('\\', '/', dirname(__DIR__));
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

$idEmpresa = $_POST["idEmpresa"];
$Correo = $_POST["Correo"];

$conexion = new Conexion();
$database = $conexion->Conectar();


//$session = $database.MongoDB\Driver\Manager::startSession();
//$session = $database->startSession();//Iniciamos la sesion
//$session->startTransaction();//Iniciamos la transaccion
//$session->commitTransaction();
//$session->abortTransaction();
//insertOne(['abc' => 1], ['session' => $session])


//Eliminando del Cliente
$filter = ["email" => $Correo];
$collection_Clientes = $database->Clientes;
$Result_Cliente = $collection_Clientes->findOne($filter/*, ['session' => $session]*/);

if($Result_Cliente  != NULL){
    if(isset($Result_Cliente["alertas"])){

        $Alertas = iterator_to_array($Result_Cliente["alertas"]);

        $Array_Alertas_nuevo = NULL;
        foreach($Alertas as $Alerta){
            if($Alerta["id_empresa"] != $idEmpresa){
                $Array_Alertas_nuevo[] = $Alerta;
            }
        }

        if($Array_Alertas_nuevo != NULL){
            $document = [
                '$set' => [
                    "alertas" => $Array_Alertas_nuevo
                ]
            ];
        }else{
            $document = [
                '$unset' => [
                    "alertas" => ""
                ]
            ];
        }

        $Result_Cliente = $collection_Clientes->updateOne($filter, $document/*, ['session' => $session]*/);

    }
}



//Eliminando de la Empresa
$collection_Empresas = $database->empresas;
$filter = ["_id" => new MongoDB\BSON\ObjectID($idEmpresa)];
$Result_Empresas = $collection_Empresas->findOne($filter/*, ['session' => $session]*/);

if($Result_Empresas  != NULL){
    if(isset($Result_Empresas["alertas"])){
        $Alertas = iterator_to_array($Result_Empresas["alertas"]);

        $Array_Alertas_nuevo = NULL;
        foreach($Alertas as $Alerta){
            if($Alerta["correo_cliente"] != $Correo){
                $Array_Alertas_nuevo[] = $Alerta;
            }
        }

        if($Array_Alertas_nuevo != NULL){
            $document = [
                '$set' => [
                    "alertas" => $Array_Alertas_nuevo
                ]
            ];
        }else{
            $document = [
                '$unset' => [
                    "alertas" => ""
                ]
            ];
        }

        $Result_Empresas = $collection_Empresas->updateOne($filter, $document/*, ['session' => $session]*/);
    }
}


if($Result_Empresas != NULL || $Result_Cliente != NULL){
    if($Result_Empresas->getModifiedCount() == 1 || $Result_Cliente->getModifiedCount() == 1){
        echo 1;//exito
    }else{
        echo 0;//No se pudo
    }
}else{
    echo 1;//exito
}

//$session->commitTransaction();
?>