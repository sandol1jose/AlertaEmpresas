<?php
session_start();//inicio de sesion
$root = str_replace('\\', '/', dirname(__DIR__));
$root = str_replace('/cpanel', '', $root);
require_once($root . '/Archivos de Ayuda PHP/conexion.php');

$idEmpresa = $_POST["idEmpresa"];
$conexion = new Conexion();
$database = $conexion->Conectar();

$filter = ["_id" => new MongoDB\BSON\ObjectID($idEmpresa)];
$options["projection"] = [
    'anuncio_borme' => 1,
    '_id' => 0
];
$collection = $database->empresas;
$Result = $collection->findOne($filter, $options);

//Buscamos todos los anuncios de la empresa
$Result = iterator_to_array($Result["anuncio_borme"]);

foreach($Result as $anuncio){
    if(!is_string($anuncio)){//Si el array tiene 2 o mas valores
        $anuncio = iterator_to_array($anuncio);
        $anuncio = $anuncio["numid"];
    }
    
    //Agregamos el campo "eliminado" a todos los anuncios de la empresa
    $filter = ["numid" => $anuncio];
    $Update = ['$set' => ["eliminado" => 1]];

    $options = [
        'upsert' => true,
        'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER
    ];
    $Result = $database->anuncios->findOneAndUpdate($filter, $Update,  $options);
}



if(isset($Result["eliminado"])){
    //Ahora buscamos si hay registro en "cambios_nombres" y le colocamos "eliminado"
    $id_nombre_empresa = $Result["id_nombre_comercial"];
    $filter = ["id_otros_nombres" => $id_nombre_empresa];
    $Result = $database->cambios_nombres->updateOne($filter, $Update);
}

echo "1";
?>