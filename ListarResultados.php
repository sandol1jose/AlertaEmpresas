<?php

require 'Archivos de Ayuda PHP/conexion.php';


$NombreEmpresa = $_POST["buscador"];

$filter = [ '$text' => [ '$search' => "\"$NombreEmpresa\"" ] ];
$options = ["nombre_comercial" => 1];


$conexion = new Conexion();
$database = $conexion->Conectar();
$collection = $database->empresas;
$Result = $collection->find($filter, $options)->toArray();

echo count($Result) . " resultados...<br><br>";
foreach($Result as $res){
    $id = strval($res["_id"]);
    $nombre_comercial = strval($res["nombre_comercial"]);
    echo $id . "<br>";
    echo "<a href='MostrarEmpresa.php?id=".$id."'>" . $nombre_comercial . "</a>" . "<br><br>";
}


?>